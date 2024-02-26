import axios from 'axios';

class McsGeo {

    constructor() {
        McsGeo.vardump('McsGeo begin initialize');

        // for debug mode
        if (!window.console) window.console = {};
        if (!window.console.log) window.console.log = {};
        if (!window.console.error) window.console.error = window.console.log;


        McsGeo.vardump('McsGeo started');
    }

    static vardump() {
        if (window.mcs_debug_mode && window.mcs_debug_mode == 1) {
            for (let key = 0; key < arguments.length; key++) window.console.log(arguments[key]);
        }
    }

    yandex_geolocation() {
        return new Promise((resolve, reject) => {
            ymaps.ready(() => {
                McsGeo.vardump("McsGeo yandex_geolocation");
                if (location.protocol == 'https:') {
                    if (ymaps.geolocation) {

                        McsGeo.vardump("McsGeo ymaps.geolocation send request");
                        ymaps.geolocation.get({
                            // Выставляем опцию для определения положения по ip
                            provider: 'auto',
                            // Автоматически геокодируем полученный результат.
                            autoReverseGeocode: true
                        }).then((result) => {
                            // Выведем в консоль данные, полученные в результате геокодирования объекта.
                            let location = {};

                            location.city = result.geoObjects.get(0).properties.get('metaDataProperty.GeocoderMetaData.AddressDetails.Country.AdministrativeArea.SubAdministrativeArea.Locality.LocalityName');

                            location.province = result.geoObjects.get(0).properties.get('metaDataProperty.GeocoderMetaData.AddressDetails.Country.AdministrativeArea.AdministrativeAreaName');

                            location.country = result.geoObjects.get(0).properties.get('metaDataProperty.GeocoderMetaData.AddressDetails.Country.CountryNameCode');

                            if (location.country == undefined) {
                                location.country = result.geoObjects.get(0).properties.get('metaDataProperty.GeocoderMetaData.Address.country_code');
                            }

                            if (location.country == undefined) {
                                location.country = result.geoObjects.get(0).properties.get('metaDataProperty.GeocoderMetaData.AddressDetails.CountryName');
                            }

                            resolve(location);

                            McsGeo.vardump('McsGeo Определен город: ' + location.city);
                            McsGeo.vardump('McsGeo Тип выбора города:  ' + window.mcs_let_select);
                        }).catch(function (err) {
                            reject(err);
                            McsGeo.vardump("McsGeo ymaps.geolocation exception");
                        });
                    } else {
                        reject(false);
                        McsGeo.vardump("McsGeo ymaps.geolocation not defined");
                    }
                } else {
                    reject(false);
                    McsGeo.vardump("McsGeo ymaps.geolocation is enabled only for HTTPS");
                }
            }, this);
        });
    }

    ipApi_geolocation() {
        McsGeo.vardump('request', '/?option=com_mycityselector&task=ipgeo.getlocation');
        return new Promise((resolve, reject) => {
            axios.get('/?option=com_mycityselector&task=ipgeo.getlocation').then(function (response) {
                McsGeo.vardump('response', response);
                // handle success
                let location = response.data;
                if (location.city || location.province || location.country) {
                    McsGeo.vardump('McsGeo Определен город: ' + location.city);
                    McsGeo.vardump('McsGeo Тип выбора города:  ' + window.mcs_let_select);
                    resolve(location);
                } else {
                    McsGeo.vardump('McsGeo ipApi_geolocation: город не определен');
                    reject(false);
                }
            }).catch(function (error) {
                reject(error);
            });
        });

    }

    /**
     * находит соответствие между найденым местоположением и доступными для выбора местоположениями
     * возвращает найденное местоположение
     */
    defineLocation(location, locations) {
        if (location !== 'undefined' && location !== null) {
            let definedLocation = { };
            switch (window.mcs_list_type) {
                case '1':
                    definedLocation = this.searchCityInLocations(location, locations);
                    if (definedLocation == null) {
                        definedLocation = this.searchProvinceInLocations(location, locations);
                    }
                    break;
                case '2':
                    definedLocation = this.searchCityInLocations(location, locations);

                    if (definedLocation == null) {
                        definedLocation = this.searchProvinceInLocations(location, locations);
                    }
                    if (definedLocation == null) {
                        definedLocation = this.searchCountryInLocations(location, locations);
                    }
                    break;
                default:
                    definedLocation = this.searchCityInLocations(location, locations);
                    break;
            }
            return definedLocation;
        }
    }

    /**
     * находит соответствие между найденым местоположением и доступными для выбора городами
     * возвращает найденное местоположение
     */
    searchCityInLocations(location, locations) {
        var definedCity = null;

        locations.forEach(function (value) {
            if (location.city == value.city_name) {
                definedCity = {};
                definedCity.type = 'city';
                definedCity.link = value.city_link || '';
                definedCity.name = value.city_name;
                definedCity.country_code = value.country_code;
                definedCity.country_name = value.country_name;
                definedCity.province_code = value.province_code;
                definedCity.province_name = value.province_name;
                definedCity.city_code = value.city_code;
                definedCity.city_name = value.city_name;
            }
        });

        return definedCity;
    }

