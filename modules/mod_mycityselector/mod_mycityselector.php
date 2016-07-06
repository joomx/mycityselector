<?php
/**
 * Модуль MyCitySelector
 * Отвечает за вывод диалогового окна со списком городов и переключение между ними
 */
defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');


/**
 * Singleton
 * Class MyCitySelectorModule
 */
class MyCitySelectorModule
{

    /**
     * @var MyCitySelectorModule|null
     */
    private static $instance = null;

    /**
     * @var string
     */
    private $error = '';

    /**
     * Template path
     * @var string
     */
    private $layout = null;

    /**
     * Variables for template
     * @var array
     */
    private $variables = [];


    /**
     * @return MyCitySelectorModule
     */
    public static function run()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        if (empty(self::$instance->error)) {
            self::$instance->render();
        } else {
            echo self::$instance->error;
        }
    }


    /**
     * init
     */
    private function __construct()
    {
        if (!class_exists('McsData')) {
            $find = JFactory::getDbo()
                ->setQuery("SELECT COUNT(*) AS `cnt` FROM `#__extensions` WHERE `element`='plgmycityselector'")
                ->loadResult();
            if ($find) {
                $err = 'Плагин MyCitySelector не активен!';
            } else {
                $err = 'Плагин MyCitySelector не установлен!';
            }
            $this->error = '<span style="color:red;">' . $err . '</span>';
            return;
        }

        $citiesList = $this->loadCities();
        $this->layout = JModuleHelper::getLayoutPath('mod_mycityselector', McsData::get('layout', 'default'));
        $this->variables = [
            'modID' => $this->get('moduleId'),
            'params' => $this->get('modSettings'),
            'comParams' => $this->get('compSettings'),
            'http' => $this->get('http'),
            'baseDomain' => $this->get('basedomain'),
            'cookieDomain' => $this->get('cookieDomain'),
            'city' => $this->get('city'),
            'cityCode' => $this->get('cityName'),
            'layoutUrl' => JURI::base() . str_replace(JPATH_BASE . '/', '', dirname($this->layout)) . '/',
            'citiesList' => $citiesList
        ];

        $dialog = '0';
        $yandex = 'false';
        if (!McsData::get('isUserHasSelected')) {
            $dialog = (McsData::get('let_select', '1') == '1') ? '1' : '2';
            if ($this->get('baseip', 'none') == 'yandexgeo') { // Yandex geolocation
                $yandex = 'true';
            }
        }
        $script = "window.mcs_dialog={$dialog};"
            . 'window.mcs_base_domain="' . McsData::get('basedomain') . '";'
            . 'window.mcs_cookie_domain="' . McsData::get('cookieDomain') . '";'
            . 'window.mcs_http="' . McsData::get('http') . '";'
            . "window.mcs_yandexgeo={$yandex};";
        JFactory::getDocument()->addScriptDeclaration($script);
    }


    /**
     * Load all cities
     */
    private function loadCities()
    {
        $listType = $this->get('cities_list_type', '0'); // 0 - cities only, 1 - provinces + cities, 2 - countries + provinces + cities
        $data = [
            'type' => $listType,
            'list' => []
        ];
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)->select('*')->from('#__mycityselector_city')->where('status = 1');
        $cities = $db->setQuery($query)->loadAssocList('subdomain');
        if ($listType == '0') {
            // [code => cityName, code => cityName, ... ]
            foreach ($cities as $city) {
                $data['list'][ $city['subdomain'] ] = $city['name'];
            }
        } else if ($listType == '1') {
            // [province => [code => cityName, code => cityName, ... ], province => [...], ...]
            $query = $db->getQuery(true)->select('*')->from('#__mycityselector_region')->where('status = 1');
            $provinces = $db->setQuery($query)->loadAssocList('subdomain');
            foreach ($provinces as $province) {
                $data['list'][$province['name']] = [];
                foreach ($cities as $city) {
                    $data['list'][$province['name']][ $city['subdomain'] ] = $city['name'];
                }
            }
        } else {
            // [country => [province => [code => cityName, code => cityName, ... ], province => [...], ...], country => [...], ...]
            $query = $db->getQuery(true)->select('*')->from('#__mycityselector_country')->where('status = 1');
            $countries = $db->setQuery($query)->loadAssocList('subdomain'); // subdomain as index
            $query = $db->getQuery(true)->select('*')->from('#__mycityselector_region')->where('status = 1');
            $provinces = $db->setQuery($query)->loadAssocList('subdomain');
            foreach ($countries as $country) {
                $data['list'][$country] = [];
                foreach ($provinces as $province) {
                    $data['list'][$country['name']][$province['name']] = [];
                    foreach ($cities as $city) {
                        $data['list'][$country['name']][$province['name']][ $city['subdomain'] ] = $city['name'];
                    }
                }
            }
        }
        return $data;
    }


    /**
     * Inject jQuery framework for Joomla 2.5
     * Uses in template as $this->addJQuery()
     */
    public function addJQuery()
    {
        if (JHtml::isRegistered('jquery.framework')) {
            JHtml::_('jquery.framework');
        } else {
            JFactory::getDocument()->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js');
            JFactory::getDocument()->addScriptDeclaration('jQuery.noConflict();');
        }
    }


    /**
     * Alias for JDocument::addScript
     * Uses in template as $this->addScript()
     */
    public function addScript($url, $type = "text/javascript", $defer = false, $async = false)
    {
        JFactory::getDocument()->addScript($url, $type, $defer, $async);
    }


    /**
     * Alias for JDocument::addStyleSheet
     * Uses in template as $this->addStyle()
     */
    public function addStyle($url, $type = 'text/css', $media = null, $attribs = array())
    {
        JFactory::getDocument()->addStyleSheet($url, $type, $media, $attribs);
    }


    /**
     * Short alias for McsData::get()
     * @param String $name
     * @param String $default
     * @return mixed
     */
    public function get($name, $default = '')
    {
        return McsData::get($name, $default);
    }


    public function render()
    {
        // init variables
        foreach ($this->variables as $varName => $varValue) {

        }
        // include template file
        include($this->layout);
    }

}


// Start module
MyCitySelectorModule::run();