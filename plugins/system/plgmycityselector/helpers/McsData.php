<?php
/**
 * Class McsData
 */

class McsData {

    /**
     * @var null
     */
    private static $compSettings = null;

    private static $modSettings = null;

    private static $countries = [];

    private static $provinces = []; // todo rename regions to provinces

    private static $cities = [];

    private static $cookieDomain = '';

    private static $moduleId = 0;

    private static $http = 'http';


    public static function load($allData=false)
    {
        $db = JFactory::getDbo();

        // load component settings
        self::$compSettings = JComponentHelper::getParams('com_mycityselector');

        // load module settings (todo there is one problem with modulehelper, it always returns params only for first module, but user may has several modules)
        $module = JModuleHelper::getModule('mod_mycityselector');
        self::$modSettings = new JRegistry($module->params);
        self::$moduleId = $module->id;

        // cookie domain
        if (self::$compSettings->get('subdomain_cities') == '1') {
            self::$cookieDomain = '.' . self::$compSettings->get('basedomain');
        } else {
            self::$cookieDomain = self::$compSettings->get('basedomain');
        }

        // http || https ?
        self::$http = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) ?
            'https://' : 'http://';

        if ($allData) {
            // countries
            $query = $db->getQuery(true)->select('*')->from('#__mycityselector_country')->where('status = 1');
            self::$countries = $db->setQuery($query)->loadAssocList();
            // provinces
            $query = $db->getQuery(true)->select('*')->from('#__mycityselector_region')->where('status = 1');
            self::$provinces = $db->setQuery($query)->loadAssocList();
            // cities
            $query = $db->getQuery(true)->select('*')->from('#__mycityselector_city')->where('status = 1');
            self::$cities = $db->setQuery($query)->loadAssocList();
        }
    }


    /**
     * Returns any MCS data/parameter by name
     * @param $name
     * @param $default
     * @return mixed
     */
    public static function get($name, $default = null)
    {
        if (property_exists(self, $name)) {
            $value = self::${$name};
        } else if (!empty(self::$compSettings)) {
            $value = self::$compSettings->get($name);
        }

        return empty($value) ? $default : $value;
    }



} 