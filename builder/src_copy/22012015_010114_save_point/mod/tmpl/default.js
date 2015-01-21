
(function(w, d) {

    var readyCounter = 0,
        $ = null,
        $overlay = null,
        $dialog = null,
        $module = null;

    // проверяем, нужно ли сделать запрос на yandex.geolocation
    if (w.mcs_yandexgeo) {
        // пробуем получить доступ к geolocation
        if (navigator.geolocation) {
            var sendYandexRequest = function(position){
                console.log("latitude", position.coords.latitude);
                console.log("longitude", position.coords.longitude);
                alert("geo!");
            };
            navigator.geolocation.getCurrentPosition(sendYandexRequest);
        } else {
            if (w.console && w.console.log) {
                w.console.log("MCS: Geolocation is not supported by this browser.");
            }
        }
    }

    // onReady
    var timer = setInterval( function(){
            if(!w.jQuery) {
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
            $(function(){
                initialize();
                // отображение окна или подсказки
                if (w.mcs_dialog == 1) {
                    openDialog();
                } else if(w.mcs_dialog == 2) {
                    $(".question", $module).show(50);
                }
            });
        }, 50);


    /**
     * Инициализация модуля.
     * Переносим диалог из позиции установки модуля в корень документа и
     * привязываем события в кнопкам и ссылкам
     */
    function initialize()
    {
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
            $(".question .close", $module).on( 'click', function(){
                $(".question", $module).hide(50);
                return false;
            } );
            // . кнопка закрытия диалога
            $('.close', $dialog).on('click', closeDialog);
            // . клик по затемнению
            $overlay.on('click', closeDialog);
            // . нажатие esc
            $(window).on("keyup", function(e){
                if (e.keyCode == 27) { closeDialog(); }
            });
            // . клик по городу из диалога
            $('.mcs-dialog .link').on('click', selectCity);
            // . клик по группе
            $('.mcs-dialog .groups .group a').on('click', selectGroup);
            if (w.console && w.console.log) {
                w.console.log('MCS started');
            }
        }
    }


    function openDialog()
    {
        $(".question", $module).css('display', 'none');
        $overlay.css('display', 'block');
        $dialog.css('display', 'block');
        return false;
    }


    function closeDialog()
    {
        $overlay.css('display', 'none');
        $dialog.css('display', 'none');
        return false;
    }


    /**
     * Запоминает выбранный город и запускает переключение контента
     * @returns {boolean}
     */
    function selectCity()
    {
        var $link = $(this);
        var cityName = $link.attr("data-city");
        if (cityName.length > 0) {
            // => сохраняем в cookie название выбранного города
            setCookie(cityName);
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
    function selectGroup()
    {
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
    function tryRedirect(href)
    {
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
            if (cityUrl.length > 1 && cityUrl != "/#"){
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
    function switchContent(cityCode){
        cityCode = cityCode || 'false';

        if (w.citySelectorContents && cityCode) {
            var tmp, content;
            // => прячем контент предыдущего города
            $('.cityContent').html('');

            if( !w.citySelectorContents[cityCode] && w.citySelectorContents.other ){
                // если выбранный город не найден, то переключаем на контент для прочих городов
                cityCode = 'other';
            }
            if( w.citySelectorContents[cityCode] ){
                // => отображаем контент выбранного города, если он есть
                for( var i=0; i<citySelectorContents[cityCode].length; i++ ){
                    if( citySelectorContents[cityCode][i] ){
                        tmp = citySelectorContents[cityCode][i].split("=");
                        if (tmp[0] == "{{$cities-group}}") { // групповой блок
                            content = citySelectorContents[ 'cities-group' ][ tmp[1] ];
                        } else {
                            content = citySelectorContents[ cityCode ][ i ];
                        }
                        $(".city-" + cityCode + "-" + i).html(content);
                    }
                }
            }
        }
    }


    /**
     * Сохранение текущего города в cookie
     * @param {String} cityName
     */
    function setCookie(cityName){
        var domain = (window.mcs_cookie_domain) ? window.mcs_cookie_domain : "";
        if (domain != "") {
            var exdate = new Date();
            exdate.setDate(exdate.getDate() + 30);
            exdate = exdate.toUTCString();
            var value = ( window.encodeURIComponent ? window.encodeURIComponent(cityName) : cityName );
            var cookie = "MCS_CITY_NAME=" + value + "; expires=" + exdate + ";domain={$domain};path=/";
            document.cookie = cookie.replace('{$domain}', domain);
            if (domain.substring(0,1) == ".") {
                document.cookie = cookie.replace('{$domain}', domain.substring(1, domain.length - 1));
            }
        }
    }


})(window, document);
