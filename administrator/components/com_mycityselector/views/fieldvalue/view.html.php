<?php
/**
 * @package     MyCitySelector
 * @subpackage  com_mycityselector
 *
 */

defined('_JEXEC') or die;

/**
 * MyCitySelector City View
 *
 * @since  1.5
 */
class MycityselectorViewFieldvalue extends JViewLegacy
{
	public function display($tpl = null)
	{
		$this->form  = $this->get('Form');
		$this->item  = $this->get('Item');
		$this->state  = $this->get('State');

		return parent::display($tpl);
	}
}
