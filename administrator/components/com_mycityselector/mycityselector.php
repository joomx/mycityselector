<?php
/**
 * My City Selector Extension
 *
 * @author Adamas Antares https://github.com/adamasantares
 * @copyright what is copyright?
 * @license let be MIT
 *
 * This extension need your help. Please visit https://github.com/adamasantares/mycityselector
 */

defined('_JEXEC') or exit(header('HTTP/1.0 404 Not Found') . '<h3>404 File not found</h3>');

$controller = JControllerLegacy::getInstance('Mycityselector');
$controller->execute(JFactory::getApplication()->input->get('task', 'display'));
$controller->redirect();
