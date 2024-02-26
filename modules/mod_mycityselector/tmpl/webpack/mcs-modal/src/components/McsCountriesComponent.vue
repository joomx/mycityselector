<template>
    <div class="mcs-cities-provinces-countries-list">
        <div class="countries-list">
            <span class="title">{{ $t('Страны') }}</span>
            <ul>
                <li v-for="(country, index) in countries"
                    :key="index"
                    :class="{ 'active' : country.country_code == countryCode}"
                >
                    <a :href="country.country_link"
                       @click.prevent="selectCountry(country)" rel="nofollow">{{ country.country_name }}</a>
                </li>
            </ul>
        </div>

        <div class="cities-list">
            <span class="title">{{ $t('Города') }}</span>
            <ul>
                <li v-for="city in cities"
                    :class="{ 'active' : city.city_code == cityCode}"
                >
                    <a :href="city.city_link"
                       @click.prevent="selectCity(city.city_code); selectLocation(city, 'city')" rel="nofollow">{{
                        city.city_name }}</a>
                </li>
                <li v-if="!isEmptyObj(currentCountry) && allowSelectWhole == 1"
                    :class="{ 'active' : currentProvince.province_code == cityCode}">
                    <a :href="currentProvince.province_link"
                       @click.prevent="selectCity(currentProvince.province_code); selectLocation(currentProvince, 'province')"
                       rel="nofollow">{{ $t('Другой город') }}</a>
                </li>
            </ul>
        </div>

        <!--    <mcs-cities-provinces-list-component-->
        <!--            :locations="locationsByCurrentCountry"-->
        <!--            :city-code-prop="cityCodeProp"-->
        <!--            :province-code-prop="provinceCodeProp"-->
        <!--            :country-prop="currentCountry"-->
        <!--    >-->
        <!--    </mcs-cities-provinces-list-component>-->

    </div>
</template>

<script>
    import geo from './../geolocation.js';

    import McsCitiesProvincesListComponent from './McsCitiesProvincesListComponent.vue';

    export default {
        components: {
            McsCitiesProvincesListComponent
        },
        data() {
            return {
                cityCode: null,
                provinceCode: null,
                countryCode: null,
                type: null
            }
        },
        props: {
            locations: Array,
            cityCodeProp: String,
            provinceCodeProp: String,
            countryCodeProp: String,
            show: Boolean,
            allowSelectWhole: String
        },
        computed: {
            currentProvince: function () {
                return this.getProvinceByCode(this.locations, this.provinceCode)
            },
            cities: function () {
                return this.getCitiesByProvince(this.locations, this.countryCode)
            },
            countries: function () {
                return this.getCountries(this.locations);
            },
            locationsByCurrentCountry: function () {
                return this.getLocationsByCountryCode(this.locations, this.countryCode);
            },
            currentCountry: function () {
                return this.getCurrentCountry(this.locations, this.countryCode);
            },
        },
        methods: {
            selectLocation: function (location, type) {
                switch (type) {
                    case 'country':
                        location.type = 'country';
                        geo.selectLocation(location);
                        window.location = location.country_link;
                        break;
                    case 'province':
                        location.type = 'province';
                        geo.selectLocation(location);
                        window.location = location.province_link;
                        break;
                    case 'city':
                        location.type = 'city';
                        geo.selectLocation(location);
                        window.location = location.city_link;
                        break;
                }
            },
            selectCity: function (code) {
                this.cityCode = code;
                this.$emit('hide-modal');
            },
            getProvinceByCode: function (locations, currentProvinceCode) {
                let province = {};

                locations.forEach(function (value) {
                    if (value.province_code == currentProvinceCode) {
                        province = value;
                    }
                });

                return province;
            },
            isEmptyObj: function (obj) {
                return Object.keys(obj).length === 0;
            },
            getCitiesByProvince: function (locations, currentProvinceCode) {
                let groupedCitiesByProvince = [];

                //группируем по названию провинции
                locations.forEach(function (value) {
                    if (value.country_code == currentProvinceCode) {
                        groupedCitiesByProvince.push(value);
                    }
                });

                //сортируем города по названию
                groupedCitiesByProvince.sort(function (a, b) {
                    return (a.city_name < b.city_name) ? -1 : (a.city_name > b.city_name) ? 1 : 0;
                });

                return groupedCitiesByProvince;
            },
            getCountries: function (locations) {
                let countries = [];

                //группируем
                locations.forEach(function (value) {
                    countries[value.country_ordering] = value;
                });

                countries = countries.filter(Boolean);

                return countries;
            },
            getLocationsByCountryCode: function (locations, countryCode) {
                let filteredLocations = locations.filter(function (location) {
                    return location.country_code == countryCode;
                });

                return filteredLocations;
            },
            getCurrentCountry: function (locations, countryCode) {
                let country = {};

                locations.forEach(function (value) {
                    if (value.country_code == countryCode) {
                        country = value;
                    }
                });

                return country;
            },
            selectCountry: function (country) {
                this.countryCode = country.country_code;
            }
        },
        mounted: function () {
            this.cityCode = this.cityCodeProp;
            this.provinceCode = this.provinceCodeProp;
            this.countryCode = this.countryCodeProp;
        }
    }
</script>

<style lang="scss">
    .mcs-dialog {
        .mcs-cities-provinces-countries-list {
            display: flex;
            width: 100%;
            height: 100%;

            .countries-list, .cities-list {
                width: 33.3%;
                box-sizing: border-box;

                .title {
                    display: block;
                    position: absolute;
                    top: 25px;
                    font-size: 24px;
                    line-height: 1;
                    color: #808080;
                    z-index: 11111;
                }

                ul {
                    list-style: none;
                    display: block;
                    border-left: 1px solid #d8d8d8;
                    margin: 0;
                    height: 100%;
                    padding: 20px 0;
                    box-sizing: border-box;

                    li {

                        a {
                            color: #000;
                            display: block;
                            padding: 10px 1em 10px 20px;
                            font-size: 16px;
                            text-decoration: none;
                        }

                        &:hover {
                            a {
                                color: #336699;
                            }
                        }

                        &.active {
                            position: relative;
                            background-color: #f7f7f7;

                            &::before {
                                content: '';
                                position: absolute;
                                left: 0;
                                top: 0;
                                width: 3px;
                                height: 100%;
                                background-color: #336699;
                                opacity: .9;
                            }

                            a {
                                font-weight: bold;
                                color: #336699;
                            }
                        }
                    }
                }
            }

            .mcs-cities-provinces-list {
                width: 66.6%;
                box-sizing: border-box;
            }

        }
    }

    @media only screen and (min-device-width: 320px) and (max-device-width: 767px) {
        .mcs-dialog {
            .mcs-cities-provinces-countries-list {
                flex-direction: column;

                .countries-list, .cities-list {
                    width: 100%;
                    margin-bottom: 25px;

                    .title {
                        margin-bottom: 15px;
                        position: relative;
                        top: 0;
                    }
                }

                .mcs-cities-provinces-list {
                    width: 100%;
                }
            }
        }
    }
</style>
