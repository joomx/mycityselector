<?php
defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

use joomx\mcs\plugin\helpers\McsData;

class MycityselectorControllerIpgeo extends \Joomla\CMS\MVC\Controller\BaseController {

    public function getLocation($cachable = false, $urlparams = [])
	{
		exit(json_encode(McsData::detectLocationFromIp()));
	}
}