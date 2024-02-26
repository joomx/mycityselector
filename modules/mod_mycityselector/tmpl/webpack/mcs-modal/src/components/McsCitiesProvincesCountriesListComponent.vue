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
    <mcs-cities-provinces-list-component
            :locations="locationsByCurrentCountry"
            :city-code-prop="cityCodeProp"
            :province-code-prop="provinceCodeProp"
            :country-prop="currentCountry"
            :allow-select-whole="allowSelectWhole"
    >
    </mcs-cities-provinces-list-component>

  </div>
</template>

<script>
  import McsCitiesProvincesListComponent from './McsCitiesProvincesListComponent.vue';

  export default {
  components: {
    McsCitiesProvincesListComponent
  },
  data () {
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
    countries: function() {
      return this.getCountries(this.locations);
    },
    locationsByCurrentCountry: function() {
      return this.getLocationsByCountryCode(this.locations, this.countryCode);
    },
    currentCountry: function() {
      return this.getCurrentCountry(this.locations, this.countryCode);
    }
  },
  methods: {
    getCountries: function(locations) {
      let countries = [];

      //группируем
      locations.forEach(function(value) {
        countries[value.country_ordering] = value;
      });

      countries = countries.filter(Boolean);

      return countries;
    },
    getLocationsByCountryCode: function(locations, countryCode) {
      let filteredLocations = locations.filter(function(location) {
          return location.country_code == countryCode;
      });

      return filteredLocations;
    },
    getCurrentCountry: function(locations, countryCode) {
      let country = {};

      locations.forEach(function(value) {
        if(value.country_code == countryCode) {
          country = value;
        }
      });

      return country;
    },
    selectCountry: function(country) {
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

        .countries-list {
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

    @media only screen and (min-device-width : 320px) and (max-device-width : 767px) {
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
