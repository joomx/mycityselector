<template>
  <div class="cities-wrapper">
      <div class="cities">
          <div class="cities-group" v-for="(citiesGroup, key) in citiesFormatted">
              <b class="first-letter-cities">{{ key }}</b>
              <div class="city" v-for="city in citiesGroup">
                  <a :class="{ 'active' : city.city_code == cityCodeProp}" @click.prevent="selectCity(city)"
                     class="link" :href="city.city_link" rel="nofollow">{{ city.city_name }}</a>
              </div>
          </div>
      </div>
  </div>
</template>

<script>
import geo from './../geolocation.js';

export default {
    data () {
        return {
            type: null
        }
    },
    props: {
        cities: Array,
        cityCodeProp: String
    },
    computed: {
        citiesFormatted: function() {
            return this.groupLocationsByAlphabet(this.cities);
        }
    },
    methods: {
        //формируем список городов в вид [первая буква города] => города
        groupLocationsByAlphabet: function(locations) {
            let groupedCitiesByAlphabet = {};

            //делаем первую букву города ключом
            locations.forEach(function(location) {
                let firstCharCity = location.city_name.charAt(0);

                if (typeof groupedCitiesByAlphabet[firstCharCity] === 'undefined')
                    groupedCitiesByAlphabet[firstCharCity] = [];

                groupedCitiesByAlphabet[firstCharCity].push(location);
            });

            //сортируем эти ключи
            let groupedCitiesByAlphabetOrdered = {};
            Object.keys(groupedCitiesByAlphabet).sort().forEach(function(key) {
                groupedCitiesByAlphabetOrdered[key] = groupedCitiesByAlphabet[key];
            });

            //сортируем города по названию в подгруппах
            for(let index in groupedCitiesByAlphabetOrdered) {
                groupedCitiesByAlphabetOrdered[index] = groupedCitiesByAlphabetOrdered[index].sort(function(a, b) {
                    return (a.city_name < b.city_name) ? -1 : (a.city_name > b.city_name) ? 1 : 0;
                });
            }

            return groupedCitiesByAlphabetOrdered;
        },
        //сохраняем город
        selectCity: function(city) {
            if(city !== 'undefined' && city !== null) {
                city.type = 'city';
                geo.selectLocation(city);
                window.location = city.city_link;
                this.$emit('hideModal');
            }
        }
    }
}
</script>

<style lang="scss">
    .mcs-dialog a:hover {
        text-decoration: underline;
    }

    .mcs-dialog .cities {
      position: relative;
      z-index: 99;
      display: flex;
      flex-wrap: wrap;
    }

    .mcs-dialog .cities .cities-group {
      position: relative;
      padding-top: 1px;
      padding-left: 30px;
      padding-bottom: 20px;
      -webkit-box-sizing: border-box;
      box-sizing: border-box;
      -webkit-column-break-inside: avoid;
      -webkit-backface-visibility: hidden;
      flex-basis: 32.3333%;
    }

    .mcs-dialog .cities .cities-group .first-letter-cities {
        position: absolute;
        top: 0;
        left: 0;
        font-size: 18px;
        color: #336699;
    }

    .mcs-dialog a.active {
        color: #336699;
        font-weight: bold;
        text-decoration: none;
    }

    .mcs-dialog a {
        font-size: 16px;
        line-height: 26px;
        color: #828282;
        text-decoration: none;
    }

    .mcs-dialog .inner:before {
        display: none !important;
    }

</style>
