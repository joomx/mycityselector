<?php
defined('_JEXEC') or exit( 'Restricted access' );


function mod_mycityselector( &$params ){
	$se = DIRECTORY_SEPARATOR;
	$citiesList = explode( "\n", $params->get('cities_list',"Москва\nСанкт-Петербург") );

	// Формируем список городов и групп
	$cityGroup = 'nogroup';
	$citiesDom = array( 'nogroup'=>array() );
	foreach( $citiesList as $i => $v ){
		$v = trim( $v );
		if( !empty($v) ){
			if( substr($v,0,1) == '[' && substr($v,-1,1) == ']' ){
				// группа
				$cityGroup = trim( trim($v,'[]') );
				if( !isset($citiesDom[$cityGroup]) ){
					$citiesDom[ $cityGroup ] = array();
				}
				unset( $citiesList[$i] );
				continue;
			}
			$v = explode( '=', trim( $v ) );
			$citiesDom[ $cityGroup ][] = $v;
			$citiesList[ $i ] = $v[0]; // потом используется для кукисов
		}else{
			unset( $citiesList[$i] );
		}
	}
	if( count($citiesDom['nogroup']) == 0 ){
		unset( $citiesDom['nogroup'] );
	}
	
	// берем данные из плагина (он вызывается раньше модуля)
	global $MSC_CURRENT_CITY;
	global $MSC_BASE_DOMAIN;
	$city = $MSC_CURRENT_CITY;
	$domain = $MSC_BASE_DOMAIN;

    // здесь корректируем автоопределенный базовый домен, если он указан в настройках
    $paramDomain = $params->get('main_domain', '');
    if (!empty($paramDomain)) {
        $domain = $paramDomain;
    }

	if( empty($city) && empty($domain) ){
		echo 'MSC: Плагин не активирован или не установлен!';
	}

	// всякие дополняшки (используются в шаблоне)
	$moduleClassSfx = $params->get( 'moduleclass_sfx', '' );
	$textBefore = $params->get( 'text_before', '' );
	$textAfter = $params->get( 'text_after', '' );

	$doc = JFactory::getDocument();
	// $app = JFactory::getApplication();
	
	// шаблон, стили, скрипты
	$myUrl = JURI::base() . 'modules/mod_mycityselector/';
	$doc->addScript( $myUrl.'script.js', 'text/javascript' ); // системный скрипт
	
	$tplName = $params->get( 'template', 'default' );
	$tplDir = dirname(__FILE__) . $se . 'templates'. $se; // путь для проверки существования файлов
	$tplUrl = JURI::base() . 'modules/mod_mycityselector/templates/';
	// проверяем существование директории шаблона
	if( !is_dir($tplDir.$tplName) || !is_file($tplDir.$tplName.'.tpl.php') ){
		$tplName = 'default';
	}
	$tplUrl .= $tplName.'/';
	$tplDir .= $tplName.$se;
	// ищем css
	$css = glob( $tplDir.'*.css' );
	foreach( $css as $file ){
		$file = pathinfo( $file );
		$doc->addStyleSheet( $tplUrl.$file['basename'] );
	}
	// ищем js
	$js = glob( $tplDir.'*.js' );
	foreach( $js as $file ){
		$file = pathinfo( $file );
		$doc->addScript( $tplUrl.$file['basename'], 'text/javascript' );
	}
	// подгружаем шаблон
	include( $tplDir.$tplName.'.tpl.php' );
}


