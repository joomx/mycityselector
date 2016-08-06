<?php
/**
 * MyCitySelector
 * @author Konstantin Kutsevalov
 * @version 2.0.0
 *
 * @formatter:off
 *
 * @var $this \adamasantares\jxmvc\JxView
 * @var $sidebar string
 * @var $data array
 * @var $model CountryModel
 */
defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

jimport('joomla.form.field');
use adamasantares\jxforms\JxField;

$key = '_' . (microtime(true) * 10000);

?><div class="field-value row-fluid new-value">
    <div class="span3">
        <label>
            <index type="hidden" name="<?= $model->getFieldName('id') . "[{$key}]" ?>" value="<?= $data['id'] ?>"/>
            <?= JText::_('COM_MYCITYSELECTOR_CITIES_TITLE') ?>
        </label>
        <select name="<?= $model->getFieldName('cities', 1) . "[{$key}][]" ?>" class="select2 fields-value" multiple="multiple">
            <!-- ajax -->
        </select>
    </div>
    <div class="span9">
        <?= JxField::editor($model->getFieldName('value', 1) . "[{$key}]", '', $data['value'], [
            'width' => '99%', 'height' => '150px'
        ]) ?>
    </div>
</div><?php

