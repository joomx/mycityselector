<?php
/**
 * MyCitySelector
 * @author  Konstantin Kutsevalov
 * @version 2.0.0
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

$input = Joomla\CMS\Factory::getApplication()->input;

$controller = Joomla\CMS\MVC\Controller\BaseController::getInstance('Mycityselector');
$controller->execute($input->get('task'));
