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

class SitemapjenViewOptions extends JViewLegacy {
	
	// выводит настройки компонента
	function display( $tpl=null ){ 
		// заголовок
		JToolBarHelper::title( JText::_( 'Sitemap Jen / Настройки' ), 'big-ico' );
		// кнопки операций
		JToolBarHelper::save( 'save_options' );
		//JToolBarHelper::cancel( 'cancel', 'Close' );
		$options = $this->get( 'Options' );
		// передаем данные в шаблон
		$this->assignRef( 'options', $options );
		$this->assign( 'token', JHTML::_( 'form.token' ) );		
		// выводим шаблон
		parent::display($tpl);
	}

}