<template>
  <div class="mcs-modal-header">
    <div class="mcs-header">
      <div class="close" @click="closeModal">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
             viewBox="0 0 21.9 21.9" enable-background="new 0 0 21.9 21.9">
          <path
              d="M14.1,11.3c-0.2-0.2-0.2-0.5,0-0.7l7.5-7.5c0.2-0.2,0.3-0.5,0.3-0.7s-0.1-0.5-0.3-0.7l-1.4-1.4C20,0.1,19.7,0,19.5,0  c-0.3,0-0.5,0.1-0.7,0.3l-7.5,7.5c-0.2,0.2-0.5,0.2-0.7,0L3.1,0.3C2.9,0.1,2.6,0,2.4,0S1.9,0.1,1.7,0.3L0.3,1.7C0.1,1.9,0,2.2,0,2.4  s0.1,0.5,0.3,0.7l7.5,7.5c0.2,0.2,0.2,0.5,0,0.7l-7.5,7.5C0.1,19,0,19.3,0,19.5s0.1,0.5,0.3,0.7l1.4,1.4c0.2,0.2,0.5,0.3,0.7,0.3  s0.5-0.1,0.7-0.3l7.5-7.5c0.2-0.2,0.5-0.2,0.7,0l7.5,7.5c0.2,0.2,0.5,0.3,0.7,0.3s0.5-0.1,0.7-0.3l1.4-1.4c0.2-0.2,0.3-0.5,0.3-0.7  s-0.1-0.5-0.3-0.7L14.1,11.3z"></path>
        </svg>
      </div>
      <div class="title">{{ modalHeaderTitle }}</div>
      <div class="form-elements-wrapper">
        <div class="quick-search">
          <vue-autosuggest
              :suggestions="filteredOptions"
              :input-props="suggestionsOptions"
              @selected="selectHandler"
              @input="onInputChange"
          >
            <template slot-scope="{suggestion}">
              <div class="suggestion-wrapper"
                   tabindex="-1">
                          <span v-if="suggestion.item.type === 'city'"
                                class="suggestion-provinces-content">
                            {{ suggestion.item.name }}
                            <span class="province">{{
                                suggestion.item.location.province_name
                              }} - {{ suggestion.item.location.country_name }}</span>
                          </span>
                <span v-else-if="suggestion.item.type === 'province'"
                      class="suggestion-provinces-content">
                            {{ suggestion.item.name }} - {{ suggestion.item.location.country_name }}
                          </span>
                <span v-else
                      class="suggestion-provinces-content">
                            {{ suggestion.item.name }}
                          </span>
              </div>
            </template>
          </vue-autosuggest>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {VueAutosuggest} from 'vue-autosuggest';
import geo from './../geolocation.js';

export default {
  components: {
    VueAutosuggest
  },
  data() {
    return {
      query: '',
      selectedLocation: null,
      selectedSearchItem: null,
      filteredOptions: [],
      suggestionsOptions: {
        //TODO поменять
        id: 'autosuggest__input' + Math.random().toString(36).substring(7),
        // onInputChange: this.onInputChange
      }
    }
  },
  props: {
    locations: Array,
    type: String,
    modalHeaderTitle: String,
    modalHeaderSearchPlaceholder: String
  },
  computed: {
    locationsForSearch: function () {
      return this.formatLocationsForSearch(this.locations);
    }
  },
  methods: {
    closeModal: function () {
      this.$root.$emit('switchModalDisplay', false);
    },
    formatLocationsForSearch: function (locations) {
      let formattedLocations = [];

      //в зависимости от выбранноо режима, добавляем регионы
      locations.forEach(function (value) {

        switch (window.mcs_list_type) {
          case '0':
            formattedLocations.push({type: 'city', code: value.city_code, name: value.city_name, location: value});

            break;
          case '1':
            formattedLocations.push({type: 'city', code: value.city_code, name: value.city_name, location: value});
            formattedLocations.push({
              type: 'province',
              code: value.province_code,
              name: value.province_name,
              location: value
            });

            break;
          case '2':
            formattedLocations.push({type: 'city', code: value.city_code, name: value.city_name, location: value});
            formattedLocations.push({
              type: 'province',
              code: value.province_code,
              name: value.province_name,
              location: value
            });
            formattedLocations.push({
              type: 'country',
              code: value.country_code,
              name: value.country_name,
              location: value
            });

            break;

          case '3':
            formattedLocations.push({type: 'city', code: value.city_code, name: value.city_name, location: value});
            formattedLocations.push({
              type: 'country',
              code: value.country_code,
              name: value.country_name,
              location: value
            });

            break;
        }
      });

      //сортируем по названию
      formattedLocations.sort(function (a, b) {
        return (a.name < b.name) ? -1 : (a.name > b.name) ? 1 : 0;
      });

      //удаляем дубли
      let foundedLocationsCodes = [];
      formattedLocations = formattedLocations.filter(function (value) {

        if (!foundedLocationsCodes.includes(value.code)) {
          foundedLocationsCodes.push(value.code);
          return true;
        }

        return false;
      });

      return formattedLocations;
    },
    onInputChange(text) {
      if (text === null) {
        /* Maybe the text is null but you wanna do
        *  something else, but don't filter by null.
        */
        return;
      }
      // Full customizability over filtering
      const filteredData = this.locationsForSearch.filter(option => {
        return option.name.toLowerCase().indexOf(text.toLowerCase()) > -1;
      });

      // Store data in one property, and filtered in another
      this.filteredOptions = [{data: filteredData}];
    },
    selectHandler(value) {
      this.selected = value;

      let item = value.item;

      switch (item.type) {
        case 'country':
          item.location.type = 'country';
          geo.selectLocation(item.location);
          window.location = item.location.country_link;
          break;
        case 'province':
          item.location.type = 'province';
          geo.selectLocation(item.location);
          window.location = item.location.province_link;
          break;
        case 'city':
          item.location.type = 'city';
          geo.selectLocation(item.location);
          window.location = item.location.city_link;
          break;
      }
    },
    /**
     * This is what the <input/> value is set to when you are selecting a suggestion.
     */
    getSuggestionValue(suggestion) {
      return suggestion.item.name;
    }
  },
  created: function () {
    this.filteredOptions = [{data: this.locationsForSearch}];
    this.suggestionsOptions.placeholder = this.modalHeaderSearchPlaceholder;
  }
}
</script>

