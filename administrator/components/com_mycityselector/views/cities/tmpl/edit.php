<?php
/**
 * MyCitySelector
 * @author Konstantin Kutsevalov
 * @version 1.0.0
 *
 * @formatter:off
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

use adamasantares\JxForms\JxField;

?>
<div id="j-sidebar-container" class="span2">
    <?= $this->sidebar ?>
</div>
<div id="j-main-container" class="span10">

    <div id="system-message-container"></div>

    <form action="index.php?option=com_mycityselector" method="post" name="adminForm" id="adminForm">

        <?= JxField::text('Item[name]', JText::_('COM_MYCITYSELECTOR_FORM_TITLE_NAME'), $this->data['name'], [
            'id' => 'com_mycityselector_name',
            'required' => true,
            'inline' => true,
            'size' => 40,
        ]) ?>

        <div class="form">
            <div class="row-fluid">
                <div class="span9">
                    <?= JxField::editor('Item[content]', JText::_('COM_MYCITYSELECTOR_FORM_TITLE_CONTENT'), $this->data['content'], [
                        'id' => 'com_mycityselector_content',
                        'required' => false
                    ]) ?>
                </div>
                <div class="span3">
                    <?= JxField::radio('Item[status]', JText::_('COM_MYCITYSELECTOR_FORM_TITLE_STATUS'), $this->data['status'], [
                        'options' => [
                            '0' => JText::_('COM_MYCITYSELECTOR_ITEM_UNPUBLISHED'),
                            '1' => JText::_('COM_MYCITYSELECTOR_ITEM_PUBLISHED'),
                        ]
                    ]) ?>
                </div>
            </div>
        </div>

        <input type="hidden" name="option" value="com_hello" />
        <input type="hidden" name="task" value="">
        <?php echo JHtml::_('form.token'); ?>
    </form>

</div>

