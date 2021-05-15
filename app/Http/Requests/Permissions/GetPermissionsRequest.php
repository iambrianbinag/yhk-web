<?php

namespace App\Http\Requests\Permissions;

use Illuminate\Foundation\Http\FormRequest;

class GetPermissionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'per_page' => ['numeric'],
            'order_by' => ['string', 'in:id,name,order'],
            'order_direction' => ['string', 'in:asc,desc'],
            'search' => ['string'],
            'not_paginated' => ['boolean'],
        ];
    }
}
