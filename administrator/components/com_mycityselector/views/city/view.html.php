<?php
/**
 * @package     MyCitySelector
 * @subpackage  com_mycityselector
 *
 */

defined('_JEXEC') or die;

JLoader::register('MycityselectorViewDefault', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'view.php');

/**
 * MyCitySelector City View
 *
 * @since  1.5
 */
class MycityselectorViewCity extends MycityselectorViewDefault
{
	public function display($tpl = null)
	{
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');
        $errors = $this->get('Errors');
		if (count($errors)) {
			throw new Exception(implode("\n", $errors), 500);
		}
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		JToolbarHelper::title(JText::_('COM_MYCITYSELECTOR_NAME') . ' - ' . JText::_('COM_MYCITYSELECTOR_ITEM_ADDING'), 'big-ico');
		JToolbarHelper::apply('city.apply');
		JToolbarHelper::save('city.save');
		JToolbarHelper::save2new('city.save2new');
		JToolbarHelper::cancel('city.cancel');
		$canDo = JHelperContent::getActions('com_mycityselector');
		JToolbarHelper::title(JText::_('COM_MYCITYSELECTOR_NAME'), 'big-ico');
		if ($canDo->get('core.admin')) {
			JToolbarHelper::preferences('com_mycityselector');
		}
		$this->sidebar = MycityselectorHelper::getSidebar($this->_name);
	}

}
