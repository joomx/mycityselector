<template>
  <div v-show="showTooltip" class="mcs-question-popup">
      <div class="question-triangle-top"></div>
      <div class="question-triangle-bottom"></div>
      <div :class="{ right: tooltipRight}" class="question-popup">
          <p v-html="question"></p>
          <br>
          <a href="#" class="close" @click.prevent="closePopup()">
              <svg xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 21.9 21.9" enable-background="new 0 0 21.9 21.9">
                  <path d="M14.1,11.3c-0.2-0.2-0.2-0.5,0-0.7l7.5-7.5c0.2-0.2,0.3-0.5,0.3-0.7s-0.1-0.5-0.3-0.7l-1.4-1.4C20,0.1,19.7,0,19.5,0  c-0.3,0-0.5,0.1-0.7,0.3l-7.5,7.5c-0.2,0.2-0.5,0.2-0.7,0L3.1,0.3C2.9,0.1,2.6,0,2.4,0S1.9,0.1,1.7,0.3L0.3,1.7C0.1,1.9,0,2.2,0,2.4  s0.1,0.5,0.3,0.7l7.5,7.5c0.2,0.2,0.2,0.5,0,0.7l-7.5,7.5C0.1,19,0,19.3,0,19.5s0.1,0.5,0.3,0.7l1.4,1.4c0.2,0.2,0.5,0.3,0.7,0.3  s0.5-0.1,0.7-0.3l7.5-7.5c0.2-0.2,0.5-0.2,0.7,0l7.5,7.5c0.2,0.2,0.5,0.3,0.7,0.3s0.5-0.1,0.7-0.3l1.4-1.4c0.2-0.2,0.3-0.5,0.3-0.7  s-0.1-0.5-0.3-0.7L14.1,11.3z"/>
              </svg>
          </a>
          <div>
              <button id="mcs-button-yes" @click="closePopup(); assignDefinedLocation()">{{ questionTooltipYes }}</button>
              <button id="mcs-button-no" @click="closePopup(); openModal()">{{ questionTooltipNo }}</button>
          </div>
      </div>
  </div>
</template>

<script>
import geo from './../geolocation.js';

function vardump() {
    if (window.mcs_debug_mode && window.mcs_debug_mode == 1) {
        for (let key = 0; key < arguments.length; key++) window.console.log(arguments[key]);
    }
}

