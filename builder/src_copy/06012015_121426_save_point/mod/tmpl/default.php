<?php
defined('_JEXEC') or die('Restricted access');

// подлючаем файлы стилей и скриптов
$this->addScript($myUrl . 'default.js', 'text/javascript');
$this->addStyleSheet($myUrl . 'default.css');

// drop-down меню
?><div id="mycityselector" class="mycityselector<?= $params->get('text_before', '') ?>">
	<?= $params->get('text_before', '') ?>
	<a class="city" href="javascript:void(0)" title="Выбрать другой город"><?php echo $city; ?></a>
	<?= $params->get('text_after', '') ?>
	<div class="mycityselector-question" style="display:none;">Не ваш город?&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" class="close-q">x</a></div>
</div><?php

// Диалог выбора
?><div class="mycityselector-dialog" style="display:none;">
	<a class="close" href="javascript:void(0)" title=""></a>
	<div class="title">Пожалуйста, выберите ваш город</div>
	<div class="inner"><?php
		
		// шаблоны элементов, чтобы не ковырять логику при смене html кода
//		$tmplGroupBlock = '<div class="groups">{groups}</div>';
//		$tmplGroup = '<div class="group"><a class="{act}" href="javascript:void(0)" data-group="{groupnum}">{groupname}</a></div>';
//		$tmplCityBlock = '<div class="cities{hidden} group-{groupnum}">{cities}</div>';
//		$tmplCity = '<div class="city"><a class="link{act}" data-domain="{domain}" id="city-{citycode}" href="{url}" title="">{cityname}</a></div>';

        $html = '';
        $gHtml = '';
        $groups = true;
        if (isset($citiesDom['nogroup']) && count($citiesDom) == 1) {
            $groups = false; // когда не разделено по группам
        }
        $gi = 1;
        foreach ($citiesDom as $group => $cityDatas) { // цикл по группам
            $cHtml = '';
            $gHid = ' hidden';
            $gAct = '';
            foreach ($cityDatas as $cityData) { // цикл по городам
                $code = self::translit($cityData[0]);
                $act = '';
                $dm = $domain;
                // ?mycity используется в тех случаях, когда пользователь выключил скрипты,
                // или они не сработали в следствии ошибок в сторонних скриптах
                $url = 'http://' . $dm . '/?mycity=' . urlencode($cityData[0]);
                if ($city == $cityData[0]) {
                    $gAct = $act = ' active';
                    $gHid = '';
                }
                if (isset($cityData[1]) && trim($cityData[1]) != '') {
                    if (substr($cityData[1], 0, 1) != '/') {
                        $dm = $cityData[1] . '.' . $domain;
                        $url = 'http://' . $dm . '/';
                    } else {
                        // адрес страницы (например site.ru/omsk/)
                        $url = 'http://' . $dm . $cityData[1];
                    }
                }
                $cHtml .= str_replace(array('{act}', '{domain}', '{citycode}', '{url}', '{cityname}'), array($act, $dm, $code, $url, $cityData[0]), $tmplCity);
            }
            if ($groups) {
                $html .= str_replace(array('{hidden}', '{groupnum}', '{cities}'), array($gHid, $gi, $cHtml), $tmplCityBlock);
                $gHtml .= str_replace(array('{act}', '{groupnum}', '{groupname}'), array($gAct, $gi, $group), $tmplGroup);
            } else {
                $html .= $cHtml;
            }
            $gi++;
        }
        if ($groups) {
            $html = str_replace('{groups}', $gHtml, $tmplGroupBlock) . $html;
        }

	?></div>
</div>
