<?php
/**
 * MyCitySelector
 * @author Konstantin Kutsevalov
 * @version 1.0.0
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
JHtml::_('formbehavior.chosen', 'select');
$form = JForm::getInstance('main', dirname(__FILE__) . '/form.xml');

use adamasantares\jxforms\JxField;
$mt = str_replace([' ','.'],'',microtime());

?>
<div class="field-value">
    <?php
    $form->setFieldAttribute('cities', 'name', 'cities_'.$mt, 'Field');
    $field = $form->getField('cities_'.$mt, 'Field');
    ?>
    <div class="cities">
        <?= $field->input; ?>
        <div class="control-buttons">
            <button class="delete-field-value" id="<?= $mt ?>" onclick="return false"><?= JText::_('COM_MYCITYSELECTOR_DELETE_VALUE') ?></button>
        </div>
    </div>
    <?php
    $form->setFieldAttribute('value', 'name', 'value_'.$mt, 'Field');
    $field = $form->getField('value_'.$mt, 'Field');
    ?>
    <div class="value">
    <?= $field->input ?>
    </div>
</div>

