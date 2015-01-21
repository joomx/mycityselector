
window.myCitySelector = {

    "readyCounter": 0,

    "timer": null,

    "isJQuery": false,

    "isMootools": false,

    "onReady": function(){
        myCitySelector.timer = setInterval( function(){
                if (window.addEvent) {
                    myCitySelector.isMootools = true;
                } else if(window.jQuery) {
                    myCitySelector.isJQuery = true;
                } else {
                    if (myCitySelector.readyCounter > 60) {
                        clearInterval(myCitySelector.timer);
                        console.log('My City Selector: Не найден ни один из требуемых фреймворков (jQuery или Mootools)');
                    }
                    myCitySelector.readyCounter++;
                    return;
                }
                clearInterval(myCitySelector.timer);
                myCitySelector.init();
            }, 50
        );
    },

    "init": function(){
        if (myCitySelector.isMootools) {
            myCitySelector_CreateDialogMoo( $$ );
            if (window.mcs_dialog == 1) {
                $$( '.mycityselector-dialog' ).setStyle('display','block'); // отобразить окно для выбора города
            } else if (window.mcs_dialog == 2) {
                $$( '.mycityselector .mycityselector-question' ).setStyle('display','block'); // показать вопрос о смене города
                $$( '.mycityselector .mycityselector-question .close-q' ).addEvent( 'click', function( e ){
                    $$( '.mycityselector .mycityselector-question' ).setStyle('display','none');
                } );
            }
        } else if(myCitySelector.isJQuery) {
            myCitySelector_CreateDialogJQ( jQuery );
            if (window.mcs_dialog == 1) {
                jQuery( '.mycityselector-dialog' ).css('display','block');
            } else if(window.mcs_dialog == 2) {
                jQuery( '.mycityselector .mycityselector-question' ).css('display','block');
                jQuery( '.mycityselector .mycityselector-question .close-q' ).on( 'click', function(){
                    jQuery( '.mycityselector .mycityselector-question' ).css('display','none');
                } );
            }
        }
    },

    "checkRedirect": function(link, domain){

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

};

myCitySelector.onReady();