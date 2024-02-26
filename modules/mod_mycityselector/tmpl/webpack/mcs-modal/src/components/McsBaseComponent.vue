<template>
  <div class="mcs-module" @keyup.esc="closeModal" tabindex="0">
      <div class="mcs-module-inner">
          <span v-if="textBefore"> {{ textBefore }} </span>
          <a class="current-location" href="#" :title="$t('Выбрать другой город')" @click.prevent="openModal">{{ currentLocationName || 'Unknown' }}</a>
          <span v-if="textAfter"> {{ textAfter }} </span>

          <mcs-modal-component
                  :locations="locationsArray"
                  :current-city-code="currentCityCode"
                  :current-province-code="currentProvinceCode"
                  :current-country-code="currentCountryCode"
                  :modal-header-title="modalHeaderTitle"
                  :modal-header-search-placeholder="modalHeaderSearchPlaceholder"
                  :allow-select-whole="allowSelectWhole"
          >
          </mcs-modal-component>

          <mcs-question-popup-component
                  :question-tooltip-question="questionTooltipQuestion"
                  :question-tooltip-yes="questionTooltipYes"
                  :question-tooltip-no="questionTooltipNo"
                  :locations="locationsArray"
                  :current-location-name="currentLocationName"
          >
          </mcs-question-popup-component>
      </div>
  </div>
</template>

<script>
import McsModalComponent from './McsModalComponent.vue';
import McsQuestionPopupComponent from './McsQuestionPopupComponent.vue';

export default {
    components: {
        McsModalComponent,
        McsQuestionPopupComponent
    },
    props: {
        locations: String,
        currentCityCode: String,
        currentProvinceCode: String,
        currentCountryCode: String,
        currentLocationName: String,
        questionTooltipQuestion: String,
        questionTooltipYes: String,
        questionTooltipNo: String,
        modalHeaderTitle: String,
        modalHeaderSearchPlaceholder: String,
        textBefore: String,
        textAfter: String,
        allowSelectWhole: String,
    },
    computed: {
        locationsArray: function() {
            return JSON.parse(this.locations);
        }
    },
    methods: {
        openModal: function () {
            this.$root.$emit('switchModalDisplay', true);
        },
        closeModal: function () {
            this.$root.$emit('switchModalDisplay', false);
        }
    }

}
</script>

<style lang="scss">
    .mcs-module {
        outline: none;

        .current-location {
            color: #046;
            font-size: 14px;
            text-decoration: none;
            border-bottom: 1px dashed #046;
        }
    }
</style>
