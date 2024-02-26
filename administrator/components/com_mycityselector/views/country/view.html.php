<?php
/**
 * @package     MyCitySelector
 * @subpackage  com_mycityselector
 *
 */

defined('_JEXEC') or die;

JLoader::register('MycityselectorViewDefault', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'view.php');

/**
 * MyCitySelector Countries View
 *
 * @since  1.5
 */
class MycityselectorViewCountry extends MycityselectorViewDefault
{
	public function display($tpl = null)
	{
		$this->form  = $this->get('Form');
		$this->item  = $this->get('Item');
		$this->state = $this->get('State');

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
		JToolbarHelper::title(JText::_('COM_MYCITYSELECTOR_NAME') . ' - ' . JText::_('COM_MYCITYSELECTOR_ITEM_ADDING'), 'big-ico');
		JToolbarHelper::apply('country.apply');
		JToolbarHelper::save('country.save');
		JToolbarHelper::save2new('country.save2new');
		JToolbarHelper::cancel('country.cancel');

		//$state = $this->get('State');
		$canDo = JHelperContent::getActions('com_mycityselector');
		//$user  = JFactory::getUser();

		// Get the toolbar object instance
		//$bar = JToolbar::getInstance('toolbar');

		JToolbarHelper::title(JText::_('COM_MYCITYSELECTOR_NAME'), 'big-ico');

		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_mycityselector');
		}


		$this->sidebar = MycityselectorHelper::getSidebar($this->_name);

	}


}
