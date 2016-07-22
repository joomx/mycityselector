<?php

/**
 * Class McsData
 *
 * like a STRUCT
 */
class McsData
{

    /**
     * Component's settings
     * @var JConfig
     */
    private static $compSettings = null;

    /**
     * Module's settings
     * @var JConfig
     */
    private static $modSettings = null;

    /**
     * Code of current city (subdomain)
     * @var string
     */
    private static $city = '';

    /**
     * Name of current city
     * @var string
     */
    private static $cityName = '';

    /**
     * Will set to TRUE if a city was already selected by user
     * @var bool
     */
    private static $isUserHasSelected = false;

    /**
     * If need redirect to some subdomain
     * @var null
     */
    private static $needRedirectTo = null;

    /**
     * Will set to FALSE for subdomains
     * @var bool
     */
    private static $isBaseDomain = true;

    /**
     * @var string
     */
    private static $cookieDomain = '';

    /**
     * @var int
     */
    private static $moduleId = 0;

    /**
     * @var string
     */
    private static $http = 'http://';


    /**
     * Returns any MCS data/parameter by name
     * @param string $name It may be a http, moduleId, cookieDomain, cityName, city, cities, provinces, countries, basedomain, default_city, needRedirectTo
     *          or any of parameter names of component config of module.
     * @param $default
     * @return mixed
     */
    public static function get($name, $default = null)
    {
        if (!empty(self::${$name})) {
            return self::${$name};
        } else if (!empty(self::$compSettings) && self::$compSettings->get($name) !== null) {
            return self::$compSettings->get($name);
        } else if (!empty(self::$modSettings) && self::$modSettings->get($name) !== null) {
            return self::$modSettings->get($name);
        }
        return $default;
    }


    public static function load()
    {
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
        self::$isUserHasSelected = empty($_COOKIE['MCS_CITY_CODE']) ? false : true; // if cookies exists then used has selected a city
        // current city
        self::detectCurrentCity();
    }


    /**
     * Defines a current city
     */
    private static function detectCurrentCity()
    {
        if (self::get('subdomain_cities') == '1') {
            // check by current subdomain
            if (!empty(self::$compSettings) && self::$compSettings->get('basedomain')) { // check base domain
                $baseDomain = self::$compSettings->get('basedomain');
            } else {
                // try to detect base domain name
                $baseDomain = $_SERVER['HTTP_HOST'];
                $parts = explode('.', $baseDomain);
                if (count($parts) > 2) {
                    if ($parts[0] == 'www') {
                        $baseDomain = str_replace('www.', '', $_SERVER['HTTP_HOST']);
                    } else {
                        // probably, this is a subdomain, need to remove its and left only domain name
                        unset($parts[0]);
                        $baseDomain = implode('.', $parts);
                    }
                }
            }
            $subDomain = str_replace([$baseDomain, '.'], ['', ''], $_SERVER['HTTP_HOST']);
            if (!empty($subDomain) && $subDomain != 'www') {
                self::$isBaseDomain = false;
                $city = self::findCity($subDomain);
                if ($city) {
                    self::$city = $subDomain;
                    self::$cityName = $city['name'];
                } // else :pointDefault:
            } else {
                // this is the BASE domain
                // check current city by cookies but only if autoswitch is disabled.
                if (self::get('autoswitch_city') == '1' && !empty($_COOKIE['MCS_CITY_CODE'])) { // new cookie key
                    $city = self::findCity($_COOKIE['MCS_CITY_CODE']);
                    if ($city) {
                        // compare with default city
                        if (!empty(self::$compSettings) && self::$compSettings->get('default_city')) {
                            $default = self::$compSettings->get('default_city');
                            if ($default != $_COOKIE['MCS_CITY_CODE']) {
                                // need redirect to right subdomain
                                self::$city = $_COOKIE['MCS_CITY_CODE'];
                                self::$cityName = $city['name'];
                                self::$needRedirectTo = self::get('http') . self::$city . '.' . self::get('basedomain') . '/';
                            }
                        }
                    } // else :pointDefault:
                }
            }
        } else {
            if (!empty($_COOKIE['MCS_CITY_CODE'])) { // new cookie key
                $city = self::findCity($_COOKIE['MCS_CITY_CODE']);
                if ($city) {
                    self::$city = $_COOKIE['MCS_CITY_CODE'];
                    self::$cityName = $city['name'];
                } // else :pointDefault:
            }
        }

        // :pointDefault:
        // get default city of base domain (default_city from comp options)
        if (empty(self::$city)) {
            if (!empty(self::$compSettings) && self::$compSettings->get('default_city')) {
                $city = self::findCity(self::$compSettings->get('default_city'));
                if ($city) {
                    self::$city = $city['subdomain'];
                    self::$cityName = $city['name'];
                }
            }
        }

        // set cookies
        setcookie('MCS_CITY_CODE', self::$city, time() + 3600 * 24 * 30, '/', self::$cookieDomain);
    }


    /**
     * TODO еще пилю
     * Returns cities from DB by condition
     * @param array $excludes
     * @return array
     */
    public static function getCities($excludes = [])
    {
        $db = JFactory::getDbo();
        if (!empty($excludes)) {
            $where = ' `name` NOT IN (';
            foreach ($excludes as $name) {
                $where .= $db->quote($name);
            }
            $where .= ')';
        }
        if (empty($where)) {
            $where = '1 = 1';
        }

        //exit($where);

        $query = $db->getQuery(true)->select('*')->from('#__mycityselector_city')->where($where);
        return $db->setQuery($query)->loadAssocList();
    }


    /**
     * @param $code
     * @return mixed|null
     */
    private static function findCity($code)
    {
        $city = null;
        if (!empty($code)) {
            $db = JFactory::getDbo();
            $code = $db->quote('%' . $code . '%');
            $query = $db->getQuery(true)->select('*')->from('#__mycityselector_city')->where("`subdomain` LIKE {$code}");
            $city = $db->setQuery($query)->loadAssocList();
            if (!empty($city)) {
                $city = $city[0];
            }
        }
        return $city;
    }


    /**
     * Search Item (city or province of cities)
     * @param $name
     * @return mixed|null
     */
    public static function getTypeByName($name)
    {
        $type = null;
        if (!empty($name)) {
            $db = JFactory::getDbo();
            $name = $db->quote('%' . $name . '%');
            $query = $db->getQuery(true)->select('id')->from('#__mycityselector_city')->where("`name` LIKE {$name}");
            if (!empty($db->setQuery($query)->loadAssocList())) {
                $type = 'city';
            } else {
                $query = $db->getQuery(true)->select('id')->from('#__mycityselector_province')->where("`name` LIKE {$name}");
                if (!empty($db->setQuery($query)->loadAssocList())) {
                    $type = 'province';
                } else {
                    $query = $db->getQuery(true)->select('id')->from('#__mycityselector_country')->where("`name` LIKE {$name}");
                    if (!empty($db->setQuery($query)->loadAssocList())) {
                        $type = 'country';
                    }
                }
            }
        }
        return $type;
    }

}