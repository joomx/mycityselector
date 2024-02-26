<?php
namespace joomx\mcs\plugin\helpers;

defined('_JEXEC') or exit(header('HTTP/1.0 404 Not Found') . '404 Not Found');

use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;


class McsPluginHelper
{

    public const CLIENT_MODE_SITE = 'site';

    public const CLIENT_MODE_ADMIN = 'admin';


    /**
     * @param $params
     * @return string
     */
    public function getVersion($params)
    {
        // $db = Factory::getContainer()->get('DatabaseDriver'); // мде, пишут использовать этот класс, а он еще даже не реализован
        $db = Factory::getDbo(); // зато блин этот уже несколько лет deprecated
        $query = $db->getQuery(true);
        $query->select($db->quoteName('manifest_cache'))->from('#__extensions')->where('extension_id = ' . $params['id']);
        $manifest = $db->setQuery($query)->loadAssoc();
        if (!empty($manifest['manifest_cache'])) {
            $manifest = json_decode($manifest['manifest_cache'], true);
            if ($manifest && isset($manifest['version'])) {
                return $manifest['version'];
            }
        }
        return '0.0.1';
    }


    public function isEditMode()
    {
        /**
         * @var $jInput \Joomla\Input\Input
         */
        $jInput = Factory::getApplication()->input;
        if (!empty($jInput->get('layout'))) {
            return ($jInput->get('layout') == 'edit' || $jInput->get('option') == 'com_rsseo');
        }
        return false;
    }


    public function getClientMode()
    {
        $app = Factory::getApplication();
        if (method_exists($app, 'isClient')) {
            return $app->isClient('administrator') ?
                self::CLIENT_MODE_ADMIN : self::CLIENT_MODE_SITE;
        }
        return ($app->getName() == 'administrator') ?
            self::CLIENT_MODE_ADMIN : self::CLIENT_MODE_SITE;
    }


    public function isRsseoCrawler()
    {
        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
            if (stripos($_agent, 'rsseo') !== false && stripos($_agent, 'crawler') !== false) {
                return true;
            }
        }
        return false;
    }


    public function clearCookies()
    {
        unset($_COOKIE['MCS_COUNTRY_CODE']);
        unset($_COOKIE['MCS_COUNTRY_NAME']);
        unset($_COOKIE['MCS_PROVINCE_CODE']);
        unset($_COOKIE['MCS_PROVINCE_NAME']);
        unset($_COOKIE['MCS_CITY_CODE']);
        unset($_COOKIE['MCS_CITY_NAME']);
        unset($_COOKIE['MCS_DOMAIN']);
        unset($_COOKIE['MCS_NOASK']);
        unset($_COOKIE['MCS_LOCATION_TYPE']);
    }


    public function setCookieFromQuery()
    {
        $domain = '.' . McsData::getBaseNameOfCurrentDomain();
        foreach ($_GET as $key => $value) {
            if (strpos($key, 'MCS') !== false) {
                setcookie($key, $value, time() + 3600 * 24 * 30, '/', $domain, false, false);
            }
        }
    }


    /**
     * Alias for APP->getBody();
     * @return string
     */
    public function getPageBody()
    {
        $app = Factory::getApplication();
        if (method_exists($app, 'getBody')) {
            return $app->getBody(); // Joomla 3.x
        }
        return JResponse::getBody(); // joomla 2.5
    }


    /**
     * Alias for APP->setBody();
     */
    public function setPageBody($body)
    {
        $app = Factory::getApplication();
        if (method_exists($app, 'setBody')) {
            $app->setBody($body); // Joomla 3.x
        } else {
            JResponse::setBody($body); // joomla 2.5
        }
    }


    public function redirectToBaseDomain()
    {
        // check for "backend mode" and "frontend edit mode"
        $baseDomain = McsData::get('basedomain', null, 'original');
        // check for redirect
        $uri = Uri::getInstance();
        $uri->setHost($baseDomain);
        $location = $uri->toString();
        exit(header('Location: ' . $location));
    }


    // TODO это для каких случаем нужен редирект через JS?
    public function JsRedirect($link)
    {
        Factory::getDocument()->addScriptDeclaration('window.location.replace("' . $link . '");');
    }


    public function loadExperimentalConfig($default = [])
    {
        if (is_file(JPATH_ROOT . '/mcs-experimental.php')) {
            $config = require_once(JPATH_ROOT . '/mcs-experimental.php');
            if (is_array($config)) {
                return array_merge($default, $config);
            }
        }
        return $default;
    }

}