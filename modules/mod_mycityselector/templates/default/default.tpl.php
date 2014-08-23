<?php
defined('_JEXEC') or die('Restricted access');

// drop-down меню
?><div id="mycityselector" class="mycityselector<?php echo $moduleClassSfx; ?>">
	<?php echo $textBefore; ?>
	<a class="city" href="javascript:void(0)" title="Выбрать другой город"><?php echo $city; ?></a>
	<?php echo $textAfter; ?>
	<div class="mycityselector-question" style="display:none;">Не ваш город?&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" class="close-q">x</a></div>
</div><?php

// Диалог выбора
?><div class="mycityselector-dialog" style="display:none;">
	<a class="close" href="javascript:void(0)" title=""></a>
	<div class="title">Пожалуйста, выберите ваш город</div>
	<div class="inner"><?php
		
		// шаблоны элементов, чтобы не ковырять логику при смене html кода
		$tmplGroupBlock = '<div class="groups">{groups}</div>';
		
		$tmplGroup = '<div class="group"><a class="{act}" href="javascript:void(0)" data-group="{groupnum}">{groupname}</a></div>';
		
		$tmplCityBlock = '<div class="cities{hidden} group-{groupnum}">{cities}</div>';
		
		$tmplCity = '<div class="city"><a class="link{act}" data-domain="{domain}" id="city-{citycode}" href="{url}" title="">{cityname}</a></div>';
		
		// эта функция генерирует список и группы на основе заданных параметров
		echo mod_mycityselector_cities_html( $city, $domain, $citiesDom, $tmplGroupBlock, $tmplGroup, $tmplCityBlock, $tmplCity );

	?></div>
</div>
