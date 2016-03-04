<?php
/**
 * MyCitySelector
 * @author Konstantin Kutsevalov
 * @version 1.0.0
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

require_once dirname(__FILE__) . '/helpers/mvc/JxRouter.php';

\adamasantares\jxmvc\JxRouter::executeController(dirname(__FILE__), 'com_mycityselector');