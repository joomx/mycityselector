<?php
// Скрипт установки
defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');


class plgsystemplg_mycityselectorInstallerScript {
	
	public function __construct( $adapter ){ }

	public function preflight( $route,  $adapter ){
		return true;
	}

	public function postflight( $route,  $adapter ){
		return true;
	}

	public function install( $adapter ){
		// echo('plg_installer_install');
		// устанавливаем порядок запуска как можно больше, чтобы не конфликтовало с SEF и не портились ссылки
        $qu = "UPDATE `#__extensions` SET `ordering`=1000, `enabled`=1"
            . " WHERE `element`='plg_mycityselector' AND `type`='plugin'";
		JFactory::getDBO()->setQuery($qu)->execute();
		return true;
	}

	public function update( $adapter ){
		// echo('plg_installer_update');
		return true;
	}

	public function uninstall( $adapter ){
        // echo('plg_installer_uninstall');
		return true;
	}

}