<style lang="scss">
.mcs-modal-header {

  .mcs-header {
    position: relative;
    padding: 25px 30px 0;

    .title {
      color: #369;
      font-size: 24px;
      font-weight: bold;
      margin-bottom: 30px;
    }
  }

  .quick-search {
    position: relative;

    &::after {
      display: block;
      content: '';
      position: absolute;
      width: 20px;
      height: 20px;
      box-sizing: border-box;
      right: 20px;
      top: 17px;
      pointer-events: none;
      background-image: url("../assets/search.svg");
      background-size: contain;
      background-position: center;
      background-repeat: no-repeat;
    }

    input {
      height: 55px;
      box-shadow: inset 0 1px 1px 0 rgba(0, 0, 0, 0.075);
      transition: all ease-in-out 0.15s;
      display: block;
      width: 100%;
      padding: 6px 12px;
      line-height: 1.42857;
      font-size: 16px;
      color: #555;
      background-color: #fff;
      background-image: none;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;

      &:focus {
        border-color: #336699;
        outline: 0;
        box-shadow: inset 0 1px 1px 0 rgba(0, 0, 0, 0.075);
      }
    }

    .autosuggest__results-container {
      position: absolute;
      height: auto;
      max-height: 535px;
      overflow-y: auto;
      width: 100%;
      top: 55px;
      left: 0;
      right: 0;
      outline: none;
      background-color: #fff;
      box-sizing: border-box;
      box-shadow: 0 5px 10px rgba(0, 0, 0, .1);
      z-index: 111111;

      ul {
        list-style: none;
        padding: 0;
        margin: 0;

        li {
          background-color: #fff;

          .suggestion-wrapper {
            position: relative;
            padding: 25px 20px;
            box-sizing: border-box;
            font-size: 16px;
            color: #000;
            cursor: pointer;

            &::before {
              content: "";
              display: block;
              position: absolute;
              bottom: 0;
              left: 20px;
              right: 20px;
              height: 1px;
              background-color: #eee;
            }

            .province {
              display: inline-block;
              vertical-align: top;
              width: 100%;
              font-size: 14px;
              color: #999;
              margin: 5px 0 -1px;
              box-sizing: border-box;
            }
          }

          &:last-of-type {
            .suggestion-wrapper {
              &::before {
                display: none;
              }
            }
          }

          &.autosuggest__results_item-highlighted,
          &:hover {
            background-color: #369;

            .suggestion-wrapper {
              color: #fff;

              .province {
                color: #fff;
              }
            }
          }
        }

      }
    }
  }

  .close {
    position: absolute;
    top: 15px;
    right: 25px;
    height: 34px;
    width: 34px;
    box-sizing: border-box;
    background-color: #fff;
    border: 1px solid #e9e9e9;
    transition: background-color .3s, border-color .3s;
    border-radius: 100%;
    opacity: 1;
    cursor: pointer;

    svg {
      position: absolute;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
      display: block;
      width: 12px;
      height: 12px;
      box-sizing: border-box;
      fill: #369;
      transition: fill .3s;
    }

    &:hover {
      background-color: #336699;
      border-color: #336699;
      opacity: 1;

      svg {
        fill: #fff;
      }

    }
  }


  @media only screen and (min-device-width: 768px) and (max-device-width: 991px) {
    .mcs-modal-header {
      padding: 25px 40px;

      .form-element-wrapper {
        width: 505px;
      }

    }
  }

  @media only screen and (min-device-width: 320px) and (max-device-width: 767px) {
    .mcs-header {
      padding: 15px 20px 0;

      .title {
        margin-bottom: 15px;
        font-size: 18px;
      }
    }

    .close {
      width: 28px;
      height: 28px;
      top: 10px;
      right: 10px;

      svg {
        width: 8px;
        height: 8px;
      }
    }

    .quick-search {
      input {
        height: 40px;
      }

      &::after {
        width: 16px;
        height: 16px;
        right: 15px;
        top: 12px;
      }

      .autosuggest__results-container {
        max-height: calc(90vh - 110px);
        top: 40px;

        ul {
          li {
            .suggestion-wrapper {
              padding: 15px 10px;
              font-size: 14px;

              &::before {
                left: 10px;
                right: 10px;
              }

              .province {
                font-size: 12px;
              }
            }
          }
        }
      }
    }
  }
}
</style>
