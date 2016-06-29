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
$form = &JForm::getInstance('main', dirname(__FILE__) . '/form.xml');

use adamasantares\jxforms\JxField;
$mt = str_replace([' ','.'],'',microtime());

?>
<div>
    <?php
    $form->setFieldAttribute('cities', 'name', 'cities_'.$mt, 'Field');
    $field = $form->getField('cities_'.$mt, 'Field');
    ?>
    <?= $field->input ?>
    <?php
    $form->setFieldAttribute('value', 'name', 'value_'.$mt, 'Field');
    $field = $form->getField('value_'.$mt, 'Field');
    ?>
    <?= $field->input ?>
</div>

