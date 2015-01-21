<?php
// Скрипт установки
defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');


class mod_mycityselectorInstallerScript {
	
	public function __construct( $adapter ){
	}

	public function preflight( $route,  $adapter ){
        echo 'mod_preflight';
		return true;
	}

	public function postflight( $route,  $adapter ){
        echo 'mod_postflight';
		return true;
	}

	public function install( $adapter ){
        echo 'mod_install';
		return true;
	}

	public function update( $adapter ){
        echo 'mod_update';
		return true;
	}

	public function uninstall( $adapter ){
        echo 'mod_uninstall';
		return true;
	}

}