<?php
/**
 * {jex_name}
 * @author {jex_author}
 * @version 1.0.0
 *
 * @formatter:off
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

use adamasantares\jxforms\JxField;

?>
<div id="j-sidebar-container" class="span2">
    <?= $this->sidebar ?>
</div>
<div id="j-main-container" class="span10">

    <div id="system-message-container"></div>

    <form action="index.php?option={jex_sysname}" method="post" name="adminForm" id="adminForm">

        <?= JxField::text('_JEX_ITEM_MODEL_[name]', JText::_('T_JEX_FORM_TITLE_NAME'), $this->data['name'], [
            'id' => '{jex_sysname}_name',
            'required' => true,
            'inline' => true,
            'size' => 40,
        ]) ?>

        <div class="form">
            <div class="row-fluid">
                <div class="span9">
                    <?= JxField::editor('_JEX_ITEM_MODEL_[content]', JText::_('T_JEX_FORM_TITLE_CONTENT'), $this->data['content'], [
                        'id' => '{jex_sysname}_content',
                        'required' => false
                    ]) ?>
                </div>
                <div class="span3">
                    <?= JxField::radio('_JEX_ITEM_MODEL_[status]', JText::_('T_JEX_FORM_TITLE_STATUS'), $this->data['status'], [
                        'options' => [
                            '0' => JText::_('T_JEX_ITEM_UNPUBLISHED'),
                            '1' => JText::_('T_JEX_ITEM_PUBLISHED'),
                        ]
                    ]) ?>
                </div>
            </div>
        </div>

        <input type="hidden" name="option" value="{jex_sysname}" />
        <input type="hidden" name="task" value="">
        <?php echo JHtml::_('form.token'); ?>
    </form>

</div>

