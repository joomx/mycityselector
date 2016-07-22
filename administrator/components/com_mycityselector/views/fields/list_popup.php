<?php
/**
 * MyCitySelector
 * @author Konstantin Kutsevalov
 * @version 2.0.0
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

/* @var $this adamasantares\jxmvc\JxView */
/* @var $listDirection string */
/* @var $pagination string */
/* @var $items array */
/* @var $isSearch bool */

if (!$isSearch) {
?>
<div id="j-main-container" class="span12">
    <form id="fast-search-content" class="form-inline form-inline-header">
        <div class="control-group">
            <div class="control-label">
                <label for="query_string">
                    <?= JText::_('COM_MYCITYSELECTOR_FAST_SEARCH') ?>
                </label>
            </div>
            <div class="controls">
                <input id="query_string" name="query" type="text" class="" />
            </div>
        </div>
    </form>
    <div class="pagination"><?= $pagination ?></div>
    <hr/>
    <table class="table" id="items-list-table">
        <thead>
            <tr>
                <th nowrap="nowrap">ID</th>
                <th nowrap="nowrap">
                    <?= JText::_('COM_MYCITYSELECTOR_GRID_TITLE') ?>
                </th>
                <th nowrap="nowrap">&nbsp;</th>
            </tr>
        </thead>
<?php
}
?>
        <tbody>
        <?php
        if (count($items) == 0) {
            ?><tr><td colspan="50" align="center">
                <b><?= JText::_('COM_MYCITYSELECTOR_ITEMS_NOT_FOUND') ?></b>
            </td></tr><?php
        } else {
            foreach ($items as $i => $item) {
                $id = $item['id'];
                $name = str_replace(['[', ']', '"', "'"], ['', '', '', ''], $item['name']);
                ?><tr class="item-row <?= ($i % 2 > 0) ? 'even' : 'odd' ?>">
                    <td class="left" width="20px"><?= $id ?></td>
                    <td align="left">
                        <a href="#" title="<?= JText::_('COM_MYCITYSELECTOR_INSERT_CODE') ?>"
                           onclick="window.parent.insertMCS('[msc-<?= $id ?> <?= $name ?>]'); return false;">
                            <?= $item['name'] ?>
                        </a>
                    </td>
                    <td class="left" width="100px">
                        <a href="#" title="" onclick="window.parent.insertMCS('[msc-<?= $id ?> <?= $name ?>]'); return false;">
                            <?= JText::_('COM_MYCITYSELECTOR_INSERT_CODE') ?>
                        </a>
                    </td>
                </tr><?php
            }
        }
        ?>
        </tbody>
<?php
if (!$isSearch) {
?>
    </table>
    <hr/>
    <div class="pagination"><?= $pagination ?></div>
    <div class="clr"></div>
</div>
<?php
}
