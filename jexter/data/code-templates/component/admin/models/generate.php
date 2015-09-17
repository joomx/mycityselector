<?php
/**
 * Sitemap Jen
 * @author Konstantin@Kutsevalov.name
 * @package    sitemapjen
 * @version 1.0 beta
 */
 
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.modellist');

// the class name starts with the name of the component (in our case 'hello'), followed by 'model', followed by the model name.
// имя класса модели составляется по следующему правилу: [название_комопнента]Model[название_модели]
// а имя файла должно совпадать с названием модели

class SitemapjenModelGenerate extends JModelList {
	
	function __construct(){
		parent::__construct();
	}
	
	function getLinksCount(){
		$this->_db->setQuery( "SELECT COUNT(*) AS `val` FROM `#__sitemapjen_links`" );
		return $this->_db->loadResult();
	}

}