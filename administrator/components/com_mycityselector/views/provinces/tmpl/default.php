<?php


defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$clientId  = (int) $this->state->get('client_id', 0);
$user      = JFactory::getUser();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$saveOrder = ($listOrder == 'ordering');
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_mycityselector&task=provinces.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'provinceList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$colSpan = $clientId === 1 ? 8 : 10;
?>
<form action="<?php echo JRoute::_('index.php?option=com_mycityselector&view=provinces'); ?>" method="post" name="adminForm"
      id="adminForm">
	<?php if (!empty($this->sidebar)) : ?>
    <div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
		<?php else : ?>
        <div id="j-main-container">
			<?php endif; ?>
			<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
			<?php if ($this->total > 0) : ?>
                <table class="table table-striped" id="provinceList">
                    <thead>
                    <tr>
                        <th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('searchtools.sort', '', 'ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                        </th>
                        <th width="1%" class="nowrap center">
							<?php echo JHtml::_('grid.checkall'); ?>
                        </th>
                        <th width="1%" class="nowrap center" style="min-width:55px">
							<?php echo JHtml::_('searchtools.sort', 'Jpublished', 'published', $listDirn, $listOrder); ?>
                        </th>
                        <th class="title">
							<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'name', $listDirn, $listOrder); ?>
                        </th>
                        <th class="subdomain">
							<?php echo JHtml::_('searchtools.sort', 'COM_MYCITYSELECTOR_FORM_TITLE_SUBDOMAIN', 'subdomain', $listDirn, $listOrder); ?>
                        </th>
                        <th class="country">
		                    <?php echo JHtml::_('searchtools.sort', 'COM_MYCITYSELECTOR_COUNTRY', 'country', $listDirn, $listOrder); ?>
                        </th>
                        <th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
                        </th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <td colspan="<?php echo $colSpan; ?>">
							<?php echo $this->pagination->getListFooter(); ?>
                        </td>
                    </tr>
                    </tfoot>
                    <tbody>
					<?php foreach ($this->items as $i => $item) :
						$ordering = ($listOrder == 'a.ordering');
						$canCreate = $user->authorise('core.create', 'com_mycityselector');
						$canEdit = $user->authorise('core.edit', 'com_mycityselector.provinces.' . $item->id);
						$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
						$canChange = $user->authorise('core.edit.state', 'com_mycityselector.provinces.' . $item->id) && $canCheckin;
						?>
                        <tr class="row<?php echo $i % 2; ?>"
                            sortable-group-id="1" item-id="<?php echo $item->id; ?>">
                            <td class="order nowrap center hidden-phone">
								<?php
								$iconClass = '';
								if (!$canChange)
								{
									$iconClass = ' inactive';
								}
                                elseif (!$saveOrder)
								{
									$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::_('tooltipText', 'JORDERINGDISABLED');
								}
								?>
                                <span class="sortable-handler<?php echo $iconClass; ?>">
								<span class="icon-menu"></span>
									<?php if ($canChange && $saveOrder) : ?>
                                        <input type="text" style="display:none" name="order[]" size="5"
                                               value="<?php echo $item->ordering; ?>" class="width-20 text-area-order"/>
									<?php endif; ?>
                            </td>
                            <td class="center">
								<?php echo JHtml::_('grid.id', $i, $item->id); ?>
                            </td>
                            <td class="center">
                                <div class="btn-group">
									<?php // Check if extension is enabled
									?>
									<?php echo JHtml::_('jgrid.published', $item->published, $i, 'provinces.', $canChange, 'cb'); ?>
                                </div>
                            </td>
                            <td class="small">
                                <div class="name break-word">
									<?php if ($canEdit) : ?>
                                        <a href="<?php echo JRoute::_('index.php?option=com_mycityselector&task=province.edit&id=' . (int) $item->id); ?>"
                                           title="<?php echo $this->escape($item->name); ?>">
											<?php echo $this->escape($item->name); ?></a>
									<?php else : ?>
										<?php echo $this->escape($item->name); ?>
									<?php endif; ?>
                                </div>
                            </td>
                            <td class="small">
                                <div class="name break-word">
									<?php echo $this->escape($item->subdomain); ?>
                                </div>
                            </td>
                            <td class="small">
                                <div class="name break-word">
			                        <?php echo $this->escape($item->country); ?>
                                </div>
                            </td>
                            <td class="hidden-phone">
								<?php echo (int) $item->id; ?>
                            </td>
                        </tr>
					<?php endforeach; ?>
                    </tbody>
                </table>
			<?php endif; ?>

            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="boxchecked" value="0"/>
			<?php echo JHtml::_('form.token'); ?>
        </div>
</form>
