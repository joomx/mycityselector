<?php
/**
 * TODO походу просто доствточно воспользваться https://github.com/joomla-projects/joomla-browser#joomla-browser-codeception-module
 *
 */

out("=== Начало тестирования ===\n", "cyan");

out(" Тесты плагина:\n", "cyan");
require_once JPATH_BASE . '/plugins/system/plgmycityselector/helpers/McsContentHelper.php';
require_once JPATH_BASE . '/plugins/system/plgmycityselector/helpers/McsData.php';
require_once JPATH_BASE . '/plugins/system/plgmycityselector/helpers/McsLog.php';
require_once JPATH_BASE . '/plugins/system/plgmycityselector/helpers/geo.php';
out("  анализ меток ... ", "cyan");
// простые метки
$body = '<p>[city Омск]только</p> <p>Омск[/city]</p> <div>[mcs-10 проверка]</div>
    <b>[city !Москва]все кроме москвы[/city]</b>
';
$tags = McsContentHelper::parseMcsTags($body);
if (!empty($tags)) {
    if (count($tags) == 2) {
        out("успех\n", "cyan");

    } else {
        out("ОШИБКА (найдены не все метки)\n", "red");
    }
} else {
    out("ОШИБКА (метки не найдены)\n", "red");
}

// TODO ну и далее прочие методы