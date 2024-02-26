<?php
defined('_JEXEC') or die;

class SqlqueryController extends \JControllerLegacy
{

	public function display($cachable = false, $urlparams = [])
	{
	    header('Location: /');
		parent::display();
	}

}
