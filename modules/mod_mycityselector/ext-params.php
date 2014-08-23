<?php
// @charset utf-8
// Расширенные параметры для модуля
// (внедряется в страницу настроек модуля посредством плагина)

// Joomla
define( '_JEXEC', 1 );
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../..') );
require_once( JPATH_BASE.'/includes/defines.php');
require_once( JPATH_BASE.'/includes/framework.php');
JFactory::getApplication('site')->initialise();
$DB = JFactory::getDbo();

mb_internal_encoding( 'UTF-8' );

// ==============================================================================

// составляем список доступных шаблонов
$tepmlatesOptions = '<option value="default">Default</option>';
// определяем текущий шаблон
$current = 'default';
$cities_list = array( 'Москва', 'Санкт-Петербург' );
$DB->setQuery( "SELECT `params` FROM `#__modules` WHERE `module`='mod_mycityselector'" );
$res = $DB->loadResult();
$params = new JRegistry();
if( !empty($res) ){
	$params->loadString( $res );
	$current = $params->get( 'template' );
	$cities_list = explode( "\n", $params->get('cities_list') );
}
// составляем список существующих шаблонов
$myDir = realpath( dirname(__FILE__) ) . '/templates/';
$tpls = glob( $myDir.'*', GLOB_ONLYDIR );
sort( $tpls );
foreach( $tpls as $tpl ){
	$tpl = pathinfo( $tpl );
	$tplName = $tpl['filename'];
	if( is_file($myDir.$tplName.'/'.$tplName.'.tpl.php') && $tplName != 'default' ){
		$optSel = ( $tplName == $current ) ? ' selected="selected"' : '';
		$tplTitle = str_replace( array('_','-'), array(' ',' '), $tplName );
		$tplTitle = mb_strtoupper( mb_substr($tplTitle,0,1) ) . mb_substr( $tplTitle, 1 );
		$tepmlatesOptions .= '<option value="'.$tplName.'"'.$optSel.'>'.$tplTitle.'</option>';
	}
}

// формируем список городов в виде таблицы (здесь не должно быть переносов строк в тексте, иначе js не будет работать)
$cities = '<tr style="border-bottom: 1px solid gray"><th>Город</th><th title="Здесь можно указать какой\n'
	.'поддомен или страница\nсоответствует указанному городу.\n'
	.'Поддомен должен записывать без\nосновного домена. Например:\n'
	.'&#34moscow&#34\nбудет соответствовать поддомену\nmoscow.site.ru\n'
	.'Адрес страницы, также не должен\nсодержать имени домена,\n'
	.'но должен начинаться со слеша.">Subdomain/Page (?)</th>'
	.'<th><a href="javascipt:void(0)" class="add"><img style="float:none;margin:0;position:relative;top:2px;" src="/administrator/templates/hathor/images/menu/icon-16-new.png" alt=""/> Добавить</a></th>'
.'</tr>';
foreach( $cities_list as $city ){
	$city = explode( '=', $city );
	if( trim($city[0]) == '' ){  continue;  }
	if( !isset($city[1]) ){
		$city[1] = '';
	}
	$cities .= '<tr style="border-bottom: 1px solid gray"><td><input type="text" name="city[]" class="city" value="'.htmlspecialchars(trim($city[0])).'" /></td>'
		.'<td><input type="text" name="sub[]" class="sub" value="'.htmlspecialchars(trim($city[1])).'" /></td>'
		.'<td><a href="javascipt:void(0)" class="remove"><img style="float:none;margin:0;position:relative;top:2px;" src="/administrator/templates/hathor/images/menu/icon-16-delete.png" alt=""/> Удалить</a></td>'
	.'</tr>';
}

// ==============================================================================

?>
if( window.addEvent ){
	window.addEvent( 'domready', function(){  myCitySelectorParams();  } );
}else{
	// каким по очереди будет загружен этот скрипт заранее не известно, поэтому ждем, пока не подгрузится mootools
	window.mcsint = setInterval( function(){
			if( window.addEvent ){
				clearInterval( window.mcsint );
				window.addEvent( 'domready', function(){  myCitySelectorParams();  } );
			}
		},
		50
	);
}

function myCitySelectorParams(){
	// Шаблон окна
	$$("#jform_params_template")[0].setProperty("type","hidden");
	// создаем выпадающий список
	( new Element( 'select', {
			"class": "mcs_select_template",
			"html": '<?php echo $tepmlatesOptions; ?>',
			"events": {
				"change": function(){
					var tpl = this.getSelected().get("value");
					$$("#jform_params_template")[0].set( "value", tpl );
				}
			}
		}
	) ).inject( $$("#jform_params_template")[0], 'after' );	
	
	// список городов
	$$("#jform_params_cities_list")[0].setStyle( "display","none" );
	var table = new Element( 'table', {
		"class" : "mcs_table_cities_list",
		"html" : '<?= $cities ?>',
		"style" : "width: 80%; border-top: 1px solid gray; border-collapse: collapse; margin-bottom: 12px;"
	} );
	table.inject( $$("#jform_params_cities_list")[0], 'before' );
	$$(".mcs_table_cities_list .remove").addEvent( 'click', function(){
		var tr = this.getParent("tr");
		var city = tr.getElement(".city").get("value");
		if( city.trim() == "" ){
			tr.destroy();
		}else{
			if( confirm("Удалить "+city+"?") ){  tr.destroy();  }
		}
		return false;
	} );
	$$(".mcs_table_cities_list tr .add").addEvent( "click", function(){ // добавление новой строки
		var tr = new Element( "tr", { // создаем
			"style": "border-bottom: 1px solid gray",
			"html": '<td><input type="text" name="city[]" class="city"/></td>' +
				'<td><input type="text" name="sub[]" class="sub"/></td>' +
				'<td><a href="javascipt:void(0)" class="remove"><img style="float:none;margin:0;position:relative;top:2px;" src="/administrator/templates/hathor/images/menu/icon-16-delete.png" alt=""/> Удалить</a></td>'
		} );
		tr.getElement(".remove").addEvent( 'click', function(){ // прикручиваем событие удаления
			var tr = this.getParent("tr");
			var city = tr.getElement(".city").get("value");
			if( city.trim() == "" ){
				tr.destroy();
			}else{
				if( confirm("Удалить "+city+"?") ){  tr.destroy();  }
			}
			return false;
		} );
		tr.inject( $$(".mcs_table_cities_list")[0], "bottom" );
	} );
	
	// привязываем событие к форме, чтобы подготовить список городов к сохранению
	$$("form#module-form")[0].addEvent( 'submit', function(){
		var lines = [];
		$$(".mcs_table_cities_list tr").each( function(el){
			var city = el.getElement(".city");
			if( !city ){  return;  }
			city = city.get("value");
			var sub = el.getElement(".sub").get("value");
			if( sub.trim() != "" ){
				lines.push( city + "=" + sub );
			}else{
				lines.push( city );
			}
		} );
		$$("#jform_params_cities_list")[0].set( "value", lines.join("\n") );
		return true;
	} );
}