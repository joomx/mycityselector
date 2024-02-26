<?php
/**
 * @package     SqlQuery
 * @subpackage  com_sqlquery
 *
 */

defined('_JEXEC') or die;

require_once realpath(__DIR__ . '/helpers.php');

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Session\Session;

/**
 * Default Controller
 */
class SqlqueryController extends Joomla\CMS\MVC\Controller\BaseController
{

	public function display($cachable = false, $urlparams = false)
	{
        $document = Factory::getDocument();
	    $db = Factory::getDbo();
        $tables = $sqlQueries = $results = [];
        $layoutName = 'disabled';
        $sqlQuery = $this->input->get('sql_query', '', 'string');
        $viewName   = $this->input->get('view', 'default');
        $viewFormat = $document->getType();
        $compSettings = ComponentHelper::getParams('com_sqlquery');

        if ($compSettings->get('is_enabled') !== '0') {
            $layoutName = 'default';
            // execute sql
            $sqlQuery = trim($sqlQuery, ' ');
            if (!empty($sqlQuery)) {
                Session::checkToken() or die('You sent wrong token!');
                $sqlQueries = \joomx\sqlquery\helpers\splitQueries($sqlQuery);
                \joomx\sqlquery\helpers\executeQueries($db, $sqlQueries, $results);
            }
            // tables
            $query = $db->setQuery("SHOW TABLES");
            $tables = $query->loadAssocList();
            foreach ($tables as $k => $values) {
                $tables[$k] = current($values);
            }
        }

		if ($view = $this->getView($viewName, $viewFormat)) {
			$view->setLayout($layoutName);
			$view->tables = $tables;
			$view->sql_query = $sqlQuery;
			$view->sql_queries = $sqlQueries;
			$view->sql_results = $results;
			$view->document = $document;
			$view->display();
		}
		return $this;
	}

}
