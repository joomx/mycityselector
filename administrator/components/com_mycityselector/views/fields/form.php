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
JHtml::_('formbehavior.chosen', 'select');
$form = JForm::getInstance('main', dirname(__FILE__) . '/form.xml');

use adamasantares\jxforms\JxField;
$mt = str_replace([' ','.'],'',microtime());

?>
<div class="field-value">
    <?php
    // TODO я не уверен, но может попробовать это?
    //$editor =& JEditor::getInstance();
    //$params = array( 'smilies'=> '0' ,
    //    'style'  => '1' ,
    //    'layer'  => '0' ,
    //    'table'  => '0' ,
    //    'clear_entities'=>'0'
    //);
    //echo $editor->display( 'desc', '', '400', '400', '20', '20', false, $params );

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

