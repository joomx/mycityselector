<?php
/**
 * MyCitySelector
 * @author Konstantin Kutsevalov
 * @version 2.0.0
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

define('COM_MCS_PATH', dirname(__FILE__));

require_once COM_MCS_PATH . '/helpers/mvc/JxRouter.php';

\adamasantares\jxmvc\JxRouter::executeController(COM_MCS_PATH, 'com_mycityselector');