export default {
    data () {
        return {
            type: null,
            showTooltip: false,
            definedLocation: {},
            tooltipRight: false
        }
    },
    props: {
        questionTooltipQuestion: String,
        questionTooltipYes: String,
        questionTooltipNo: String,
        locations: Array,
        currentCityCode: String,
        currentProvinceCode: String,
        currentCountryCode: String,
        currentLocationName: String
    },
    computed: {
        question: function () {
            const location = this.definedLocation && this.definedLocation.name ? this.definedLocation.name : this.currentLocationName;
            return this.questionTooltipQuestion.replace('%s', '<b>' + location + '</b>');
        }
    },
    methods: {
        openModal: function() {
            this.$root.$emit('switchModalDisplay', true);
        },
        closePopup: function() {
            this.showTooltip = false;
            geo.setCookie('MCS_NOASK', 1);
        },
        toggleQuestion: function() {
            const location = this.definedLocation && this.definedLocation.name ? this.definedLocation.name : this.currentLocationName;
            if (location) {
                this.showTooltip = true;
                let w = window,
                    d = document,
                    e = d.documentElement,
                    g = d.getElementsByTagName('body')[0],
                    x = w.innerWidth || e.clientWidth || g.clientWidth,
                    windowWidth = x,
                    question = document.getElementsByClassName('mcs-question-popup')[0],
                    questionOffset = question.offsetLeft,
                    questionWidth = question.offsetWidth,
                    questionRightPosition = questionOffset + questionWidth,
                    centerWidthOfWindow = windowWidth / 2;
                if ((questionRightPosition > windowWidth) || (questionOffset > centerWidthOfWindow)) {
                    this.tooltipRight = true;
                }
            }
        },
        /**
         * Функция получает навание города, определенного через yandex geolocation и пытается переключить на этот город,
         * если он есть в списке. При условии что не открыто окно выбора города и пользователь еще не успел сделать выбор.
         * @param {string} location Название города
         */
        autoSwitchToDetectedCity: function() {
            if (document.getElementsByClassName('mcs-dialog')[0].style.display == 'none') {
                // ищем город в списке
                this.assignDefinedLocation();
            }
        },
        assignDefinedLocation: function() {
            if (typeof this.definedLocation !== 'undefined' && this.definedLocation !== null) {
                geo.selectLocation(this.definedLocation);

                if(this.definedLocation.name !== this.currentLocationName && this.definedLocation.link) {
                    window.location = this.definedLocation.link;
                }
            }
        }
    },
    mounted: function () {
        vardump('McsQuestionPopupComponent is mounted');
        const geoLocation = geo.geolocation();
        const noAsk = geo.getCookie('MCS_NOASK');
        vardump('MCS: mcs_let_select = ' + window.mcs_let_select);
        vardump('MCS: MCS_NOASK = ' + noAsk);
        if (noAsk != '1') {
            //если город определен
            if (geoLocation) {
                vardump('MCS: geoLocation NOT empty');
                geoLocation.then((result) => {
                    vardump('MCS: geoLocation result', result);
                    if (result.city || result.province || result.country) {
                        this.definedLocation = geo.defineLocation(result, this.locations);
                        vardump('this.definedLocation', this.definedLocation);
                        switch (window.mcs_let_select) {
                            case '1':
                                vardump('MCS: openModal');
                                this.openModal();
                                break;
                            case '2':
                                vardump('MCS: toggleQuestion');
                                this.toggleQuestion();
                                break;
                            case '3':
                                vardump('MCS: autoSwitchToDetectedCity');
                                this.autoSwitchToDetectedCity();
                                break;
                        }
                    } else {
                        vardump('MCS: geoLocation result does not contain required data');
                    }
                });
            } else {
                vardump('MCS: geoLocation IS empty');
                const result = {
                    city: this.currentCityCode,
                    province: this.currentProvinceCode,
                    country: this.currentCountryCode,
                };
                this.definedLocation = geo.defineLocation(result, this.locations);
                switch (window.mcs_let_select) {
                    case '1':
                        vardump('MCS: openModal');
                        this.openModal();
                        break;
                    case '2':
                        vardump('MCS: toggleQuestion');
                        this.toggleQuestion();
                        break;
                    case '3':
                        vardump('MCS: autoSwitchToDetectedCity');
                        this.autoSwitchToDetectedCity();
                        break;
                }
            }
        }
    }
}
</script>

<style lang="scss">
    .mcs-module {
        .question-popup {
            box-shadow: 0 0 0 0 rgba(0,0,0,0.4);
            border-radius: 5px;
            position: absolute;
            top: calc(100% + 15px);
            left: -15px;
            text-align: left;
            white-space: nowrap;
            background-color: #FFF;
            z-index: 100;
            padding: 15px 40px 15px 20px;
            border: 1px solid #ddd;
            color: #333;

            &.right {
                left: auto;
                right: -15px;
            }

            p {
                margin: 0;
                font-size: 14px;
            }

            .close {
                display: block;
                position: absolute;
                right: 10px;
                top: 10px;
                height: 10px;
                width: 10px;
            }

            .close svg {
                display: block;
                max-width: 100%;
                height: auto;
                fill: #5B5B5B;
            }
        }

        .question-triangle-top {
            top: calc(100% - 5px);
            border-color: transparent transparent #ddd transparent;
        }

        .question-triangle-bottom {
            top: calc(100% - 4px);
            border-color: transparent transparent #FFF transparent;
        }

        .question-triangle-top,
        .question-triangle-bottom {
            left: 25px;
            z-index: 101;
            border-style: solid;
            border-width: 10px 15px;
            position: absolute;
            left: calc(50% - 15px);
        }

        #mcs-button-yes,
        #mcs-button-no {
            border: none;
            outline: none;
            padding: 0;
            color: #046;
            font-size: 14px;
            text-decoration: none;
            border-bottom: 1px dashed #046;
            background-color: transparent;
            cursor: pointer;
        }

        #mcs-button-yes {
            margin-right: 15px;
        }
    }
</style>
