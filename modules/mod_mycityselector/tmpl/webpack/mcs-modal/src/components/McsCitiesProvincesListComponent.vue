<template>
  <div class="locations-wrapper mcs-cities-provinces-list">
      <div class="provinces-list">
          <span class="title">{{ $t('Регионы') }}</span>
          <ul>
              <li v-for="province in provinces"
                  :class="{ 'active' : province.province_code == provinceCode}"
              >
                  <a :href="province.province_link"
                     @click.prevent="selectProvince(province.province_code)" rel="nofollow">{{ province.province_name }}</a>
              </li>
              <li v-if="!isEmptyObj(countryProp) && allowSelectWhole == 1"
                  :class="{ 'active' : countryProp.country_code == provinceCode}"
              >
                  <a :href="countryProp.country_link"
                     @click.prevent="selectProvince(countryProp.country_code); selectLocation(countryProp, 'country')" rel="nofollow">Другой регион</a>
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
                     @click.prevent="selectCity(city.city_code); selectLocation(city, 'city')" rel="nofollow">{{ city.city_name }}</a>
              </li>
              <li v-if="!isEmptyObj(currentProvince) && allowSelectWhole == 1"
                  :class="{ 'active' : currentProvince.province_code == cityCode}">
                  <a :href="currentProvince.province_link"
                     @click.prevent="selectCity(currentProvince.province_code); selectLocation(currentProvince, 'province')" rel="nofollow">{{ $t('Другой город') }}</a>
              </li>
          </ul>
      </div>
  </div>
</template>

<script>
  import geo from './../geolocation.js';

  export default {
  data () {
    return {
      cityCode: null,
      provinceCode: null
    }
  },
  props: {
    locations: Array,
    cityCodeProp: String,
    provinceCodeProp: String,
    countryProp: Object,
    show: Boolean,
    allowSelectWhole: String
  },
  computed: {
    provinces: function() {
      return this.groupProvinces(this.locations);
    },
    cities: function() {
      return this.getCitiesByProvince(this.locations, this.provinceCode)
    },
    currentProvince: function() {
      return this.getProvinceByCode(this.locations, this.provinceCode)
    }
  },
  methods: {
      //формируем список городов в вид [первая буква города] => города
      groupProvinces: function(locations) {
        let groupedProvincesByName = {};

        //группируем по названию
        locations.forEach(function(value) {
          groupedProvincesByName[value.province_name] = value;
        });

        //сортируем эти ключи
        let groupedProvincesByNameOrdered = {};
        Object.keys(groupedProvincesByName).sort().forEach(function(key) {
            groupedProvincesByNameOrdered[key] = groupedProvincesByName[key];
        });

        return groupedProvincesByNameOrdered;
      },
      getCitiesByProvince: function(locations, currentProvinceCode) {
        let groupedCitiesByProvince = [];

        //группируем по названию провинции
        locations.forEach(function(value) {
          if(value.province_code == currentProvinceCode) {
              groupedCitiesByProvince.push(value);
          }
        });

        //сортируем города по названию
        groupedCitiesByProvince.sort(function(a, b) {
          return (a.city_name < b.city_name) ? -1 : (a.city_name > b.city_name) ? 1 : 0;
        });

        return groupedCitiesByProvince;
      },
      getProvinceByCode: function(locations, currentProvinceCode) {
        let province = {};

        locations.forEach(function(value) {
          if(value.province_code == currentProvinceCode) {
            province = value;
          }
        });

        return province;
      },
      isEmptyObj: function(obj) {
          return Object.keys(obj).length === 0;
      },
      selectProvince: function(code) {
          document.querySelector('.mcs-dialog .inner').scrollTo(0,0);
          this.provinceCode = code;
      },
      selectCity: function(code) {
        this.cityCode = code;
          this.$emit('hide-modal');
      },
      selectLocation: function(location, type) {
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
      }
  },
  mounted: function () {
    this.cityCode = this.cityCodeProp;
    this.provinceCode = this.provinceCodeProp;
  }
}
</script>

<style lang="scss">
    .mcs-cities-provinces-list {
        display: flex;
        width: 100%;

        .provinces-list, .cities-list {
            width: 50%;
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
    }

    @media only screen and (min-device-width : 320px) and (max-device-width : 767px) {
        .mcs-dialog {
            .mcs-cities-provinces-list {
                flex-direction: column;
                width: 100%;

                .provinces-list {
                    width: 100%;
                    margin-bottom: 25px;
                    .title {
                        margin-bottom: 15px;
                        position: relative;
                        top: 0;
                    }
                }
            }
        }
    }
</style>
