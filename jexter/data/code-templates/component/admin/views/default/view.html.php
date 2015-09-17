<?php
/**
 * Sitemap Jen
 * @author Konstantin@Kutsevalov.name
 * @package    sitemapjen
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );
jimport( 'joomla.html.html' );	// JHTML

// имя "вида" формируется по принципу [название_компонента]View[название_view]
// (./views/[название_view]/view.html.php)

class SitemapjenViewDefault extends JViewLegacy {
	// выводит список просканированных ссылок
	
	function display( $tpl=null ){
		// заголовок
		JToolBarHelper::title( JText::_( 'Sitemap Jen' ), 'big-ico' );
		$links = $this->get( 'Links' ); // $this->get('Links') вызывает метод модели $model->getLinks();
		$pagination = $this->get( 'Pagination' );
		// передаем данные из модели в шаблон
		$this->assignRef( 'links', $links );
		$this->assignRef( 'pagination', $pagination );
		if( count($links) > 0 ){
			// кнопки операций
			JToolBarHelper::custom( 'to_ignore', 'unpublish', '123', 'В исключение', true, false );
			JToolBarHelper::custom( 'clear_links', 'delete', null, 'Удалить все', false, false );
		}
		$this->assign( 'token', JHTML::_( 'form.token' ) );
		parent::display( $tpl );
	}
 
}