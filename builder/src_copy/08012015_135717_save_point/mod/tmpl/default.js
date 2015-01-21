
(function() {


})();



    var timer = setInterval( function(){
                if(!window.jQuery) {
                    if (myCitySelector.readyCounter > 60) {
                        clearInterval(myCitySelector.timer);
                        console.log('My City Selector: Не найден ни один из требуемых фреймворков (jQuery или Mootools)');
                    }
                    myCitySelector.readyCounter++;
                    return;
                }
                clearInterval(myCitySelector.timer);
                myCitySelector.init();
            }, 50);



    var me = {
    /**
     * Инициализация
     */
    "init": function(){
        // перемещаем html код диалогового окна на самый верх к элементу body
        // и навешиваем события на кнопки и ссылки
        initDialog( jQuery );
        if (window.mcs_dialog == 1) {
            jQuery( '.mycityselector-dialog' ).css('display','block');
        } else if(window.mcs_dialog == 2) {
            jQuery( '.mycityselector .mycityselector-question' ).css('display','block');
            jQuery( '.mycityselector .mycityselector-question .close-q' ).on( 'click', function(){
                jQuery( '.mycityselector .mycityselector-question' ).css('display','none');
            } );
        }
    },


    /**
     * Инициализацтя диалогового окна
     */
    "initDialog": function($){




    },


    fn: function() {// myCitySelector_CreateDialogJQ($){
        // находим html-шаблон диалога
        var dlg = $('.mycityselector-dialog');
        if (dlg.length) {
            // перемещаем код диалога в корень тега <body>
            $("body").prepend(dlg);
            // кнопка закрытия диалога
            $('.mycityselector-dialog .close').on('click', function(){
                $('.mycityselector-dialog').css('display', 'none');
            });
            // при выборе города из диалога
            $('.mycityselector-dialog .link').on('click', function(e){
                e.preventDefault();
                // ставим куки
                var cityName = (typeof this.innerHTML == 'object') ? this.innerHTML[0] : this.innerHTML;
                myCitySelector.setCookie(cityName);
                // здесь проверяем, нужно ли сделать редирект
                var domain = $( this ).attr("data-domain");
                myCitySelector.checkRedirect(this, domain);
                $('.mycityselector-dialog .link').removeClass('active');
                $(this).addClass('active');
                $('#mycityselector .city').html( $(this).html() );
                var city = this.id.split('-')[1];
                myCitySelector.switchContent(city);
                $('.mycityselector-dialog').css('display', 'none');
                return false;
            } );
            // клик по группе
            $('.mycityselector-dialog .groups .group a').on('click', function(e){
                e.preventDefault();
                $('.mycityselector-dialog .cities').addClass('hidden');
                $('.mycityselector-dialog .group a').removeClass('active');
                var group = $(this).attr('data-group');
                $('.mycityselector-dialog .cities.group-' + group).removeClass('hidden');
                $(this).addClass('active');
            });
            // клик по названию текущего города
            $('#mycityselector .city').on('click', function(e){
                e.preventDefault();
                $('.mycityselector .mycityselector-question').css('display', 'none');
                $('.mycityselector-dialog').css('display', 'block');
            });
        }
    },

    "checkRedirect": function(link, domain){
        if (myCitySelector.isMootools) {
            // редирект на субдомен
            if (domain != window.mcs_cur_domain) {
                var thisUrl = window.location.href.toString().split(window.location.host);
                if (thisUrl.length == 2) {
                    thisUrl = thisUrl[1];
                } else {
                    thisUrl = "/";
                }
                var redirect = location.protocol + "//" + domain + thisUrl;
                location.href = redirect;
                // при редиректе не заменяем контент на текущей странице
                return false;
            }
            // редирект на страницу (?)
            var linkUrl = $$(link).get("href")[0].split(window.location.host);
            if (linkUrl.length == 2) {
                linkUrl = linkUrl[1].split("?");
                if (linkUrl.length == 1) {
                    myCitySelector.setCookie(link.innerHTML);
                    location.href = $$(link).get("href")[0];
                    return false;
                }
            }
        } else if(myCitySelector.isJQuery) {
            // редирект на субдомен
            if (domain != window.mcs_cur_domain) {
                var thisUrl = window.location.href.toString().split(window.location.host);
                if (thisUrl.length == 2) {
                    thisUrl = thisUrl[1];
                } else {
                    thisUrl = "/";
                }
                var redirect = location.protocol + "//" + domain + thisUrl;
                location.href = redirect;
                // при редиректе не заменяем контент на текущей странице
                return false;
            }
            // редирект на страницу (?)
            var linkUrl = jQuery(link).attr("href").split(window.location.host);
            if (linkUrl.length == 2) {
                linkUrl = linkUrl[1].split("?");
                if (linkUrl.length == 1) {
                    myCitySelector.setCookie(link.innerHTML);
                    location.href = jQuery(link).attr("href");
                    return false;
                }
            }
        }
    },

    "switchContent": function(city){
        city = city || 'false';
        var tmp, content;
        if (window.citySelectorContents) {
            if (myCitySelector.isMootools) {
                $$('.cityContent').set( 'html', '' ); // прячем контент предыдущего города
            } else if (myCitySelector.isJQuery) {
                jQuery('.cityContent').html( '' );
            }
            if( !window.citySelectorContents[city] && window.citySelectorContents.other ){
                city = 'other';
            }
            if( window.citySelectorContents[city] ){
                // отображаем контент выбранного города, если он есть
                for( var i=0; i<citySelectorContents[city].length; i++ ){
                    if( citySelectorContents[city][i] ){
                        tmp = citySelectorContents[city][i].split("=");
                        if (tmp[0] == "{{$cities-group}}") { // групповой блок
                            content = citySelectorContents[ 'cities-group' ][ tmp[1] ];
                        } else {
                            content = citySelectorContents[ city ][ i ];
                        }
                        if (myCitySelector.isMootools) {
                            $$('.city-' + city + '-' + i).set('html', content);
                        } else if (myCitySelector.isJQuery) {
                            jQuery('.city-' + city + '-' + i).html(content);
                        }
                    }
                }
            }
        }
    },

    "setCookie": function(city){
        var domain = (window.mcs_cookie_domain) ? window.mcs_cookie_domain : "";
        console.log(city, domain);
        if (domain != "") {
            var exdate = new Date();
            exdate.setDate(exdate.getDate() + 30);
            exdate = exdate.toUTCString();
            var value = ( window.encodeURIComponent ? window.encodeURIComponent(city) : city );
            var cook = "MCS_CITY_NAME=" + value + "; expires=" + exdate + ";domain=" + domain + ";path=/";
            document.cookie = cook;
            if (domain.substring(0,1) == ".") {
                domain = domain.substring(1, domain.length - 1);
                document.cookie = "MCS_CITY_NAME=" + value + "; expires=" + exdate + ";domain=" + domain + ";path=/";
            }
        }
    }

})();


