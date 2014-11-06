<?php
// @charset utf-8
// Расширенные параметры для модуля
// (внедряется в страницу настроек модуля посредством плагина)

// Joomla
define('_JEXEC', 1);
define('JPATH_BASE', realpath(dirname(__FILE__) . '/../..'));
require_once(JPATH_BASE . '/includes/defines.php');
require_once(JPATH_BASE . '/includes/framework.php');
JFactory::getApplication('site')->initialise();
$DB = JFactory::getDbo();

mb_internal_encoding('UTF-8');

$doc = JFactory::getDocument();
$myUrl = JURI::base() . 'modules/mod_mycityselector/';
$doc->addScript($myUrl . 'tablednd.js', 'text/javascript');

// ==============================================================================

// составляем список доступных шаблонов
$tepmlatesOptions = '<option value="default">Default</option>';
// определяем текущий шаблон
$current = 'default';
$cities_list = array('Москва', 'Санкт-Петербург');
$DB->setQuery("SELECT `params` FROM `#__modules` WHERE `module`='mod_mycityselector'");
$res = $DB->loadResult();
$params = new JRegistry();
if (!empty($res)) {
    $params->loadString($res);
    $current = $params->get('template');
    $cities_list = explode("\n", $params->get('cities_list'));
}
// составляем список существующих шаблонов
$myDir = realpath(dirname(__FILE__)) . '/templates/';
$tpls = glob($myDir . '*', GLOB_ONLYDIR);
sort($tpls);
foreach ($tpls as $tpl) {
    $tpl = pathinfo($tpl);
    $tplName = $tpl['filename'];
    if (is_file($myDir . $tplName . '/' . $tplName . '.tpl.php') && $tplName != 'default') {
        $optSel = ($tplName == $current) ? ' selected="selected"' : '';
        $tplTitle = str_replace(array('_', '-'), array(' ', ' '), $tplName);
        $tplTitle = mb_strtoupper(mb_substr($tplTitle, 0, 1)) . mb_substr($tplTitle, 1);
        $tepmlatesOptions .= '<option value="' . $tplName . '"' . $optSel . '>' . $tplTitle . '</option>';
    }
}

// формируем список городов в виде таблицы (здесь не должно быть переносов строк в тексте, иначе js не будет работать)
$cities = '<tr style="border-bottom: 1px solid gray"><th>Город</th><th title="Здесь можно указать какой\n'
    . 'поддомен или страница\nсоответствует указанному городу.\n'
    . 'Поддомен должен записывать без\nосновного домена. Например:\n'
    . '&#34moscow&#34\nбудет соответствовать поддомену\nmoscow.site.ru\n'
    . 'Адрес страницы, также не должен\nсодержать имени домена,\n'
    . 'но должен начинаться со слеша.">Subdomain/Page (?)</th>'
    . '<th><a href="javascipt:void(0)" class="add">'
    . '<img style="float:none;margin:0;position:relative;top:-2px;" src="/administrator/templates/hathor/images/menu/icon-16-new.png" alt=""/> Добавить</a>'
    . '</th>'
    . '</tr>';
foreach ($cities_list as $city) {
    $city = explode('=', $city);
    if (trim($city[0]) == '') {
        continue;
    }
    if (!isset($city[1])) {
        $city[1] = '';
    }
    $cities .= '<tr style="border-bottom: 1px solid gray"><td><input type="text" name="city[]" class="city" value="' . htmlspecialchars(trim($city[0])) . '" /></td>'
        . '<td><input type="text" name="sub[]" class="sub" value="' . htmlspecialchars(trim($city[1])) . '" /></td>'
        . '<td><a href="javascipt:void(0)" class="remove">'
        . '<img style="float:none;margin:0;position:relative;top:-2px;" src="/administrator/templates/hathor/images/menu/icon-16-delete.png" alt=""/> Удалить</a></td>'
        . '</tr>';
}

// ==============================================================================

include dirname(__FILE__) . '/ext-params.js';