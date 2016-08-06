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

use adamasantares\jxforms\JxField;

$this->addJsDeclaration('
        // todo init select2
');

?>
<div id="system-message-container"><?= $this->getMessage() ?></div>
<div id="j-sidebar-container" class="span2">
    <?= $sidebar ?>
</div>
<div id="j-main-container" class="span10 fields-page">
    <h3><?= JText::_('COM_MYCITYSELECTOR_FIELDS') ?></h3>
    <form action="index.php?option=<?= $this->getComponentName() ?>" method="post" name="adminForm" id="adminForm">

        <?= JxField::text($model->getFieldName('name'), JText::_('COM_MYCITYSELECTOR_FORM_TITLE_NAME'), $data['name'], [
            'id' => 'com_mycityselector_name',
            'required' => true,
            'inline' => true,
            'size' => 40,
        ]) ?>

        <?= JxField::radio($model->getFieldName('published'), JText::_('COM_MYCITYSELECTOR_FORM_TITLE_STATUS'), $data['published'], [
            'options' => [
                '0' => JText::_('COM_MYCITYSELECTOR_ITEM_UNPUBLISHED'),
                '1' => JText::_('COM_MYCITYSELECTOR_ITEM_PUBLISHED'),
            ],
            'inline' => true
        ]) ?>

        <div class="field-values">
            <?php
            // default value first
            foreach ($data['fieldValues'] as $fieldValue) {
                if ($fieldValue['default'] == 1) {
                    $key = empty($fieldValue['id']) ? '_' . (microtime(true) * 10000) . 'DEF' : $fieldValue['id']; // "DEF" is marker of default value
                    ?><div class="field-value row-fluid">
                        <div class="span3">
                            <input type="hidden" name="<?= $model->getFieldName('id') . "[{$key}]" ?>"
                            <input type="hidden" name="<?= $model->getFieldName('cities', 1) . "[{$key}][]" ?>" value="0"/>
                            <label><?= JText::_('COM_MYCITYSELECTOR_DEFAULTVALUE') ?></label>
                        </div>
                        <div class="span9">
                            <?= JxField::editor($model->getFieldName('value', 1) . "[{$key}]", '', $fieldValue['value'], [
                                'width' => '99%', 'height' => '100'
                            ]) ?>
                        </div>
                    </div><?php
                }
            }
            // other values
            foreach ($data['fieldValues'] as $fieldValue) {
                if ($fieldValue['default'] != 1) {
                    $key = empty($fieldValue['id']) ? '_' . (microtime(true) * 10000) : $fieldValue['id'];
                    ?><div class="field-value row-fluid">
                        <div class="span3">
                            <label>
                                <input type="hidden"
                                   name="<?= $model->getFieldName('id') . "[{$key}]" ?>"
                                   value="<?= $fieldValue['id'] ?>"/>
                                <?= JText::_('COM_MYCITYSELECTOR_CITIES_TITLE') ?>
                            </label>
                            <select name="<?= $model->getFieldName('cities', 1) . "[{$key}][]" ?>"
                                class="select2 fields-value" multiple="multiple">
                                <?php foreach ($fieldValue['cities'] as $city) { ?>
                                    <option selected value="<?= $city['id'] ?>"><?= $city['name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="span9">
                            <?= JxField::editor($model->getFieldName('value', 1) . "[{$key}]",
                                '', $fieldValue['value'], ['width' => '99%', 'height' => '100']) ?>
                        </div>
                    </div><?php
                }
            }
            ?>
            <button id="addform" onclick="return false;"><?= JText::_('COM_MYCITYSELECTOR_ADDFORM'); ?></button>
        </div>

        <input type="hidden" name="<?= $model->getFieldName('id') ?>" value="<?= $data['id'] ?>"/>

        <?= $this->formControllerName() ?>
        <?= $this->formOption() ?>
        <?= $this->formTask() ?>
        <?= $this->formToken() ?>
    </form>

</div>

