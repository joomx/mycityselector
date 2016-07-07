<?php
/**
 * Default Template
 * Шаблон модуля mcs
 *
 * В данном шаблоне существует объект $this, который указывает
 * на экземпляр класса 'mcsModule' из файла 'modules/mod_mcs/mod_mcs.php'.
 * Поэтому здесь можно использовать вызов любых его методов, кроме '__construct()'.
 */
defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');


// загружаем jquery
$this->addJQuery();
// подлючаем файлы стилей и скриптов ($myUrl - это URL до директории, в которой находится текущий шаблон)
$this->addScript($this->variables['layoutUrl'] . 'default.js');
$this->addStyle($this->variables['layoutUrl'] . 'default.css');

// подключаем YandexGeoLocation
$this->addScript('https://api-maps.yandex.ru/2.1/?lang=ru_RU');

// TODO var_dump($citiesList); // этот массив может быть разным в зависимости от настроек модуля, нужно переписать шаблоны так, чтобы они учитывали это

// Drop-down меню
?>
<div class="mcs-module<?= $this->get('moduleclass_sfx') ?>">
    <?= $this->get('text_before') ?>
    <a class="city" href="javascript:void(0)" title="Выбрать другой город"><?= $this->variables['cityCode'] ?></a>
    <?= $this->get('text_after') ?>
    <div class="question" style="display:none;">Ваш город <span id="yaCity"></span>? &nbsp;&nbsp;&nbsp;&nbsp;<a
            href="javascript:void(0)" class="close">x</a>
        <div>
            <button id="mcs-button-yes"><?= JText::_('JYES') ?></button>
            <button id="mcs-button-no"><?= JText::_('JNO') ?></button>
        </div>
    </div>

</div><?php


// Диалог выбора города.
// При создании своей html разметки необходимо сохранить имена классов основных элементов (.mcs-dialog, .close и т.д.).
?>
<div
    class="mcs-dialog <?= $this->variables['cities_list_type'] == 1 ? 'has-groups' : '' ?>
<?= $this->variables['cities_list_type'] == 2 ? 'has-groups-countries' : '' ?>"
    style="display:none;">
    <a class="close" href="javascript:void(0)" title=""></a>
    <div class="title"><?= $this->get('dialog_title') ?></div>
    <div class="quick-search">
        <input type="text" placeholder="<?= JText::_('COM_MYCITYSELECTOR_SEARCH_HINT')?>">
    </div>
    <div class="inner">
        <?php
        switch ($this->variables['cities_list_type']) {
            case 0: //только города
                $cities = $this->variables['citiesList']['list'];
                $province = '';
                include($this->layoutCity);
                break;
            case 1: //регионы и города
                // если города раздлены по группам, выводим их в отдельный блок
                $country = '';
                $provinces = $this->variables['citiesList']['list'];
                include($this->layoutProvince);
                // города
                foreach ($this->variables['citiesList']['list'] as $province => $provinceData) {
                    $cities = $provinceData['list'];
                    include($this->layoutCity);
                }
                break;

            case 2: // страны регионы и города
                $countries = $this->variables['citiesList']['list'];
                include($this->layoutCountry);
                foreach ($this->variables['citiesList']['list'] as $country => $countryData) {
                    $provinces = $countryData['list'];
                    include($this->layoutProvince);

                }
                foreach ($this->variables['citiesList']['list'] as $country => $countryData) {
                    foreach ($countryData['list'] as $province => $provinceData) {
                        $cities = $provinceData['list'];
                        include($this->layoutCity);
                    }
                }

                break;
        }
        ?>


    </div>
</div>
