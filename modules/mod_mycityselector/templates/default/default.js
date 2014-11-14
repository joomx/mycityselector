
// создает диалоговое окно выбора города и привязывает события к ссылкам
// эта функция вызывается из главного скрипта после загрузки страницы

function myCitySelector_CreateDialogJQ($){ // jQuery
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
			// здесь проверяем, нужно ли сделать редирект
			var domain = $( this ).attr("data-domain");
            myCitySelector.checkRedirect(this, domain);
			$('.mycityselector-dialog .link').removeClass('active');
			$(this).addClass('active');
			$('#mycityselector .city').html( $(this).html() );
			var city = this.id.split('-')[1];
            myCitySelector.switch(city, this.innerHTML);
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
}



function myCitySelector_CreateDialogMoo($$){ // Mootools
	// находим html-шаблон диалога
	var dlg = $$('.mycityselector-dialog');
	if (dlg.length) {
		// перемещаем код диалога в корень тега <body>
		dlg[0].inject($$('body')[0], 'top');
		// кнопка закрытия диалога
		$$('.mycityselector-dialog .close').addEvent('click', function(e){
			$$('.mycityselector-dialog').setStyle('display', 'none');
		} );
		// при выборе города из диалога
		$$('.mycityselector-dialog .link').addEvent('click', function(e){
			try{ new Event(e).stop(); }catch( err ){ }
			// здесь проверяем, нужно ли сделать редирект
			var domain = $$(this).get("data-domain")[0];
            myCitySelector.checkRedirect(this, domain);
			$$('.mycityselector-dialog .link').removeClass('active');
			$$(this).addClass('active');
			$$('#mycityselector .city').set('html', this.innerHTML);
			var city = this.id.split('-')[1];
            myCitySelector.switch(city, this.innerHTML);
			$$('.mycityselector-dialog').setStyle('display', 'none');
			return false;
		} );
		// клик по группе
		$$('.mycityselector-dialog .groups .group a').addEvent('click', function(e){
			try{ new Event(e).stop(); }catch( err ){ }
			$$('.mycityselector-dialog .cities').addClass('hidden');
			$$('.mycityselector-dialog .group a').removeClass('active');
			var group = $$(this).get('data-group');
			$$('.mycityselector-dialog .cities.group-' + group).removeClass('hidden');
			$$(this).addClass('active');
		} );
		// клик по названию текущего города
		$$('#mycityselector .city').addEvent('click', function(e){
			try{ new Event(e).stop(); }catch( err ){ }
			$$('.mycityselector .mycityselector-question').setStyle('display', 'none');
			$$('.mycityselector-dialog').setStyle('display', 'block');
		} );
	}
}