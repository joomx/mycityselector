<?php
defined('_JEXEC') or die;

class MycityselectorController extends Joomla\CMS\MVC\Controller\BaseController
{

	public function display($cachable = false, $urlparams = [])
	{
		$document   = Joomla\CMS\Factory::getDocument();
		$viewType   = $document->getType();
		$viewName   = $this->input->get('view', $this->default_view);
		$viewLayout = $this->input->get('layout', 'default', 'string');

		$view = $this->getView($viewName, $viewType, '', ['base_path' => $this->basePath, 'layout' => $viewLayout]);
		$view->setModel($this->getModel('country'));
		$view->setModel($this->getModel('province'));
		$view->setModel($this->getModel('city'));

		parent::display();
	}

}
