<?php
/**
 * MyCitySelector
 * @author Konstantin Kutsevalov
 * @version 2.0.0
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');


class MycityselectorControllerCountry extends Joomla\CMS\MVC\Controller\FormController
{

	/**
	 * Add new item
	 */
	public function add()
	{
		$document = Joomla\CMS\Factory::getDocument();
		$viewName   = $this->input->get('view', 'country');
		$viewFormat = $document->getType();
		$lName   = $this->input->get('layout', 'default', 'string');
		if ($view = $this->getView($viewName, $viewFormat))
		{
			$model = $this->getModel($viewName);
			$view->setModel($model, true);
			$view->setLayout($lName);
			$view->document = $document;
			$view->display();
		}
		return $this;
	}

}