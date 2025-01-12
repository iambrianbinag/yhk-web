<template>
  <v-row justify="center">
    <v-dialog
      v-model="visible"
      persistent
      max-width="300px"
    >
      <form-wrapper :validator="$v.form">
        <v-form @submit.prevent="handleFormSubmit">
          <v-card>
            <v-toolbar
              color="primary"
              dark
              flat
              dense
            >
              <v-toolbar-title>{{ title }}</v-toolbar-title>
            </v-toolbar>
            <v-card-text>
              <v-row dense>
                <v-col
                  cols="12"
                >
                  <form-group name="name">
                    <v-text-field
                      slot-scope="{ attrs }"
                      v-bind="attrs"
                      v-model="form.name"
                      label="Name *"
                      hide-details="auto"
                      outlined
                      dense
                    />
                  </form-group>
                </v-col>
                <v-col
                  cols="12"
                >
                  <form-group name="order">
                    <v-text-field
                      slot-scope="{ attrs }"
                      v-bind="attrs"
                      v-model="form.order"
                      label="Order"
                      hide-details="auto"
                      outlined
                      dense
                    />
                  </form-group>
                </v-col>
              </v-row>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                color='secondary'
                @click="closeDialog"
                small
              >
                  Cancel
              </v-btn>
              <v-btn
                :loading="isLoadingCreatePermission || isLoadingUpdatePermission"
                color='primary'
                type='submit'
                small
              >
                  Save
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-form>
      </form-wrapper>
    </v-dialog>
  </v-row>
</template>

<script>
  import { mapGetters, mapActions } from 'vuex'; 
  import { required, decimal } from 'vuelidate/lib/validators';

  export default {
    name: 'PermissionFormDialog',
    props: {
      /**
       * The permission details
       */
      permission: {
        type: Object,
        default: null,
      },
      /**
       * Visible status of dialog
       */
      visible: {
        type: Boolean,
        default: false,
      },
      /**
       * Get called when action is successful
       */
      successCallback: {
        type: Function,
        default: () => {},
      }
    },
    data(){
      return {
        form: {
          id: null,
          name: '',
          order: null,
        }
      }
    },
    computed: {
      ...mapGetters('admin.permissions', [
        'isLoadingCreatePermission',
        'isLoadingUpdatePermission'
      ]),
      title: function(){
        return this.isUpdateAction ? 'Update permission' : 'Add new permission';
      },
      isUpdateAction: function(){
        return this.form.id ? true : false;
      },
    },
    validations: {
      form: {
        name: { 
          required 
        },
        order: {
          decimal,
        },
      }
    },
    methods: {
      ...mapActions('base.system', [
        'showSnackbar'
      ]),
      ...mapActions('admin.permissions', [
        'createPermission',
        'updatePermission',
      ]),
      /**
       * Close dialog
       */
      closeDialog(){
        this.$emit('update:visible', false);
      },
      /**
       * Set permission details in form data
       */
      setPermissionPropToForm(){
        if(this.permission){
          this.form = { ...this.permission };
        }
      },
      /**
       * Set form to empty
       */
      resetForm(){
        this.form = {
          id: null,
          name: '',
        }
      },
      /**
       *  Triggered when form is submitted
       * 
       * @event click
       * @type {event}
       */
      handleFormSubmit(){
        this.$v.$touch();
        if(this.$v.$invalid){
          return;
        }

        const params = {...this.form};

        if(this.isUpdateAction){
          this.updatePermission(params)
            .then((response) => {
              this.showSnackbar({
                message: 'Permission updated successfully'
              });
              this.$v.$reset();
              this.successCallback();
              this.closeDialog();
            });
        } else {
          delete params.id;
          this.createPermission(params)
            .then((response) => {
              this.showSnackbar({
                message: 'Permission created successfully'
              });
              this.$v.$reset();
              this.resetForm();
              this.successCallback();
              this.closeDialog();
            });
        }
      },
    },
    mounted(){
      this.setPermissionPropToForm();
    }
  }
</script>

<style scoped>
</style>