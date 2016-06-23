(function (w, d) {

    var readyCounter = 0,
        $ = null,
        $overlay = null,
        $dialog = null,
        $module = null,
        yaCity = null,
        yaRegion = null;

    // check console
    if (!w.console) {
        w.console = {};
    }
    if (!w.console.log) {
        w.console.log = function () {
        };
    }
    if (!w.console.error) {
        w.console.error = w.console.log;
    }

    // onReady
    var timer = setInterval(function () {
        if (!w.jQuery) {
            if (readyCounter > 60) {
                clearInterval(timer);
                console.log("My City Selector: Не найден фреймворк jQuery");
            }
            readyCounter++;
            return;
        }
        clearInterval(timer);
        $ = jQuery;
        // инициализация
        $(function () {
            initialize();
            ymaps.ready(yandex_geolocation);
            // отображение окна или подсказки
/*            if (getCookie('MCS_NOASK') != 1) {
                if (w.mcs_dialog == 1) {
                    openDialog();
                } else if (w.mcs_dialog == 2) {
                    $(".question", $module).show(50);
                }
            }*/
        });
    }, 50);


    /**
     * Инициализация модуля.
     * Переносим диалог из позиции установки модуля в корень документа и
     * привязываем события в кнопкам и ссылкам
     */
    function initialize() {
        // находим html-шаблон диалога и модуля
        $dialog = $('.mcs-dialog');
        $module = $('.mcs-module');
        $overlay = $('<div></div>').addClass("mcs-overlay");
        if ($dialog.length && $module.length) {
            // => перемещаем код диалога из позиции модуля в корень документа
            $("body").prepend($dialog);
            $("body").prepend($overlay);
            // => навешиваем события
            // клик по названию текущего города
            $(".city", $module).on('click', openDialog);
            // . клик по кнопке закрыть на tooltip
            $(".question .close", $module).on('click', function () {
                $(".question", $module).hide(50);
                return false;
            });
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
            // . клик по городу из диалога
            $('.mcs-dialog .link').on('click', selectCity);
            // . клик по группе
            $('.mcs-dialog .groups .group a').on('click', selectGroup);
            console.log('MCS started');
        }
    }


    /*function yandex_geolocation(){
     // проверяем, нужно ли сделать запрос на yandex.geolocation
     if (w.mcs_yandexgeo && w.mcs_yandexgeo === true) {
     // пробуем получить доступ к geolocation
     console.log("MCS: try to use geolocation.");
     if (navigator.geolocation) {
     navigator.geolocation.getCurrentPosition(
     function(position){ // success
     console.log("MCS: latitude " + position.coords.latitude + ", longitude " + position.coords.longitude);
     // отправляем запрос на определение города по координатам
     $.ajax({
     "url": "/modules/mod_mycityselector/yandex-geo.php",
     "dataType": "json",
     "type": "post",
     "data": {
     "key": "sv84ts934pesgs037cw0bynh23z0-203c0-039c9ru",
     "lat": position.coords.latitude, // широта
     "lon": position.coords.longitude // долгота
     }
     }).done(function(data){
     if (typeof(data.error) != "undefined") {
     if (data.error == 0) {
     console.log('MSC: city was defined as ' + data.description + ", " + data.name);
     // пытаемся переключиться на полученный город
     autoSwitchToDetectedCity(data.name);
     } else if (data.error == 1) {
     console.error("MCS: wrong coordinates.");
     } else if (data.error == 2) {
     console.error("MCS: error on request to yandex.");
     } else {
     console.error("MCS: error on city detection.");
     }
     } else {
     console.error("MCS: error on city detection.");
     }
     }).fail(function(xhr, err){
     console.error("MCS: error", err);
     });
     },
     function(err){ // fail
     console.error("MCS: error " + err.code + ' - ' + err.message);
     }
     );
     } else {
     console.log("MCS: Geolocation is not supported by this browser.");
     }
     }
     }*/

    function yandex_geolocation() {
        if (ymaps.geolocation) {
            ymaps.geolocation.get({
                // Выставляем опцию для определения положения по ip
                provider: 'auto',
                // Автоматически геокодируем полученный результат.
                autoReverseGeocode: true
            }).then(function (result) {
                // Выведем в консоль данные, полученные в результате геокодирования объекта.
                yaCity = result.geoObjects.get(0).properties.get('metaDataProperty.GeocoderMetaData.AddressDetails.Country.AdministrativeArea.Locality.LocalityName');
                yaRegion = result.geoObjects.get(0).properties.get('metaDataProperty.GeocoderMetaData.AddressDetails.Country.AdministrativeArea.AdministrativeAreaName');
                console.log('Определен город: ' + yaCity);
                console.log('Определен регион: ' + yaRegion);
                if (getCookie('MCS_NOASK') != 1) {
                    if (w.mcs_dialog == 1) {
                        openDialog();
                    } else if (w.mcs_dialog == 2) {
                        $("#yaCity",$module).html(yaCity);
                        $(".question", $module).show(50);
                    }
                }
            })
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
                $groups = $(".groups .group", $dialog);
            if ($groups.length == 0) {
                $cities.removeClass("hidden").addClass("active");
            } else {
                $groups.removeClass("hidden");
                $cities.removeClass("hidden");
                $($groups[0]).addClass("active");
                $($cities[0]).addClass("active");
            }
        }
        return false;
    }


    function closeDialog() {
        $overlay.css('display', 'none');
        $dialog.css('display', 'none');
        return false;
    }


    /**
     * Функция получает навание города, определенного через yandex geolocation и пытается переключить на этот город,
     * если он есть в списке. При условии что не открыто окно выбора города и пользователь еще не успел сделать выбор.
     * @param {string} city Название города
     */
    function autoSwitchToDetectedCity(city) {
        if ($dialog.css('display') == 'none' && w.mcs_yandexgeo === true) {
            // ищем город в списке
            $(".link", $dialog).each(function (index, link) {
                if ($(link).data("city") == city) {
                    // переключаем город
                    selectCity.apply(link);
                    return false;
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
        var $link = $(this);
        var cityName = $link.attr("data-city");
        if (cityName.length > 0) {
            // => сохраняем в cookie название выбранного города
            //setCookie(cityName);
            setCookie('MCS_CITY_NAME', cityName);
            setCookie('MCS_NOASK', 1);
            // => выделяем выбранный город
            $(".link", $dialog).removeClass('active');
            $link.addClass('active');
            $(".city", $module).html(cityName);
            // => проверяем, нужно ли сделать редирект
            if (!tryRedirect(this)) { // this должен быть объектом ссылки, по котороый кликнули
                // => переключаем контент на лету
                var cityCode = $link.attr("id").split('-')[1];
                switchContent(cityCode);
                closeDialog();
                if (w.mcs_callback) {
                    mcs_callback();
                }
            }
        }
        return false;
    }


    /**
     * Переключает группы городов
     */
    function selectGroup() {
        $(".cities", $dialog).addClass('hidden'); // => прячем все города
        $(".group a", $dialog).removeClass('active'); // => сбрасываем активную группу
        $(this).addClass('active'); // => выделяем группу
        var group = $(this).attr('data-group'); // => читаем идентификатор выбранной группы
        $(".cities.group-" + group, $dialog).removeClass('hidden'); // => отображаем блок городов
        return false;
    }


    /**
     * Проверяет адрес ссылки выбранного города и делает редирект, если это необходимо
     * @param {Object} href Ссылка выбранного города
     */
    function tryRedirect(href) {
        var cityUrl = href.toString().split(href.host),
            cityUrl = (cityUrl.length == 2) ? cityUrl[1] : '/',
            thisUrl = w.location.href.toString().split(w.location.host);
        thisUrl = (thisUrl.length == 2) ? thisUrl[1] : '/';

        if (href.hostname != w.location.hostname) {
            // => если хосты отличаются то редирект на другой поддомен
            console.log('Редирект на ' + w.location.protocol + "//" + href.host + thisUrl);
            w.location.href = w.location.protocol + "//" + href.host + thisUrl;
            return true;
        } else {
            if (cityUrl.length > 1 && cityUrl != "/#") {
                console.log('Редирект на ' + w.location.protocol + "//" + w.location.host + cityUrl);
                // => редирект на специальную страницу города
                w.location.href = w.location.protocol + "//" + w.location.host + cityUrl;
                return true;
            }
        }

        return false;
    }


    /**
     * Переключает контент для выбранного города
     * @param {String} cityCode
     */
    function switchContent(cityCode) {
        cityCode = cityCode || 'false';

        if (w.citySelectorContents && cityCode) {
            var tmp, content;
            // => прячем контент предыдущего города
            $('.cityContent').html('');

            if (!w.citySelectorContents[cityCode] && w.citySelectorContents.other) {
                // если выбранный город не найден, то переключаем на контент для прочих городов
                cityCode = 'other';
            }
            if (w.citySelectorContents[cityCode]) {
                // => отображаем контент выбранного города, если он есть
                for (var i = 0; i < citySelectorContents[cityCode].length; i++) {
                    if (citySelectorContents[cityCode][i]) {
                        tmp = citySelectorContents[cityCode][i].split("=");
                        if (tmp[0] == "{{$cities-group}}") { // групповой блок
                            content = citySelectorContents['cities-group'][tmp[1]];
                        } else {
                            content = citySelectorContents[cityCode][i];
                        }
                        $(".city-" + cityCode + "-" + i).html(content);
                    }
                }
            }
        }
    }


    /**
     * Сохранение параметров в cookie
     * @param {String} cookie name
     * @param {String} cookie value
     */
    function setCookie(name, cookieval) {
        var domain = (window.mcs_cookie_domain) ? window.mcs_cookie_domain : "";
        if (domain != "") {
            var exdate = new Date();
            exdate.setDate(exdate.getDate() + 30);
            exdate = exdate.toUTCString();
            var value = ( window.encodeURIComponent ? window.encodeURIComponent(cookieval) : cookieval );
            var cookie = name + "=" + value + "; expires=" + exdate + ";domain={$domain};path=/";
            document.cookie = cookie.replace('{$domain}', domain);
            if (domain.substring(0, 1) == ".") {
                document.cookie = cookie.replace('{$domain}', domain.substring(1, domain.length - 1));
            }
        }
    }

    function getCookie(name) {
        var matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }


})(window, document);
