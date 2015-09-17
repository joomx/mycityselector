<?php
/**
 * Sitemap Jen
 * @author Konstantin@Kutsevalov.name
 * @package    sitemapjen
 */
 
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.modellist');

// имя класса модели составляется по следующему правилу: [название_комопнента]Model[название_модели]
// а имя файла должно совпадать с названием модели

class SitemapjenModelDefault extends JModelList {

	private $limit = 500;
	private $input = null;

	function __construct(){
		parent::__construct();
		$this->input = JFactory::getApplication()->input;
	}
	
	public function getLinks(){
		$page = intval( $this->input->getCmd('page',0) );
		$start = $this->limit * $page;
		$qu = "SELECT * FROM `#__sitemapjen_links` ORDER BY LENGTH(`loc`) ASC, `loc` ASC LIMIT {$start},{$this->limit}";
		$this->_db->setQuery( $qu );
		$rows = $this->_db->loadAssocList();
		return $rows;
	}
	
	public function getPagination(){
		$html = '';
		$page = intval( $this->input->getCmd('page',0) );
		$this->_db->setQuery( "SELECT COUNT(*) AS `val` FROM `#__sitemapjen_links`" );
		$count = $this->_db->loadResult();
		if( $count > 0 ){
			$url = $_SERVER['REQUEST_URI'];
			if( strpos($url,'?') === false ){
				$url .= '?';
			}else{
				$url .= '&';
			}
			$pages = intval( $count / $this->limit );
			if( $count % $this->limit > 0 ){
				$pages++;
			}
			for( $i=0; $i<$pages; $i++ ){
				if( $page == $i ){
					$html .= '<b><a href="'.$url.'page='.$i.'">'.($i+1).'</a></b> &nbsp;';
				}else{
					$html .= '<a href="'.$url.'page='.$i.'">'.($i+1).'</a> &nbsp;';
				}
			}
			$html .= '<br/>Всего '.$count.' записей';
		}
		return $html;
	}
	
	// добавляет адреса в игнор-лист и удаляет их из базы
	public function addIgnore( $ids, $list ){
		if( is_array($ids) && count($ids) > 0 ){
			$list = trim( $list );
			$whereIn = implode( ',', $ids );
			$this->_db->setQuery( "SELECT `loc` FROM `#__sitemapjen_links` WHERE `id` IN ({$whereIn})" );
			$rows = $this->_db->loadAssocList();
			// теперь поочередно помещаем в игнор-лист
			if( count($rows) > 0 ){
				$domain = 'http://'.$_SERVER['SERVER_NAME'];
				foreach( $rows as $row ){
					$loc = str_replace( $domain, '', $row['loc'] );
					$list .= "\n".$loc;
				}
				$list = trim( $list );
				// удаляем адреса из базы
				$this->_db->setQuery( "DELETE FROM `#__sitemapjen_links` WHERE `id` IN ({$whereIn})" );
				$locs = $this->_db->query();				
			}
		}
		return $list;
	}
	
	
	// удаляем все ссылки из базы
	public function removeLinks(){
		$this->_db->setQuery( "TRUNCATE `#__sitemapjen_links`" );
		$rows = $this->_db->query();
	}
	
}