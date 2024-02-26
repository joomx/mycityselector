<?php
/**
 * SQL Query
 * @author Konstantin Kutsevalov
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

if (!Joomla\CMS\Factory::getUser()->authorise('core.manage', 'com_sqlquery'))
{
	throw new Joomla\CMS\Access\Exception\NotAllowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
}

$application = Joomla\CMS\Factory::getApplication();
$compSettings = Joomla\CMS\Component\ComponentHelper::getParams('com_sqlquery');

if ($compSettings->get('is_enabled') == '0') {
    $application->enqueueMessage(JText::_('COM_SQLQUERY_IS_DISABLED'),'notice');
}

$controller = Joomla\CMS\MVC\Controller\BaseController::getInstance('Sqlquery');
$controller->execute($application->input->get('task'));
$controller->redirect();
