<template>
  <v-alert
    :value='hasErrors'
    type='error'
    dense
    outlined
    dismissible
  >
    <div v-for='(error, index) in activeErrorMessages' :key='index'>
      <div>{{ error }}</div>
    </div>
  </v-alert>
</template>
<script>
  import { multiErrorExtractorMixin } from 'vuelidate-error-extractor';
  export default {
    name: 'FormSummary',
    extends: multiErrorExtractorMixin,
    computed: {
      hasErrors(){
        return !!this.activeErrors.length;
      }
    }
  }
</script>