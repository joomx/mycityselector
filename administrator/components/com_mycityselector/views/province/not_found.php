<?php
/**
 * MyCitySelector
 * @author Konstantin Kutsevalov
 * @version 2.0.0
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

?>
<div id="j-sidebar-container" class="span2">
    <?= $this->sidebar ?>
</div>
<div id="j-main-container" class="span10">

    <div class="Mycityselector-error">
        <?= JText::_('COM_MYCITYSELECTOR_ITEM_NOT_FOUND') ?>
    </div>

</div>
