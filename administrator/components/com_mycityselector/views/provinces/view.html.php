<?php
/**
 * @package     MyCitySelector
 * @subpackage  com_mycityselector
 *
 */

defined('_JEXEC') or die;

JLoader::register('MycityselectorViewDefault', dirname(__DIR__) . '/default/view.php');

/**
 * MyCitySelector Provinces View
 *
 * @since  1.5
 */
class MycityselectorViewProvinces extends MycityselectorViewDefault
{
	public function display($tpl = null)
	{
		// Get data from the model.
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->state         = $this->get('State');
		$this->total         = $this->get('Total');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		//$state = $this->get('State');
		$canDo = JHelperContent::getActions('com_mycityselector');
		//$user  = JFactory::getUser();

		// Get the toolbar object instance
		//$bar = JToolbar::getInstance('toolbar');

		JToolbarHelper::title(JText::_('COM_MYCITYSELECTOR_NAME'), 'big-ico');

		if ($canDo->get('core.create'))
		{
			JToolbarHelper::addNew('province.add');
		}

		if ($canDo->get('core.edit'))
		{
			JToolbarHelper::editList('province.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::publish('provinces.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('provinces.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		}

		if ($canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'provinces.delete', 'JTOOLBAR_REMOVE');
		}

		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_mycityselector');
		}


//		$this->sidebar = MycityselectorHelper::getSidebar($this->_name);
		$this->sidebar = null;

	}


}
