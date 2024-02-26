<?php
/**
 * MyCitySelector
 * @author Konstantin Kutsevalov
 * @version 2.0.0
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

use joomx\mcs\plugin\helpers\McsData;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Language\Text;

// HTMLHelper::_('behavior.tabstate'); todo вроде как это не нужно больше

if (!Factory::getUser()->authorise('core.manage', 'com_mycityselector')) {
	throw new Joomla\CMS\Access\Exception\NotAllowed(Text::_('JERROR_ALERTNOAUTHOR'), 403);
}

$application = Factory::getApplication();

if (McsData::MCS_FREE) {
    $application->enqueueMessage(Text::_('COM_MYCITYSELECTOR_DEMO'),'notice');
}

$controller = Joomla\CMS\MVC\Controller\BaseController::getInstance('Mycityselector');
$controller->execute($application->input->get('task'));
$controller->redirect();