    /**
     * находит соответствие между найденым местоположением и доступными для выбора регионами
     * возвращает найденное местоположение
     */
    searchProvinceInLocations(location, locations) {
        var definedCity = null;

        locations.forEach(function (value) {
            if (location.province == value.province_name) {
                definedCity = {};
                definedCity.type = 'province';
                definedCity.link = value.province_link;
                definedCity.name = value.province_name;
                definedCity.country_code = value.country_code;
                definedCity.country_name = value.country_name;
                definedCity.province_code = value.province_code;
                definedCity.province_name = value.province_name;
                definedCity.city_name = null;
                definedCity.city_code = null;
            }
        });

        return definedCity;
    }

    /**
     * находит соответствие между найденым местоположением и доступными для выбора странами
     * возвращает найденное местоположение
     */
    searchCountryInLocations(location, locations) {
        var definedCity = null;

        locations.forEach(function (value) {
            if (location.country == value.country_code || location.country == value.country_name) {
                definedCity = {};
                definedCity.type = 'country';
                definedCity.link = value.country_link;
                definedCity.name = value.country_name;
                definedCity.country_code = value.country_code;
                definedCity.country_name = value.country_name;
                definedCity.city_name = null;
                definedCity.city_code = null;
                definedCity.province_name = null;
                definedCity.province_code = null;
            }
        });

        return definedCity;
    }

    /**
     * Запоминает выбранный город и запускает переключение контента
     * @returns {boolean}
     */
    selectLocation(location) {

        if (location !== 'undefined' && location !== null) {
            // => сохраняем в cookie название выбранного местоположения

            switch (location.type) {
                case 'country':
                    this.setCookie('MCS_COUNTRY_CODE', location.country_code);
                    this.setCookie('MCS_COUNTRY_NAME', location.country_name);

                    this.setCookie('MCS_PROVINCE_CODE', '');
                    this.setCookie('MCS_PROVINCE_NAME', '');

                    this.setCookie('MCS_CITY_CODE', '');
                    this.setCookie('MCS_CITY_NAME', '');

                    McsGeo.vardump("selectLocation", location.country_name, location.country_code);

                    break;
                case 'province':
                    this.setCookie('MCS_COUNTRY_CODE', location.country_code);
                    this.setCookie('MCS_COUNTRY_NAME', location.country_name);

                    this.setCookie('MCS_PROVINCE_CODE', location.province_code);
                    this.setCookie('MCS_PROVINCE_NAME', location.province_name);

                    this.setCookie('MCS_CITY_CODE', '');
                    this.setCookie('MCS_CITY_NAME', '');

                    McsGeo.vardump("selectLocation", location.province_name, location.province_code);

                    break;
                case 'city':
                    this.setCookie('MCS_COUNTRY_CODE', location.country_code);
                    this.setCookie('MCS_COUNTRY_NAME', location.country_name);

                    this.setCookie('MCS_PROVINCE_CODE', location.province_code);
                    this.setCookie('MCS_PROVINCE_NAME', location.province_name);

                    this.setCookie('MCS_CITY_CODE', location.city_code);
                    this.setCookie('MCS_CITY_NAME', location.city_name);

                    McsGeo.vardump("selectLocation", location.city_name, location.city_code);
            }

            this.setCookie('MCS_LOCATION_TYPE', location.type);
            this.setCookie('MCS_NOASK', 1);
        }

    }

    /**
     * Сохранение параметров в cookie
     * @param {String} cookie name
     * @param {String} cookie value
     */
    setCookie(name, value) {
        var exdate = new Date(), cookie, domain = window.mcs_cookie_domain ? window.mcs_cookie_domain : "";

        if (domain != "") {
            this.deleteCookie(name);
            value = window.encodeURIComponent ? window.encodeURIComponent(value) : value;
            exdate.setDate(exdate.getDate() + 30);

            cookie = name + "=" + value + "; expires=" + exdate.toUTCString() + ";domain={$domain};path=/";
            document.cookie = cookie.replace('{$domain}', domain);
            McsGeo.vardump("McsGeo setCookie", cookie.replace('{$domain}', domain));
            if (domain.substring(0, 1) == ".") {
                document.cookie = cookie.replace('{$domain}', domain.substring(1, domain.length));
                McsGeo.vardump("McsGeo setCookie", cookie.replace('{$domain}', domain.substring(1, domain.length)));
            }

        }
    }


    getCookie(name) {
        var matches = document.cookie.match(new RegExp("(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }


    deleteCookie(name) {
        var domain = window.mcs_cookie_domain ? window.mcs_cookie_domain : "";

        if (domain != "") {
            var cookie = name + "=;domain={domain};expires=Thu, 01 Jan 1970 00:00:01 GMT";
            McsGeo.vardump("McsGeo deleteCookie", cookie.replace('{domain}', domain));
            document.cookie = cookie.replace('{domain}', domain);
            if (domain.substring(0, 1) == ".") {
                McsGeo.vardump("McsGeo deleteCookie", cookie.replace('{domain}', domain.substring(1, domain.length)));
                document.cookie = cookie.replace('{domain}', domain.substring(1, domain.length));
            } else {
                McsGeo.vardump("McsGeo deleteCookie", cookie.replace('{domain}', "." + domain));
                document.cookie = cookie.replace('{domain}', "." + domain);
            }
        }
    }


    geolocation() {
        switch (window.mcs_baseip) {
            case 'yandexgeo':
                return this.yandex_geolocation();
            case 'ip-api':
                return this.ipApi_geolocation();
        }
        return null;
    }

};

const geo = new McsGeo();
export default geo
