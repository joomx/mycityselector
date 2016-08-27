<?php
/**
 * Default Template
 *
 * @var $this MyCitySelectorModule
 * @var $layoutUrl string
 * @var $citiesList array[string]
 * @var $cities_list_type int
 * @var $city string
 * @var $cityCode string
 * @var $layoutCity string
 * @var $layoutProvince string
 * @var $layoutCountry string
 */
defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');

$this->addJQuery();
$this->addScript($layoutUrl . 'default.js');
$this->addStyle($layoutUrl . 'default.css');

// YandexGeoLocation
$this->addScript('https://api-maps.yandex.ru/2.1/?lang=ru_RU');

// Drop-down menu
?>
<div class="mcs-module<?= $this->get('moduleclass_sfx') ?>">
    <?= $this->get('text_before') ?>
    <a class="city" href="javascript:void(0)" title="Выбрать другой город"><?= $cityCode ?></a>
    <?= $this->get('text_after') ?>
    <div class="question" style="display:none;"><?= JText::printf('COM_MYCITYSELECTOR_IS_THIS_YOUR_CITY', '<span id="yaCity"></span>') ?>&nbsp;&nbsp;&nbsp;<a
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
    class="mcs-dialog <?= $cities_list_type == 1 ? 'has-groups' : '' ?>
<?= $cities_list_type == 2 ? 'has-groups-countries' : '' ?>"
    style="display:none;">
    <a class="close" href="javascript:void(0)" title=""><?= JText::_('COM_MYCITYSELECTOR_CLOSE') ?></a>
    <div class="title"><?= $this->get('dialog_title') ?></div>
    <?php
    if ($cities_list_type == 2) {
        $countries = $citiesList['list'];
        include($layoutCountry);
    }
    ?>
    <div class="quick-search">
        <input type="text" placeholder="<?= JText::_('COM_MYCITYSELECTOR_SEARCH_HINT') ?>">
    </div>
    <div class="inner">
        <?php
        switch ($cities_list_type) {
            case 0: //только города
                $cities = $citiesList['list'];
                $province = '';
                ?>
                <div class="cities-wrapper full-width">
                    <?php
                    include($layoutCity);
                    ?>
                </div>
                <?php
                break;
            case 1: //регионы и города
                // если города раздлены по группам, выводим их в отдельный блок
                $country = '';
                $provinces = $citiesList['list'];
                include($layoutProvince);
                // города
                ?>
                <div class="cities-wrapper">
                    <div class="mcs-city-title"><?= JText::_('COM_MYCITYSELECTOR_CITY'); ?></div>
                    <?php
                    foreach ($citiesList['list'] as $province => $provinceData) {
                        $cities = $provinceData['list'];
                        include($layoutCity);
                    }
                    ?>
                </div>
                <?php
                break;
            case 2: // страны регионы и города
                foreach ($citiesList['list'] as $country => $countryData) {
                    $provinces = $countryData['list'];
                    include($layoutProvince);

                }
                ?>
                <div class="cities-wrapper">
                    <div class="mcs-city-title"><?= JText::_('COM_MYCITYSELECTOR_CITY'); ?></div>
                    <?php
                    foreach ($citiesList['list'] as $country => $countryData) {
                        foreach ($countryData['list'] as $province => $provinceData) {
                            $cities = $provinceData['list'];
                            include($layoutCity);
                        }
                    }
                    ?>
                </div>
                <?php
                break;
        }
        ?>
    </div>
</div>