// транслит для названий, чтобы формировать идентификаторы для js
function mod_mycityselector_translit( $str ){
	$str = trim( $str );
	$letters = array(
		'й'=>'j', 'ц'=>'ts', 'у'=>'u', 'к'=>'k', 'е'=>'e', 'н'=>'n', 'г'=>'g', 'ш'=>'sh', 'щ'=>'sch',
		'з'=>'z', 'х'=>'h', 'ъ'=>'', 'ф'=>'f', 'ы'=>'y', 'в'=>'v', 'а'=>'a', 'п'=>'p', 'р'=>'r', 'о'=>'o',
		'л'=>'l', 'д'=>'d', 'ж'=>'zh', 'э'=>'e', 'я'=>'ya',	'ч'=>'ch', 'с'=>'s', 'м'=>'m', 'и'=>'i',
		'т'=>'t', 'ь'=>'', 'б'=>'b', 'ю'=>'yu', 'ё'=>'e',
		'Й'=>'j', 'Ц'=>'ts', 'У'=>'u', 'К'=>'k', 'Е'=>'e', 'Н'=>'n', 'Г'=>'g', 'Ш'=>'sh', 'Щ'=>'sch',
		'З'=>'z', 'Х'=>'h', 'Ъ'=>'', 'Ф'=>'f', 'Ы'=>'y', 'В'=>'v', 'А'=>'a', 'П'=>'p', 'Р'=>'r', 'О'=>'o',
		'Л'=>'l', 'Д'=>'d', 'Ж'=>'zh', 'Э'=>'e', 'Я'=>'ya', 'Ч'=>'ch', 'С'=>'s', 'М'=>'m', 'И'=>'i',
		'Т'=>'t', 'Ь'=>'', 'Б'=>'b', 'Ю'=>'yu', 'Ё'=>'e', ' '=>'_', '-'=>'_',','=>'_','?'=>'_', '!'=>'_',
		'/'=>'_', '('=>'_', ')'=>'_', '___'=>'_', '__'=>'_'
	);
	foreach( $letters as $key => $value ){
		$str = str_replace( $key, $value, $str );
	}
	$str = strtolower( $str );
	return $str;
}


// формирует html код диалогового окна с городами
function mod_mycityselector_cities_html( $city, $domain, $citiesDom, $tmplGroupBlock, $tmplGroup, $tmplCityBlock, $tmplCity ){
	// (псевдо)логика :D
	$html = '';
	$gHtml = '';
	$groups = true;
	if( isset($citiesDom['nogroup']) && count($citiesDom) == 1 ){
		$groups = false; // когда не разделено по группам
	}
	$gi = 1;
	foreach( $citiesDom as $group=>$cityDatas ){ // цикл по группам
		$cHtml = '';
		$gHid = ' hidden';
		$gAct = '';
		foreach( $cityDatas as $cityData ){ // цикл по городам
			$code = mod_mycityselector_translit( $cityData[0] );
			$act = '';
			$dm = $domain;
			// ?mycity используется в тех случаях, когда пользователь выключил скрипты,
			// или они не сработали в следствии ошибок в сторонних скриптах
			$url = 'http://'. $dm . '/?mycity='.urlencode( $cityData[0] );
			if( $city == $cityData[0] ){
				$gAct = $act = ' active';
				$gHid = '';
			}
			if( isset($cityData[1]) && trim($cityData[1]) != '' ){
				if( substr($cityData[1],0,1) != '/' ){
					$dm = $cityData[1].'.'.$domain;
					$url = 'http://'. $dm . '/';
				}else{
					// адрес страницы (например site.ru/omsk/)
					$url = 'http://'. $dm . $cityData[1];
				}
			}
			$cHtml .= str_replace( array('{act}','{domain}','{citycode}','{url}','{cityname}'),array($act,$dm,$code,$url,$cityData[0]), $tmplCity );
		}
		if( $groups ){
			$html .= str_replace( array('{hidden}','{groupnum}','{cities}'), array($gHid,$gi,$cHtml), $tmplCityBlock );
			$gHtml .= str_replace( array('{act}','{groupnum}','{groupname}'), array($gAct,$gi,$group), $tmplGroup );
		}else{
			$html .= $cHtml;
		}
		$gi++;
	}
	if( $groups ){
		$html = str_replace( '{groups}', $gHtml, $tmplGroupBlock ) . $html;
	}
	
	return $html;
}

// ===================================================

mod_mycityselector( $params ); // $params уже существует в текущем scope