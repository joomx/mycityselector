<?php
/**
 * Sitemap Jen
 * @author Konstantin@Kutsevalov.name
 * @package    sitemapjen
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

// эта модель работает с настройками компонента
 
jimport('joomla.application.component.modellist');
jimport( 'joomla.filesystem.file' );


// имя класса модели составляется по следующему правилу: [название_комопнента]Model[название_модели]
// а имя файла должно совпадать с названием модели

class SitemapjenModelOptions extends JModelList {
	
	private $compId = 0;
	private $input = null;
	
	function __construct(){
		parent::__construct();
		$this->input = JFactory::getApplication()->input;
	}
	
	function getLastGen(){
		$qu = "SELECT `value` FROM `#__sitemapjen_options` WHERE `param`='last_update'";
		$this->_db->setQuery( $qu );
		return $this->_db->loadResult();
	}
	
	function getOptions(){
		// считываем текущие настройки
		$qu = "SELECT * FROM `#__sitemapjen_options`";
		$this->_db->setQuery( $qu );
		$rows = $this->_db->loadAssocList();
		$opt = array();
		foreach( $rows as $row ){
			$opt[ $row['param'] ] = $row['value'];
		}
		// проверяем список исключаемых адресов, если он пуст, сканируем robots.txt на наличие disallow
		if( trim($opt['ignore_list']) == '' ){
			$opt['ignore_list'] = $this->parseRobotstxt();
		}
		return $opt;	
	}
	
	
	private function parseRobotstxt(){
		$disallow = '';
		$path = str_replace( 'administrator', '', JPATH_BASE );
		if( is_file($path.'robots.txt') ){
			$cnt = file( $path.'robots.txt' );
			foreach( $cnt as $line ){
				$line = trim( $line );
				if( substr($line,0,9) == 'Disallow:' ){
					$v = trim( substr($line,9) );
					$v = rtrim( $v, '/' );
					if( $v != '/' ){
						$disallow .= $v."\n";
					}
				}
			}
		}
		return $disallow;
	}
	

	// сохранение настроек
	function saveOptions(){
		$this->_db->setQuery( "SELECT * FROM `#__sitemapjen_options`" );
		$rows = $this->_db->loadAssocList();
		foreach( $rows as $row ){
			if( substr($row['param'],0,5) == 'task_' ){  continue;  }
			$param = $this->input->getVar( $row['param'], '' );
			echo $param.'<br>';
			$param = $this->_db->escape( $param );
			echo $param.'<br>';
			$qu = "UPDATE `#__sitemapjen_options` SET `value`='{$param}' WHERE `param`='{$row['param']}'";
			$this->_db->setQuery( $qu );
			echo $qu.'<br>';
			echo '------------<br>';
			$this->_db->query();
		}
		return true;
	}
	
	
	function setOption( $name, $value ){
		$name = $this->_db->escape( $name );
		$value = $this->_db->escape( $value );
		$qu = "UPDATE `#__sitemapjen_options` SET `value`='{$value}' WHERE `param`='{$name}'";
		$this->_db->setQuery( $qu );
		$this->_db->query();
	}


}