<?php
/**
 * Sitemap Jen
 * @author Konstantin@Kutsevalov.name
 * @package    sitemapjen
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );
//jimport( 'joomla.html.html' );	// JHTML

// имя "вида" формируется по принципу [название_компонента]View[название_view]
// (./views/[название_view]/view.html.php)

class SitemapjenViewGenerate extends JViewLegacy {
	
	function display( $tpl=null ){
		JToolBarHelper::title( 'Sitemap Jen / Генератор', 'big-ico' );
		// данные для шаблона передаются из контроллера
		parent::display($tpl);
	}

}