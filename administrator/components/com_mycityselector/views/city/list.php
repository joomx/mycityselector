<?php
/**
 * MyCitySelector
 * @author Konstantin Kutsevalov
 * @version 1.0.0
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

/* @var $this JViewLegacy */
/* @var $listOrder string */
/* @var $listDirection string */

JHtml::_('behavior.multiselect');

$count = count($this->items);

if ($listOrder == 'a.ordering') {
    $saveOrderingUrl = 'index.php?option=com_mycityselector&task=saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirection), $saveOrderingUrl);
}

?>
<div id="j-sidebar-container" class="span2">
    <?= $sidebar ?>
</div>
<div id="j-main-container" class="span10">
    <form action="index.php" method="post" name="adminForm" class="admin-form com_mycityselector" id="adminForm">
        <div class="pagination"><?= $pagination ?></div>
        <hr/>
        <table class="table">
        <thead>
            <tr>
                <th width="1%" class="nowrap center hidden-phone">
                    <?php echo JHtml::_('searchtools.sort', '', 'a.ordering', 0, '', null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                </th>
                <th width="1%" class="center">
                    <?php echo JHtml::_('grid.checkall'); ?>
                </th>
                <th nowrap="nowrap"><?= JText::_('COM_MYCITYSELECTOR_GRID_TITLE') ?></th>
                <th nowrap="nowrap"><?= JText::_('COM_MYCITYSELECTOR_ITEMS_OPERATIONS') ?></th>
                <th nowrap="nowrap">ID</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($count == 0) {
            ?><tr><td colspan="50" align="center"><b><?= JText::_('COM_MYCITYSELECTOR_ITEMS_NOT_FOUND') ?></b></td></tr><?php
        } else {
            foreach ($items as $i => $item) {
                $isPublished = ($item['status'] == 1);
                ?><tr class="item-row <?= ($i % 2 > 0) ? 'even' : 'odd' ?>">
                    <td class="order nowrap center" width="10px">
                        <span class="sortable-handler inactive tip-top hasTooltip" title=""
                              data-original-title="Please sort by order to enable reordering">
                            <span class="icon-menu"></span>
                        </span>
                    </td>
                    <td class="center">
                        <input type="checkbox" id="cb<?= $i ?>" name="cid[]" value="<?= $item['id'] ?>" onclick="Joomla.isChecked(this.checked);">
                    </td>
                <td align="left">
                    <a href="index.php?option=com_mycityselector&task=update&id=<?= $item['id'] ?>" title=""><?= $item['name'] ?></a>
                </td>
                    <td class="center">
                        <div class="btn-group">
                            <a class="btn btn-micro hasTooltip" href="javascript:void(0);"
                               onclick="return listItemTask('cb<?= $i ?>','<?= $isPublished ? 'unpublish' : 'publish' ?>')" title=""
                               data-original-title="<?= $isPublished ? JText::_('COM_MYCITYSELECTOR_ITEM_PUBLISHED') : JText::_('COM_MYCITYSELECTOR_ITEM_UNPUBLISHED') ?>">
                                    <span class="icon-<?= $isPublished ? 'publish' : 'unpublish' ?>"></span>
                            </a>
                            <button data-toggle="dropdown" class="dropdown-toggle btn btn-micro"><span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="index.php?option=com_mycityselector&task=update&id=<?= $item['id'] ?>" title="">
                                        <span class="icon-edit"></span> <?= JText::_('COM_MYCITYSELECTOR_ITEM_EDIT') ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" onclick="listItemTask('cb<?= $i ?>', 'drop')">
                                        <span class="icon-delete"></span> <?= JText::_('COM_MYCITYSELECTOR_ITEM_DELETE') ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                    <td class="left"><?= $item['id'] ?></td>
                </tr><?php
            }
        }
        ?>
        </tbody>
        </table>
        <hr/>
        <div class="pagination"><?= $pagination ?></div>

        <div class="clr"></div>
        <input type="hidden" name="boxchecked" value="0">
        <?= $this->formOption() ?>
        <?= $this->formTask() ?>
        <?= $this->formToken() ?>
    </form>
</div>