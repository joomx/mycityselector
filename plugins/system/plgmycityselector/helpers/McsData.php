<?php
/**
 * Class McsData
 *
 * like a STRUCT
 */

class McsData {

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
     * All countries from table
     * @var array
     */
    private static $countries = [];

    /**
     * All provinces from table
     * @var array
     */
    private static $provinces = []; // todo rename regions to provinces

    /**
     * All cities from table
     * @var array
     */
    private static $cities = [];

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
            $value = self::${$name};
        } else if (!empty(self::$compSettings) && self::$compSettings->get($name)) {
            $value = self::$compSettings->get($name);
        } else if (!empty(self::$modSettings) && self::$modSettings->get($name)) {
            $value = self::$modSettings->get($name);
        }
        return empty($value) ? $default : $value;
    }


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

        self::$isUserHasSelected = empty($_COOKIE['MCS_CITY_CODE']) ? false : true; // if cookies exists then used has selected a city

        // current city
        self::$city = self::detectCurrentCity();

        if ($allData) {
            // countries
            $query = $db->getQuery(true)->select('*')->from('#__mycityselector_country')->where('status = 1');
            $rows = $db->setQuery($query)->loadAssocList();
            foreach ($rows as $row) {
                self::$countries[ $row['subdomain'] ] = $row; // subdomain as index
            }
            // provinces
            $query = $db->getQuery(true)->select('*')->from('#__mycityselector_region')->where('status = 1');
            $rows = $db->setQuery($query)->loadAssocList();
            foreach ($rows as $row) {
                self::$provinces[ $row['subdomain'] ] = $row;
            }
            // cities
            $query = $db->getQuery(true)->select('*')->from('#__mycityselector_city')->where('status = 1');
            $rows = $db->setQuery($query)->loadAssocList();
            foreach ($rows as $row) {
                self::$cities[ $row['subdomain'] ] = $row;
            }
        }
    }


    /**
     * Defines a current city
     */
    private static function detectCurrentCity()
    {
        // @devnote TODO KD: не уловил идею, зачем нужен debug?
        if (self::get('debug_mode') == '1') {
            unset($_COOKIE['MCS_CITY_NAME']);
            unset($_COOKIE['MCS_NOASK']);
            setcookie('MCS_CITY_NAME', '', time()-10, '/', self::$cookieDomain);
            setcookie('MCS_NOASK', '', time()-10, '/', self::$cookieDomain);
        }

        // @devnote TODO KD проверь мой алгоритм, может я что-то упустил из виду?
        // @devnote === Алгоритм ===
        // @devnote Если есть поддомены то:
        // @devnote     Если мы сейчас на поддомене то:
        // @devnote         Проверяем текущий поддомен и ищем соответствующий ему город
        // @devnote         Если соответствия город-поддомен не найдено то
        // @devnote             Устанавливаем город по умолчанию. end.
        // @devnote         Если соответствие найдено то
        // @devnote             Устанавливаем город как текущий. end.
        // @devnote     Если мы сейчас на базовом домене, то:
        // @devnote         Если "автопереключение" активно, то берем текущий домен из кукисов (redirect будет в плагине). end.
        // @devnote         Если "автопереключение" не активно, то ставим город по умолчанию. end.
        // @devnote Если нет поддоменов то:
        // @devnote     Пытаемся прочитать город из кукисов или берем город по умолчанию. end.
        // @devnote
        // @devnote TODO Нужно подумать над опцией 'autoswitch_city', мне кажется, что правильнее было бы делать
        // @devnote перебрасывание на уже выбранный город (из кукисов) не только с базового домена, а вообще,
        // @devnote с любого поддомена тоже. То есть если автопереключение выключено, то текущий город == текущий домен (не важно какой).
        // @devnote А если включено, то текущий город = город в кукисах и автоматический переход на соотв. поддомен.


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
            if ($subDomain != 'www') {
                self::$isBaseDomain = false;
                if (isset(self::$cities[$subDomain])) {
                    self::$city = $subDomain;
                    self::$cityName = self::$cities[$subDomain]['name'];
                } // else :pointDefault:
            } else {
                // this is the BASE domain
                // check current city by cookies but only if autoswitch is disabled.
                if (self::get('autoswitch_city') == '1' && !empty($_COOKIE['MCS_CITY_CODE'])) { // new cookie key
                    if (isset(self::$cities[$_COOKIE['MCS_CITY_CODE']])) {
                        // compare with default city
                        if (!empty(self::$compSettings) && self::$compSettings->get('default_city')) {
                            $default = self::$compSettings->get('default_city');
                            if ($default != $_COOKIE['MCS_CITY_CODE']) {
                                // need redirect to right subdomain
                                self::$city = $_COOKIE['MCS_CITY_CODE'];
                                self::$cityName = self::$cities[$_COOKIE['MCS_CITY_CODE']]['name'];
                                self::$needRedirectTo = self::get('http') . self::$city . '.' . self::get('basedomain') . '/';
                            }
                        }
                    } // else :pointDefault:
                }
            }
        } else {
            if (!empty($_COOKIE['MCS_CITY_CODE'])) { // new cookie key
                if (isset(self::$cities[$_COOKIE['MCS_CITY_CODE']])) {
                    self::$city = $_COOKIE['MCS_CITY_CODE'];
                    self::$cityName = self::$cities[$_COOKIE['MCS_CITY_CODE']]['name'];
                } // else :pointDefault:
            }
        }

        // :pointDefault:
        // get default city of base domain (default_city from comp options)
        if (empty(self::$city)) {
            if (!empty(self::$compSettings) && self::$compSettings->get('default_city')) {
                $city = self::$compSettings->get('default_city');
                if (self::$cities[$city]) {
                    self::$city = $city;
                    self::$cityName = self::$cities[$city]['name'];
                }
            }
        }

        // set cookies
        setcookie('MCS_CITY_CODE', self::$city, time() + 3600 * 24 * 30, '/', self::$cookieDomain);
    }

}