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

?>
<div id="j-sidebar-container" class="span2">
    <?= $sidebar ?>
</div>
<div id="j-main-container" class="span10">

    <h3><?= JText::_('COM_MYCITYSELECTOR_FIELDS') ?></h3>

    <div id="system-message-container"><?= $this->getMessage() ?></div>

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
            if (isset($data['fieldValues']) && (sizeof($data['fieldValues']) > 0)) { ?>
                <div>
                    <label><?= JText::_('COM_MYCITYSELECTOR_DEFAULTVALUE') ?></label>
                    <?php
                    $default = array_shift($data['fieldValues']);
                    $default['id'] = $default['id'] ? $default['id'] : str_replace([' ', '.'], '', microtime());
                    $form->setFieldAttribute('value', 'name', 'valuedefault_' . $default['id'], 'Field');
                    $field = $form->getField('valuedefault_' . $default['id'], 'Field');
                    $field->setValue($default['value']);
                    echo $field->input;
                    $form->setFieldAttribute('valuedefault_' . $default['id'], 'name', 'value', 'Field');
                    ?>
                </div>
                <?php
                foreach ($data['fieldValues'] as $fieldValue) {
                    ?>
                    <div>
                        <?php
                        $form = JForm::getInstance('main', dirname(__FILE__) . '/form.xml');
                        $form->setFieldAttribute('cities', 'name', 'cities_' . $fieldValue['id'], 'Field');

                        $field = $form->getField('cities_' . $fieldValue['id'], 'Field');
                        $field->setValue($fieldValue['cities']);
                        echo $field->input;
                        $form->setFieldAttribute('cities_' . $fieldValue['id'], 'name', 'cities', 'Field');

                        $form->setFieldAttribute('value', 'name', 'value_' . $fieldValue['id'], 'Field');
                        $field = $form->getField('value_' . $fieldValue['id'], 'Field');
                        $field->setValue($fieldValue['value']);
                        echo $field->input;
                        $form->setFieldAttribute('value_' . $fieldValue['id'], 'name', 'value', 'Field');
                        ?>
                    </div>
                    <?php

                }
            } else {
                ?>
                <div>
                    <?php
                    $id = str_replace([' ', '.'], '', microtime());
                    $form->setFieldAttribute('value', 'name', 'valuedefault_' . $id, 'Field');
                    $field = $form->getField('valuedefault_' . $id, 'Field');
                    ?>
                    <label><?= JText::_('COM_MYCITYSELECTOR_DEFAULTVALUE') ?></label>
                    <?= $field->input ?>
                </div>
                <?php
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

