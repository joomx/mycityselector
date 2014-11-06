// в версии 3.3 mootools больше не подключается к front-end :(

if( window.addEvent ){
	window.addEvent( 'domready', function(){  myCitySelectorInit('mt');  } );
}else if( window.jQuery ){
	jQuery( function(){  myCitySelectorInit('jq');  } );
}else{
	// каким по очереди будет загружен этот скрипт заранее не известно, поэтому ждем, пока не подгрузится mootools
    var myCitySelectorReady = 0;
	window.mcsint = setInterval( function(){
			if (window.addEvent) {
				clearInterval(window.mcsint);
				window.addEvent( 'domready', function(){  myCitySelectorInit('mt');  } );
			} else if(window.jQuery) {
                clearInterval(window.mcsint);
				jQuery( function(){  myCitySelectorInit('jq');  } );
			} else {
                if (myCitySelectorReady > 60) {
                    clearInterval(window.mcsint);
                    console.log('My City Selector: Не найден ни один из требуемых фреймворков (jQuery или Mootools)');
                }
            }
		}, 50
	);
}


function myCitySelectorInit( fw ){
	if( fw == "mt" ){
		myCitySelector_CreateDialogMoo( $$ );
		if( window.mcsdialog == 1 ){
			$$( '.mycityselector-dialog' ).setStyle('display','block'); // отобразить окно для выбора города
		}else if( window.mcsdialog == 2 ){
			$$( '.mycityselector .mycityselector-question' ).setStyle('display','block'); // показать вопрос о смене города
			$$( '.mycityselector .mycityselector-question .close-q' ).addEvent( 'click', function( e ){
				$$( '.mycityselector .mycityselector-question' ).setStyle('display','none');
			} );
		}
	}else if( fw == "jq" ){
		myCitySelector_CreateDialogJQ( jQuery );
		if( window.mcsdialog == 1 ){
			jQuery( '.mycityselector-dialog' ).css('display','block');
		}else if( window.mcsdialog == 2 ){
			jQuery( '.mycityselector .mycityselector-question' ).css('display','block');
			jQuery( '.mycityselector .mycityselector-question .close-q' ).on( 'click', function(){
				jQuery( '.mycityselector .mycityselector-question' ).css('display','none');
			} );
		}
	}
}


// переключает на новый город
function myCitySelector_switcher( city, title, fw ){
	city = city || 'false';
	var tmp, content;
	title = (typeof title == 'object') ? title[0] : title;
	if( window.citySelectorContents ){
		if( fw == "mt" ){
			$$('.cityContent').set( 'html', '' ); // прячем контент предыдущего города
		}else{
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
					if( tmp[0] == "{{$cities-group}}" ){ // групповой блок
						content = citySelectorContents[ 'cities-group' ][ tmp[1] ];
					}else{
						content = citySelectorContents[ city ][ i ];
					}
					if( fw == "mt" ){
						$$( '.city-'+city+'-'+i ).set( 'html', content );
					}else{
						jQuery( '.city-'+city+'-'+i ).html( content );
					}
				}
			}
		}
	}
	myCitySelector_setCookie( title );
}


// сохранение названия города в кукисах
function myCitySelector_setCookie( city ){
	var name = 'mycity_selected_name';
	var domain = (window.msc_cookie_domain) ? window.msc_cookie_domain : "";
	if( domain != "" ){
		var exdate = new Date();
		exdate.setDate( exdate.getDate() + 30 );
		exdate = exdate.toUTCString();
		var value = ( window.encodeURIComponent ? window.encodeURIComponent(city) : city );
		var cook = "mycity_selected_name=" + value + "; expires=" + exdate + ";domain=" + domain + ";path=/";
		// console.log( 'document.cookie = "' + cook + '";' );
		document.cookie = cook;
		if( domain.substring(0,1) == "." ){
			domain = domain.substring( 1, domain.length-1 );
			document.cookie = "mycity_selected_name=" + value + "; expires=" + exdate + ";domain=" + domain + ";path=/";
		}
	}
}