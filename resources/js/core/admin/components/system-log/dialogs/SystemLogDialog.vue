<template>
  <div>
    <template v-if="!isVisible">
      <v-tooltip top>
        <template #activator="{ on, attrs }">
          <v-btn
            @click="handleViewLog"
            v-bind="attrs"
            v-on="on"
            x-small
            icon
          >
            <v-icon>mdi-clock</v-icon>
          </v-btn>
        </template>
        <span>View log</span>
      </v-tooltip>
    </template>
    <template v-else>
      <v-row justify="center">
        <v-dialog
          :value="true"
          persistent
          max-width="800px"
        >
          <v-card>
            <v-toolbar
              color="primary"
              dark
              flat
              dense
            >
              <v-toolbar-title>{{ title }}</v-toolbar-title>
              <v-spacer></v-spacer>
              <v-btn
                icon
                dark
                small
                @click="handleCloseDialog"
              >
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-toolbar>
            <SystemLogTable
              :systemLogData="systemLogData"
              :isFilteredBySystemLogName="true" 
            />
          </v-card>
        </v-dialog>
      </v-row>
    </template>
  </div>
</template>

<script>
  import SystemLogTable from '../tables/SystemLogTable';

  export default {
    name: 'LogViewDialog',
    components: { SystemLogTable },
    props: {
      title: {
        type: String,
        required: true
      },
      /**
       * The log data
       */
      systemLogData: {
        type: Object,
        default: function(){
          return {
            'log_name': null,
            'subject_id': null,
          };
        },
      },
    },
    data(){
      return {
        isVisible: false,
      }
    },
    methods: {
      /**
       * Triggered when view log button is clicked
       * 
       * @event click
       * @type {event}
       */
      handleViewLog(){
        this.isVisible = true;
      },
       /**
       * Close dialog
       * 
       * @event click
       * @type {event}
       */
      handleCloseDialog(){
        this.isVisible = false;
      },
    },
  }
</script>

<style scoped>
</style>