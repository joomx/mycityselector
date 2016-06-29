<?php
defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');

class PlgSystemPlgMycityselectorInstallerScript {
	
	public function __construct($adapter){ }

	public function preflight($route, $adapter)
	{
		return true;
	}

	public function postflight($route, $adapter)
	{
		return true;
	}

	public function install($adapter)
	{
		// Set the big value for plugin ordering
        $qu = "UPDATE `#__extensions` SET `ordering`=9000, `enabled`=1"
            . " WHERE `element`='plgmycityselector' AND `type`='plugin'";
		JFactory::getDBO()->setQuery($qu)->execute();
		return true;
	}

	public function update($adapter)
	{
		return true;
	}

	public function uninstall($adapter)
	{
		return true;
	}

}