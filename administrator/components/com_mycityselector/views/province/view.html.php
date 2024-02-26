<?php
/**
 * @package     MyCitySelector
 * @subpackage  com_mycityselector
 *
 */

defined('_JEXEC') or die;

JLoader::register('MycityselectorViewDefault', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'view.php');

/**
 * MyCitySelector Province View
 *
 * @since  1.5
 */
class MycityselectorViewProvince extends MycityselectorViewDefault
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
		JToolbarHelper::apply('province.apply');
		JToolbarHelper::save('province.save');
		JToolbarHelper::save2new('province.save2new');
		JToolbarHelper::cancel('province.cancel');
        JToolbarHelper::title(JText::_('COM_MYCITYSELECTOR_NAME'), 'big-ico');

		$canDo = Joomla\CMS\Helper\ContentHelper::getActions('com_mycityselector');
		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_mycityselector');
		}

		$this->sidebar = MycityselectorHelper::getSidebar($this->_name);
	}


}
