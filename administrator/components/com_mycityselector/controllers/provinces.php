<?php
/**
 * MyCitySelector
 * @author Konstantin Kutsevalov
 * @version 2.0.0
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');


class MycityselectorControllerProvinces extends Joomla\CMS\MVC\Controller\AdminController
{

	public function getModel($name = '', $prefix = '', $config = array())
	{
		return parent::getModel('Province');
	}

}