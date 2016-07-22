<?php
/**
 * MyCitySelector
 * @author Konstantin Kutsevalov
 * @version 2.0.0
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

/* @var $this adamasantares\jxmvc\JxView */
/* @var $listOrder string */
/* @var $listDirection string */
/* @var $items array */

JHtml::_('behavior.multiselect');

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
                        <?php //@devnote здесь я написал свой метод для создания сортируемых столбцов, так как стандартная сортировка не только ?>
                        <?php //@devnote завязана на многих скриптах, так еще и требует создания xml файла JForm, что окончательно меня взорвало :) ?>
                        <?= $this->sortingColumn('Ordering', 'ordering', $listOrder, $listDirection, $this->url('saveOrdering')) ?>
                    </th>
                    <th width="1%" class="center">
                        <?= JHtml::_('grid.checkall'); ?>
                    </th>
                    <th nowrap="nowrap">
                        <?= $this->sortingColumn(JText::_('COM_MYCITYSELECTOR_GRID_TITLE'), 'name', $listOrder, $listDirection) ?>
                    </th>
                    <th nowrap="nowrap">&nbsp</th>
                    <th nowrap="nowrap"><?= JText::_('COM_MYCITYSELECTOR_ITEMS_OPERATIONS') ?></th>
                    <th nowrap="nowrap">
                        <?= $this->sortingColumn('ID', 'id', $listOrder, $listDirection) ?>
                    </th>
                </tr>
            </thead>
            <tbody>
            <?php
            if (count($items) == 0) {
                ?><tr><td colspan="50" align="center"><b><?= JText::_('COM_MYCITYSELECTOR_ITEMS_NOT_FOUND') ?></b></td></tr><?php
            } else {
                foreach ($items as $i => $item) {
                    $id = $item['id'];
                    $isPublished = ($item['status'] == 1);
                    ?><tr class="item-row <?= ($i % 2 > 0) ? 'even' : 'odd' ?>">
                        <td class="order nowrap center" width="10px">
                            <?= $this->orderingRow($listOrder, $id, $item['ordering']) ?>
                        </td>
                        <td class="center">
                            <input type="checkbox" id="cb<?= $i ?>" name="cid[]" value="<?= $id ?>" onclick="Joomla.isChecked(this.checked);">
                        </td>
                        <td align="left">
                            <a href="<?= $this->url('update', ['id' => $id]) ?>" title=""><?= $item['name'] ?></a>
                        </td>
                        <td align="left">
                            <a href="<?= $this->url('index', ['country_id' => $id], 'province') ?>" title="">
                                <?= JText::_('COM_MYCITYSELECTOR_PROVINCES') ?>
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
                                        <a href="<?= $this->url('update', ['id' => $id]) ?>" title="">
                                            <span class="icon-edit"></span> <?= JText::_('COM_MYCITYSELECTOR_ITEM_EDIT') ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?= $this->url('index', ['country_id' => $id], 'province') ?>" title="">
                                            <span class="icon-forward-circle"></span> <?= JText::_('COM_MYCITYSELECTOR_PROVINCES') ?>
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
                        <td class="left" width="20px"><?= $id ?></td>
                    </tr><?php
                }
            }
            ?>
            </tbody>
        </table>
        <hr/>
        <div class="pagination"><?= $pagination ?></div>

        <div class="clr"></div>
        <?= $this->formFilterSorting($listOrder, $listDirection) ?>
        <?= $this->formOptions /* returns hidden inputs */ ?>
    </form>
</div>
