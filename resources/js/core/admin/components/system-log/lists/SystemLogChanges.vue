<template>
  <div class="pa-2">
    <v-expansion-panels
      v-for="(propertyGroupValue, propertyGroupName, index) in propertiesInAlphabeticalOrder"
      :value="isExpansionPanelComponentOpen"
      :key="index"
      :light="isLight"
      class="mb-2" 
      accordion 
      focusable
    >
      <v-expansion-panel>
        <v-expansion-panel-header>
          {{ getPropertyGroupLabel(propertyGroupName) | upperCase }}
        </v-expansion-panel-header>
        <v-expansion-panel-content>
          <v-row
            class="mt-2" 
            dense
          >
            <v-col
              v-for="(propertyValue, propertyName, index) in propertyGroupValue"
              :key="index"
              cols="12"
            >
              <div class="d-flex">
                <div class="mr-1">
                  {{ `${propertyName}: ` | capitalize }}
                </div>
                <div style="word-break: break-word;">
                  <template v-if="Array.isArray(propertyValue)">
                    <template v-for="(propertyArrayValue, index) in propertyValue">
                      <template v-if="isMobile">
                          <span :key="index">
                            {{ `${propertyArrayValue} ${index !== (propertyValue.length - 1) ? ',' : ''} ` }}
                          </span>
                      </template>
                      <v-chip
                        v-else
                        :key="index"
                        class="ma-1"
                        x-small
                      >
                        {{ propertyArrayValue }}
                      </v-chip>
                    </template>
                  </template>
                  <template v-else>
                    {{ propertyValue }}
                  </template>
                </div>
              </div>
            </v-col>
          </v-row>
        </v-expansion-panel-content>
      </v-expansion-panel>
    </v-expansion-panels>
  </div>
</template>

<script>
  const PROPERTIES = {
    attributes: { label: 'New'},
    old: { label: 'Old'},
  };

  const PROPERTIES_REQUIRED = Object.keys(PROPERTIES);

  export default {
    name: 'SystemLogChanges',
    props: {
      /**
       * The changes properties data
       */
      propertiesData: {
        type: Object,
        required: true,
        validator: function(value){
          for(const property in value){
            if(value.hasOwnProperty(property)){
              return PROPERTIES_REQUIRED.indexOf(property) !== -1;
            }
          }
        },
      },
      /**
       * Set if all panels are open initally
       */
      isExpansionPanelsOpen: {
        type: Boolean,
        default: false,
      },
      /**
       * Set if expansion panel background is light
       */
      isLight: {
        type: Boolean,
        default: true,
      },
    },
    computed: {
      isMobile: function(){
        return this.$vuetify.breakpoint.xsOnly;
      },
      propertiesInAlphabeticalOrder: function(){
        return this.sortObjectByKey(this.propertiesData);
      },
      isExpansionPanelComponentOpen: function(){
        return this.isExpansionPanelsOpen ? 0 : -1; // If true then set open the first panel which is zero index 
      },
    },
    methods: {
      /**
       * Get group label based on given group name
       */
      getPropertyGroupLabel(groupName){
        return PROPERTIES[groupName].label;
      }
    }
  }
</script>

<style scoped>
</style>
