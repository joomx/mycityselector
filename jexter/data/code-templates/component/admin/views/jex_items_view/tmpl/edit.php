<?php
/**
 * {jex_name}
 * @author {jex_author}
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

    <form action="index.php?option={jex_sysname}" method="post" name="adminForm" id="adminForm">

        <?= JxField::text('Item[name]', JText::_('T_JEX_FORM_TITLE_NAME'), $this->data['name'], [
            'id' => '{jex_sysname}_name',
            'required' => true,
            'inline' => true,
            'size' => 40,
        ]) ?>

        <div class="form">
            <div class="row-fluid">
                <div class="span9">
                    <?= JxField::editor('Item[content]', JText::_('T_JEX_FORM_TITLE_CONTENT'), $this->data['content'], [
                        'id' => '{jex_sysname}_content',
                        'required' => false
                    ]) ?>
                </div>
                <div class="span3">
                    <fieldset class="form-vertical">
                        <div class="control-group">
                            <div class="control-label">
                                <label for="{jex_sysname}_status"><?= JText::_('T_JEX_FORM_TITLE_STATUS') ?></label>
                            </div>
                            <div class="controls">
                                <select id="{jex_sysname}_status" name="Item[status]" class="chzn-color-state" size="1">
                                    <option value="1" selected="selected"><?= JText::_('T_JEX_ITEM_PUBLISHED') ?></option>
                                    <option value="0"><?= JText::_('T_JEX_ITEM_UNPUBLISHED') ?></option>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>

        <input type="hidden" name="option" value="com_hello" />
        <input type="hidden" name="task" value="">
        <?php echo JHtml::_('form.token'); ?>
    </form>

</div>

