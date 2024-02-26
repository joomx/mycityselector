<?php
/**
 * {jex_name}
 * @author {jex_author}
 * @version 1.0.0
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');


$controller = JControllerLegacy::getInstance('{jex_shortname}');
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task', 'default'));
$controller->redirect();