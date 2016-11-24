<?php
/**
 * MyCitySelector
 * @author Konstantin Kutsevalov
 * @version 2.0.0
 *
 * @formatter:off
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

JHtml::_('formbehavior.chosen', 'select');

use adamasantares\jxforms\JxField;

?>
<div id="j-sidebar-container" class="span2">
    <?= $sidebar ?>
</div>
<div id="j-main-container" class="span10">

    <h3><?= JText::_('COM_MYCITYSELECTOR_CITY') ?></h3>

    <div id="system-message-container"><?= $this->getMessage() ?></div>

    <form action="index.php?option=<?= $this->getComponentName() ?>" method="post" name="adminForm" id="adminForm">

        <?= JxField::text($model->getFieldName('name'), JText::_('COM_MYCITYSELECTOR_FORM_TITLE_NAME'), $data['name'], [
            'id' => 'com_mycityselector_name',
            'required' => true,
            'inline' => true,
            'size' => 40,
        ]) ?>

        <?= JxField::text($model->getFieldName('subdomain'), JText::_('COM_MYCITYSELECTOR_FORM_TITLE_SUBDOMAIN'), $data['subdomain'], [
            'id' => 'com_mycityselector_subdomain',
            'required' => true,
            'inline' => true
        ]) ?>

        <?= JxField::radio($model->getFieldName('status'), JText::_('COM_MYCITYSELECTOR_FORM_TITLE_STATUS'), $data['status'], [
            'options' => [
                '0' => JText::_('COM_MYCITYSELECTOR_ITEM_UNPUBLISHED'),
                '1' => JText::_('COM_MYCITYSELECTOR_ITEM_PUBLISHED'),
            ],
            'inline' => true
        ]) ?>


        <div class="form-inline form-inline-header">
            <div class="control-label">
                <label><?= JText::_('COM_MYCITYSELECTOR_COUNTRY') ?></label>
            </div>
            <div class="controls">
                <?=
                JHtml::_('select.genericlist', $data['countries'], $model->getFieldName('country_id'), null, 'value', 'text', $data['country_id']);
                ?>
            </div>
        </div>

        <div class="form-inline form-inline-header">
            <div class="control-label">
                <label><?= JText::_('COM_MYCITYSELECTOR_PROVINCE') ?></label>
            </div>
            <div class="controls">
                <?=
                JHtml::_('select.genericlist', $data['provinces'], $model->getFieldName('province_id'), null, 'value', 'text', $data['province_id']);
                ?>
            </div>
        </div>
        <input type="hidden" name="<?= $model->getFieldName('id') ?>" value="<?= $data['id'] ?>"/>

        <?= $this->formControllerName() ?>
        <?= $this->formOption() ?>
        <?= $this->formTask() ?>
        <?= $this->formToken() ?>
    </form>

</div>

