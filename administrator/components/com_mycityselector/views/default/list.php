<?php
/**
 * MyCitySelector
 * @author Konstantin Kutsevalov
 * @version 1.0.0
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

/* @var $this adamasantares\jxmvc\JxView */
/* @var $listOrder string */
/* @var $listDirection string */
/* @var $items array */

JHtml::_('behavior.multiselect');

$count = count($items);

$saveOrderingUrl = 'index.php?option=' . $this->getComponentName() . '&task=saveOrderAjax&controller=' . $this->getControllerName() . '&tmpl=component';

?>
<div id="j-sidebar-container" class="span2">
    <?= $sidebar ?>
</div>
<div id="j-main-container" class="span10">

    <h3><?= JText::_('COM_MYCITYSELECTOR_COUNTRIES') ?></h3>
    <div id="system-message-container"><?= $this->getMessage() ?></div>

    <form action="index.php" method="post" id="adminForm" name="adminForm" class="admin-form <?= $this->getComponentName() ?>">
        <div class="pagination"><?= $pagination ?></div>
        <hr/>
        <table class="table" id="items-list-table">
            <thead>
                <tr>
                    <th width="1%" class="nowrap center hidden-phone">
                        <?php //@devnote sort($title, $order, $direction = 'asc', $selected = 0, $task = null, $new_direction = 'asc', $tip = '', $icon = null, $formName = 'adminForm') ?>
                        <?= JHtml::_('searchtools.sort', '', 'a.ordering', $listDirection, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                    </th>
                    <th width="1%" class="center">
                        <?= JHtml::_('grid.checkall'); ?>
                    </th>
                    <th nowrap="nowrap"><?= JText::_('COM_MYCITYSELECTOR_GRID_TITLE') ?></th>
                    <th nowrap="nowrap">&nbsp</th>
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
                    ?><tr class="item-row <?= ($i % 2 > 0) ? 'even' : 'odd' ?>" sortable-group-id="<?= $item['id'] ?>">
                        <td class="order nowrap center" width="10px">
                            <span class="sortable-handler hasTooltip" title="" data-original-title="Please sort by order to enable reordering">
                                <span class="icon-menu"></span>
                            </span>
                            <input type="text" style="display:none" name="order[]" size="5" value="<?= $item['ordering'] + 1 ?>" />
                        </td>
                        <td class="center">
                            <input type="checkbox" id="cb<?= $i ?>" name="cid[]" value="<?= $item['id'] ?>" onclick="Joomla.isChecked(this.checked);">
                        </td>
                        <td align="left">
                            <a href="index.php?option=<?= urlencode($this->getComponentName()) ?>&task=update&id=<?= $item['id'] ?>" title=""><?= $item['name'] ?></a>
                        </td>
                        <td align="left">
                            <a href="index.php?option=<?= urlencode($this->getComponentName()) ?>&controller=region&task=index&country_id=<?= $item['id'] ?>" title="">
                                <?= JText::_('COM_MYCITYSELECTOR_REGIONS') ?>
                            </a>
                        </td>
                        <td class="left" width="100px">
                            <div class="btn-group">
                                <a class="btn btn-micro hasTooltip" href="javascript:void(0);"
                                   onclick="return listItemTask('cb<?= $i ?>','<?= $isPublished ? 'unpublish' : 'publish' ?>')" title=""
                                   data-original-title="<?= $isPublished ? JText::_('COM_MYCITYSELECTOR_ITEM_PUBLISHED') : JText::_('COM_MYCITYSELECTOR_ITEM_UNPUBLISHED') ?>">
                                        <span class="icon-<?= $isPublished ? 'publish' : 'unpublish' ?>"></span>
                                </a>
                                <button data-toggle="dropdown" class="dropdown-toggle btn btn-micro"><span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="index.php?option=<?= urlencode($this->getComponentName()) ?>&controller=<?= urlencode($this->getControllerName()) ?>&task=update&id=<?= $item['id'] ?>" title="">
                                            <span class="icon-edit"></span> <?= JText::_('COM_MYCITYSELECTOR_ITEM_EDIT') ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.php?option=<?= urlencode($this->getComponentName()) ?>&controller=region&task=index&country_id=<?= $item['id'] ?>" title="">
                                            <span class="icon-forward-circle"></span> <?= JText::_('COM_MYCITYSELECTOR_REGIONS') ?>
                                        </a>
                                    </li>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="javascript:void(0)" onclick="listItemTask('cb<?= $i ?>', 'drop')">
                                            <span class="icon-delete color-red"></span> <?= JText::_('COM_MYCITYSELECTOR_ITEM_DELETE') ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                        <td class="left" width="20px"><?= $item['id'] ?></td>
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
        <?= $this->formControllerName() ?>
        <?= $this->formOption() ?>
        <?= $this->formTask() ?>
        <?= $this->formToken() ?>
    </form>
</div>
