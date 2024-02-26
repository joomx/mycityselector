<?php
/**
 * Orange Template
 *
 * @var $this MyCitySelectorModule
 * @var $layoutUrl string
 * @var $locationsList array[string]
 * @var $locationsListType int
 * @var $location string
 * @var $city string
 * @var $province string
 * @var $country string
 * @var $provinceCode string
 * @var $countryCode string
 * @var $locationName string
 */

defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');

use joomx\mcs\plugin\helpers\McsData;
use Joomla\CMS\Language\Text;

if (is_file(__DIR__ . '/custom.css')) {
    ?><link link="/modules/mod_mycityselector/tmpl/custom.css" rel="stylesheet" /><?php
}
?>
<div class="mcs-app">
    <mcs-base-component
        locations='<?= json_encode($locationsList) ?>'
        current-city-code='<?= $city ?>'
        current-province-code='<?= $province ?>'
        current-country-code='<?= $country ?>'
        current-location-name='<?= $locationName ?>'
        question-tooltip-question="<?= Text::_('MOD_MYCITYSELECTOR_IS_THIS_YOUR_CITY') ?>"
        question-tooltip-yes="<?= Text::_('JYES') ?>"
        question-tooltip-no="<?= Text::_('JNO') ?>"
        modal-header-title="<?= $this->get('dialog_title') ?>"
        modal-header-search-placeholder="<?= JText::_('MOD_MYCITYSELECTOR_SEARCH_HINT') ?>"
        text-before="<?= $text_before ?>"
        text-after="<?= $text_after ?>"
        allow-select-whole="<?= McsData::get('allow_select_whole') ?>"
    >
    </mcs-base-component>
</div>

