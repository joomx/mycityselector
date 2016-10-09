<?php
defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');

// Note: all installers instructions moved to admin/comp/mcs/installer
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