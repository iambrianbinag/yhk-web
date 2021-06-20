<?php

namespace App\Http\Controllers\Roles;

use App\Http\Controllers\Controller;
use App\Http\Requests\Roles\CreateRoleRequest;
use App\Http\Requests\Roles\GetRolesRequest;
use App\Http\Requests\Roles\GiveRolePermissionsRequest;
use App\Http\Requests\Roles\UpdateRoleRequest;
use App\Models\Roles\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{    
    /**
     * Get all roles
     *
     * @param GetRolesRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(GetRolesRequest $request)
    {
        $perPage = $request->per_page ?? config('settings.pagination.per_page');
        $orderBy = $request->order_by;
        $orderDirection = $request->order_direction ?? config('settings.pagination.order_direction');
        $search = $request->search;
        $notPaginated = $request->not_paginated;

        $roles = Role::select('id', 'name')
            ->when($search, function($query, $search){
                return $query->where(function($query) use ($search){
                    $query->where('id', 'like', "%$search%")
                        ->orWhere('name', 'like', "%$search%");
                });
            })
            ->when($orderBy, function($query, $orderBy) use ($orderDirection){
                return $query->orderBy($orderBy, $orderDirection);
            }, function($query) use ($orderDirection){
                return $query->orderBy('id', $orderDirection);
            })
            ->when($notPaginated, function($query){
                return $query->get();
            }, function($query) use ($perPage){
                return $query->paginate($perPage);
            });

        return response()->json($roles);
    }
    
    /**
     * Create a role
     *
     * @param CreateRoleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateRoleRequest $request)
    {
        $data = $request->only(['name']);
        $role = Role::create($data);

        $logData = ['attributes' => $data];
        $role->fillActivity($logData);
        $role->saveActivity('created');

        return response()->json([
            'id' => $role->id,
            'name' => $role->name
        ]);
    }
    
    /**
     * Show a role
     *
     * @param Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Role $role)
    {
        return response()->json([
            'id' => $role->id,
            'name' => $role->name
        ]);
    }
    
    /**
     * Update a role
     *
     * @param UpdateRoleRequest $request
     * @param Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $logData = [
            'attributes' => [], 
            'old' => [
                'name' => $role->name,
            ],
        ];

        $data = $request->only(['name']);
        $role->update($data);

        $logData['attributes'] = [
            'name' => $role->name,
        ];

        $role->fillActivity($logData);
        $role->saveActivity('updated');

        return response()->json([
            'id' => $role->id,
            'name' => $role->name
        ]);
    }
    
    /**
     * Delete a role
     *
     * @param Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json([
            'id' => $role->id,
            'name' => $role->name
        ]);
    }
    
    /**
     * Get role's attached permissions
     *
     * @param Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRolePermissions(Role $role)
    {
        $roleWithPermissions = $role->load([
            'permissions' => function($query){
                $query->select('id', 'name', 'order')
                    ->orderBy('order', 'desc');
            }
        ]);
        $roleWithPermissions->permissions->transform(function($permission){
            return $permission->makeHidden('pivot');
        });

        return response()->json([
            'id' => $roleWithPermissions->id,
            'name' => $roleWithPermissions->name,
            'permissions' => $roleWithPermissions->permissions
        ]);
    }
    
    /**
     * Give permissions to role
     *
     * @param GiveRolePermissionsRequest $request
     * @param Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function giveRolePermissions(GiveRolePermissionsRequest $request, Role $role)
    {
        $roleWithPermissions = $role->givePermissionTo($request->permission_ids);
        $roleWithPermissions->permissions->transform(function($permission){
           return $permission->only(['id', 'name']);
        });

        return response()->json([
            'id' => $roleWithPermissions->id,
            'name' => $roleWithPermissions->name,
            'permissions' => $roleWithPermissions->permissions
        ]);
    }
    
    /**
     * Sync permissions to role
     *
     * @param GiveRolePermissionsRequest $request
     * @param Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncRolePermissions(GiveRolePermissionsRequest $request, Role $role)
    {
        $logData = [
            'attributes' => [], 
            'old' => [
                'permissions' => $role->permissions->pluck('name')->all(),
            ],
        ];

        $roleWithPermissions = $role->syncPermissions($request->permission_ids);
        $roleWithPermissions->permissions->transform(function($permission){
            return $permission->only(['id', 'name']);
        });

        $logData['attributes'] = [
            'permissions' => $role->permissions->pluck('name')->all(),
        ];

        $role->fillActivity($logData);
        $role->saveActivity('updated');

        return response()->json([
            'id' => $roleWithPermissions->id,
            'name' => $roleWithPermissions->name,
            'permissions' => $roleWithPermissions->permissions
        ]);
    }
}
