<?php
// Скрипт установки

class plgsystemplg_mycityselectorInstallerScript {
	
	// private $ver = '1.2.4';
	// private $domain = '';
	// private $code = 'mcs';
	
	public function __construct( $adapter ){
		// $this->domain = urlencode( $_SERVER['HTTP_HOST'] );
		// $v = explode( '.', $this->ver );
		// if( intval($v[1]) < 10 ){  $v[1] = '0'.$v[1];  }
		// if( intval($v[2]) < 10 ){  $v[2] = '0'.$v[2];  }
		// $this->ver = implode( '', $v );
		// $this->key = md5( $this->domain.' '.$this->code.' '.$this->ver );
	}

	public function preflight( $route,  $adapter ){
		return true;
	}

	public function postflight( $route,  $adapter ){
		return true;
	}

	public function install( $adapter ){
		// echo('plg_installer_install');
		// устанавливаем порядок запуска как можно больше, чтобы не конфликтовало с SEF и не портились ссылки
		$db = JFactory::getDBO();
		$db->setQuery( "UPDATE `#__extensions` SET `ordering`=1000 WHERE `element`='plg_mycityselector' AND `type`='plugin'" );
		$db->query();
		// @file_get_contents( 'http://api.kutsevalov.name/install?key='.$this->key.'&ver='.$this->ver.'&domain='.$this->domain.'&extension='.$this->code );
		return true;
	}

	public function update( $adapter ){
		// echo('plg_installer_update');
		return true;
	}

	public function uninstall( $adapter ){
		// @file_get_contents( 'http://api.kutsevalov.name/delete?key='.$this->key.'&ver='.$this->ver.'&domain='.$this->domain.'&extension='.$this->code );
		return true;
	}

}