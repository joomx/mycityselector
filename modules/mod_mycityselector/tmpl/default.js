(function (w, d) {

    var readyCounter = 0,
        $ = null,
        $overlay = null,
        $dialog = null,
        $module = null,
        yaCity = null,
        yaRegion = null;

    // for debug mode
    if (!w.console) w.console = {};
    if (!w.console.log) w.console.log = function () {
    };
    if (!w.console.error) w.console.error = w.console.log;
    function vardump() {
        (w.mcs_debug_mode && w.mcs_debug_mode == 1) && w.console.log(arguments);
    }


    // onReady
    var timer = setInterval(function () {
        if (!w.jQuery) {
            if (readyCounter > 60) {
                clearInterval(timer);
                vardump("My City Selector: Не найден фреймворк jQuery");
            }
            readyCounter++;
            return;
        }
        clearInterval(timer);
        $ = jQuery;
        // инициализация
        $(function () {
            initialize();
            if (w.mcs_yandexgeo == true) {
                ymaps.ready(yandex_geolocation);
            }
        });
    }, 50);


    /**
     * Инициализация модуля.
     * Переносим диалог из позиции установки модуля в корень документа и
     * привязываем события в кнопкам и ссылкам
     */
    function initialize() {
        vardump("MCS begin initialize");
        // находим html-шаблон диалога и модуля
        $dialog = $('.mcs-dialog');
        $module = $('.mcs-module');
        $overlay = $('<div></div>').addClass("mcs-overlay");
        if ($dialog.length && $module.length) {
            // => перемещаем код диалога из позиции модуля в корень документа
            $("body").prepend($dialog).prepend($overlay);
            // => навешиваем события
            // клик по названию текущего города
            $(".city", $module).on('click', openDialog);
            // . клик по кнопке закрыть на tooltip
            $(".question .close", $module).on('click', closePopUp);
            // . клик по кнопке да на tooltip
            $("#mcs-button-yes", $module).on('click', function () {
                autoSwitchToDetectedCity(yaCity)
                $(".question", $module).hide(50);
            });
            // . клик по кнопке нет на tooltip
            $("#mcs-button-no", $module).on('click', openDialog);
            // . кнопка закрытия диалога
            $('.close', $dialog).on('click', closeDialog);
            // . клик по затемнению
            $overlay.on('click', closeDialog);
            // . нажатие esc
            $(window).on("keyup", function (e) {
                if (e.keyCode == 27) {
                    closeDialog();
                }
            });
            $('.mcs-dialog .link').on('click', selectCity);
            $('.mcs-dialog .provinces a').on('click', selectGroup);
            $('.mcs-dialog .country a').on('click', selectCountry);
            $('.mcs-dialog .quick-search input').on('input', mcsQuickSearch);
            vardump('MCS started');
        } else {
            vardump('MCS dialog not found');
        }
    }


    function yandex_geolocation() {
        vardump("yandex_geolocation");
        if (location.protocol == 'https:') {
            if (ymaps.geolocation) {
                vardump("ymaps.geolocation send request");
                ymaps.geolocation.get({
                    // Выставляем опцию для определения положения по ip
                    provider: 'auto',
                    // Автоматически геокодируем полученный результат.
                    autoReverseGeocode: true
                }).then(function (result) {
                    // Выведем в консоль данные, полученные в результате геокодирования объекта.
                    yaCity = result.geoObjects.get(0).properties.get('metaDataProperty.GeocoderMetaData.AddressDetails.Country.AdministrativeArea.Locality.LocalityName');
                    yaRegion = result.geoObjects.get(0).properties.get('metaDataProperty.GeocoderMetaData.AddressDetails.Country.AdministrativeArea.AdministrativeAreaName');
                    vardump('Определен город: ' + yaCity);
                    vardump('Определен регион: ' + yaRegion);
                    if (getCookie('MCS_NOASK') != 1) {
                        if (w.mcs_dialog == 1) {
                            openDialog();
                        } else if (w.mcs_dialog == 2) {
                            $("#yaCity", $module).html(yaCity);
                            $(".question", $module).show(50);
                        }
                    }
                })
            } else {
                vardump("ymaps.geolocation not defined");
            }
        } else {
            vardump("ymaps.geolocation is enabled only for HTTPS");
        }
    }


    function openDialog() {
        $(".question", $module).css('display', 'none');
        $overlay.css('display', 'block');
        $dialog.css('display', 'block');
        // проверяем видима ли хоть одна группа городов (fix)
        if ($(".cities.active", $dialog).length == 0) {
            // делаем активной первую группу
            var $cities = $(".cities", $dialog),
                $groups = $(".provinces .province", $dialog);
            if ($groups.length == 0) {
                $cities.removeClass("hidden").addClass("active");
            } else {
                $groups.removeClass("hidden");
                $cities.removeClass("hidden");
                $($groups[0]).addClass("active");
                $($cities[0]).addClass("active");
            }
        }
        vardump("openDialog");
        return false;
    }


    function closeDialog() {
        vardump("closeDialog");
        $overlay.css('display', 'none');
        $dialog.css('display', 'none');
        return false;
    }


    function closePopUp() {
        vardump("closePopUp");
        setCookie('MCS_NOASK', 1);
        $(".question", $module).hide(50);
        return false;
    }


    /**
     * Функция получает навание города, определенного через yandex geolocation и пытается переключить на этот город,
     * если он есть в списке. При условии что не открыто окно выбора города и пользователь еще не успел сделать выбор.
     * @param {string} city Название города
     */
    function autoSwitchToDetectedCity(city) {
        vardump("autoSwitchToDetectedCity", city);
        if ($dialog.css('display') == 'none' && w.mcs_yandexgeo === true) {
            // ищем город в списке
            $(".link", $dialog).each(function (index, link) {
                if ($(link).data("city") == city) {
                    // переключаем город
                    selectCity.apply(link);
                    var domainIndex = location.host.indexOf(window.mcs_base_domain);
                    var city_code_current = location.host.substr(0, domainIndex - 1);
                    var city_code = $(link).data("code");
                    if (window.mcs_subdomain_cities && city_code_current != "" && window.mcs_default_city != city_code_current) {
                        //var href = window.location.href.replace(/(?:http:\/\/|https:\/\/)([^\/]+)\//, $(link).data("city")+'.'+window.mcs_base_domain);
                        if (window.mcs_default_city == city_code) {
                            var href = window.location.href.replace(city_code_current + '.' + window.mcs_base_domain, window.mcs_base_domain);
                        } else  {
                            var href = window.location.href.replace(city_code_current + '.' + window.mcs_base_domain, $(link).data("code") + '.' + window.mcs_base_domain);
                        }
                        window.location.href = href;

                    }
                }
            });
        }
    }


    /**
     * Запоминает выбранный город и запускает переключение контента
     * @returns {boolean}
     */
    function selectCity() {
        w.mcs_yandexgeo = false; // запрещаем геолокации автоматическое переключение города, так как пользователь уже сделал выбор
        var $link = $(this),
            cityCode = $link.data("code"),
            cityName = $link.data("city");
        if (cityName.length > 0) {
            // => сохраняем в cookie название выбранного города
            setCookie('MCS_CITY_CODE', cityCode);
            setCookie('MCS_CITY_NAME', cityName);
            setCookie('MCS_NOASK', 1);
            $(".link", $dialog).removeClass('active');
            $link.addClass('active');
            $(".city", $module).html(cityName);
        }
        vardump("selectCity", cityName, cityCode);
        // no need 'return false' !
    }


    /**
     * Переключает группы городов
     */
    function selectGroup() {
        $(".cities", $dialog).addClass('hidden'); // => прячем все города
        $(".province a", $dialog).removeClass('active'); // => сбрасываем активную группу
        $(".province", $dialog).removeClass('active');
        $(this).addClass('active'); // => выделяем группу
        $(this).parent().addClass('active'); // => выделяем группу
        var group = $(this).attr('data-group'); // => читаем идентификатор выбранной группы
        $(".cities.group-" + group, $dialog).removeClass('hidden'); // => отображаем блок городов
        vardump("selectGroup", $(this).text(), $(this).data("group"));
        return false;
    }


    /**
     * Переключает страны
     */
    function selectCountry() {
        $(".provinces", $dialog).addClass('hidden'); // Прячем все регионы
        $(".country a", $dialog).removeClass('active'); // => сбрасываем активную группу
        $(".country", $dialog).removeClass('active');
        $(this).addClass('active'); // => выделяем группу
        $(this).parent().addClass('active');
        vardump("selectCountry", $(this).text(), $(this).data("group"));
        var group = $(this).attr('data-group'); // => читаем идентификатор выбранной группы
        $(".provinces-" + group, $dialog).removeClass('hidden');
        return false;
    }


    function mcsQuickSearch() {
        var city, value = this.value.toLowerCase();
        vardump("mcsQuickSearch", value);
        if (value != '') {
            $(".countries", $dialog).addClass('hidden');
            $(".provinces", $dialog).addClass('hidden');
            $(".cities", $dialog).addClass('hidden');
            $(".city", $dialog).addClass('hidden');
            $(".city a.link", $dialog).each(function (index, data) {
                city = $(data).attr('data-city');
                if (city.toLowerCase().indexOf(value) != -1) {
                    $(data).parent().removeClass('hidden');
                    $(data).parent().parent().removeClass('hidden');
                }
            })
        } else {
            $(".countries", $dialog).removeClass('hidden');
            $($(".provinces", $dialog)[0]).removeClass('hidden');
            $(".cities", $dialog).addClass('hidden');
            $($(".cities", $dialog)[0]).removeClass('hidden');
            $(".city", $dialog).removeClass('hidden');
        }
    }


    /**
     * Сохранение параметров в cookie
     * @param {String} cookie name
     * @param {String} cookie value
     */
    function setCookie(name, value) {
        var exdate = new Date(), cookie,
            domain = (window.mcs_cookie_domain) ? window.mcs_cookie_domain : "";
        if (domain != "") {
            deleteCookie(name);
            value = window.encodeURIComponent ? window.encodeURIComponent(value) : value;
            exdate.setDate(exdate.getDate() + 30);
            cookie = name + "=" + value + "; expires=" + exdate.toUTCString() + ";domain={$domain};path=/";
            document.cookie = cookie.replace('{$domain}', domain);
            vardump("setCookie", cookie.replace('{$domain}', domain));
            if (domain.substring(0, 1) == ".") {
                document.cookie = cookie.replace('{$domain}', domain.substring(1, domain.length - 1));
                vardump("setCookie", cookie.replace('{$domain}', domain.substring(1, domain.length - 1)));
            }
        }
    }


    function getCookie(name) {
        var matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }


    function deleteCookie(name) {
        var domain = (window.mcs_cookie_domain) ? window.mcs_cookie_domain : "",
            cookie = name + "=;domain={domain};expires=Thu, 01 Jan 1970 00:00:01 GMT";
        vardump("deleteCookie", cookie.replace('{domain}', domain));
        document.cookie = cookie.replace('{domain}', domain);
        if (domain.substring(0, 1) == ".") {
            vardump("deleteCookie", cookie.replace('{domain}', domain.substring(1, domain.length - 1)));
            document.cookie = cookie.replace('{domain}', domain.substring(1, domain.length - 1));
        } else {
            vardump("deleteCookie", cookie.replace('{domain}', "." + domain));
            document.cookie = cookie.replace('{domain}', "." + domain);
        }
    }


})(window, document);
