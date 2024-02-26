<?php
/**
 * @package     SqlQuery
 */

defined('_JEXEC') or die;

/**
 * Default View
 *
 * @since
 */
class SqlqueryViewDefault extends Joomla\CMS\MVC\View\HtmlView
{

	/**
	 * Display the view.
	 *
	 * @param   string  $tpl  Template
	 *
	 * @return  void
	 *
	 * @since   1.5
	 */
	public function display($tpl = null)
	{
		// Get data from the model.
		$state = $this->get('State');

		// Are there messages to display?
		$showMessage = false;

		if (is_object($state))
		{
			$message1    = $state->get('message');
			$message2    = $state->get('extension_message');
			$showMessage = ($message1 || $message2);
		}

		$this->showMessage = $showMessage;
		$this->state       = &$state;

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
        \JToolbarHelper::title(JText::_('COM_SQLQUERY'), 'big-ico');
        \JToolbarHelper::preferences('com_sqlquery');
        \JToolbarHelper::addNew('table.add');
	}

}
