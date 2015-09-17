<?php
/**
 * Sitemap Jen
 * @author Konstantin@Kutsevalov.name
 * @joomlaVer 3.X
 */

defined('_JEXEC') or die('Restricted access');

$controller = JControllerLegacy::getInstance('Sitemapjen');
$input = JFactory::getApplication()->input;
$controller->execute( $input->getCmd('task') );
$controller->redirect();