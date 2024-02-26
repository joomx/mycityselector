<template>
    <div>
        <transition name="modal-fade">
            <div class="mcs-dialog" v-show="showModal" :style="{ height: modalHeight ? modalHeight + 'px' : 'auto' }">
                <mcs-modal-header-component
                        :locations="locations"
                        :modal-header-title="modalHeaderTitle"
                        :modal-header-search-placeholder="modalHeaderSearchPlaceholder"
                >
                </mcs-modal-header-component>
                <div class="inner">
                    <mcs-cities-list-component v-if="type == 0"
                       :cities="locations"
                       :city-code-prop="currentCityCode"
                       :allow-select-whole="allowSelectWhole"
                    >
                    </mcs-cities-list-component>
                    <mcs-cities-provinces-list-component v-if="type == 1"
                        :locations="locations"
                        :city-code-prop="currentCityCode"
                        :province-code-prop="currentProvinceCode"
                        :country-prop="{}"
                        :allow-select-whole="allowSelectWhole"
                    >
                    </mcs-cities-provinces-list-component>
                    <mcs-cities-provinces-countries-list-component v-if="type == 2"
                        :locations="locations"
                        :city-code-prop="currentCityCode"
                        :province-code-prop="currentProvinceCode"
                        :country-code-prop="currentCountryCode"
                        :allow-select-whole="allowSelectWhole"
                    >
                    </mcs-cities-provinces-countries-list-component>


                    <mcs-countries-component v-if="type == 3"
                       :locations="locations"
                       :city-code-prop="currentCityCode"
                       :province-code-prop="currentProvinceCode"
                       :country-code-prop="currentCountryCode"
                       :allow-select-whole="allowSelectWhole"
                    >
                    </mcs-countries-component>


                </div>
            </div>
        </transition>
        <div v-if="showModal" class="mcs-overlay" @click="closeModal"></div>
    </div>
</template>

<script>
import McsModalHeaderComponent from './McsModalHeaderComponent.vue';
import McsCitiesListComponent from './McsCitiesListComponent.vue';
import McsCitiesProvincesListComponent from './McsCitiesProvincesListComponent.vue';
import McsCitiesProvincesCountriesListComponent from './McsCitiesProvincesCountriesListComponent.vue';
import McsCountriesComponent from "./McsCountriesComponent";

function vardump() {
    if (window.mcs_debug_mode && window.mcs_debug_mode == 1) {
        for (let key = 0; key < arguments.length; key++) window.console.log(arguments[key]);
    }
}

export default {
    components: {
        McsCountriesComponent,
        McsModalHeaderComponent,
        McsCitiesListComponent,
        McsCitiesProvincesListComponent,
        McsCitiesProvincesCountriesListComponent
    },
    data () {
        return {
            type: null,
            showModal: false,
            modalHeight: false
        }
    },
    props: {
        locations: Array,
        currentCityCode: String,
        currentProvinceCode: String,
        currentCountryCode: String,
        modalHeaderTitle: String,
        modalHeaderSearchPlaceholder: String,
        allowSelectWhole: String,
    },
    methods: {
        closeModal: function() {
            vardump('closeModal()');
            this.$nextTick(() => {
                this.modalHeight = document.getElementsByClassName('mcs-dialog')[0].clientHeight;
            });
            document.querySelector('body').style.overflow = 'initial';
            this.showModal = false;
        },
        openModal: function() {
            vardump('openModal()');
            this.$nextTick(() => {
                this.modalHeight = document.getElementsByClassName('mcs-dialog')[0].clientHeight;
            });
            document.querySelector('body').style.overflow = 'hidden';
            this.showModal = true;
        }
    },
    mounted: function () {
        vardump('McsModalComponent is mounted');
        this.type = window.mcs_list_type;
        this.$root.$on('switchModalDisplay', bool => {
            vardump('Event "switchModalDisplay" received', bool);
            if (bool) {
                this.openModal();
            } else {
                this.closeModal();
            }
        })
    }
}
</script>

<style lang="scss">

    .mcs-dialog .inner {
        position: relative;
        height: 450px;
        padding: 50px 30px 30px;
        overflow-y: auto;

        &::before {
            content: '';
            display: block;
            position: absolute;
            width: 100%;
            z-index: 10000;
            box-shadow: 0 0 35px 25px #fff;
        }
    }

    .mcs-module {
        position: relative;
        width: 200px;
        text-align: left;
    }

    .mcs-module .mcs-module-inner {
        position: relative;
        display: inline-block;
    }

    .mcs-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, .2);
        z-index: 9997;
    }

    .mcs-dialog {
        position: fixed;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        display: flex;
        flex-direction: column;
        max-width: 810px;
        max-height: 90vh;
        height: auto;
        width: 100%;
        z-index: 9998;
        box-sizing: border-box;
        background: #fff;
        border-radius: 5px;
        border: 1px solid rgba(0,0,0,.2);
        box-shadow: 0 16px 24px 0 #555 !important;
    }

    .modal-fade-enter-active {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
        transition: all .3s ease;
    }

    .modal-fade-leave-active {
        transition: all .0s;
    }

    .modal-fade-enter, .modal-fade-leave-to {
        transform: translate(-50%, -50%) scale(0.7);
        opacity: 0;
        transition: all .3s;
    }

    @media only screen and (min-device-width : 320px) and (max-device-width : 767px) {
        .mcs-dialog {
            height: auto !important;
            .inner {
                padding: 25px 20px;
            }
        }
    }

</style>
