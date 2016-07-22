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
            'citiesList' => $citiesList,
            'cities_list_type' => $this->get('cities_list_type', '1'),
            'returnUrl' => JUri::getInstance()->toString(),
            'layoutCountry' => JModuleHelper::getLayoutPath('mod_mycityselector', '__country'), // for partial templates use "__" prefix.
            'layoutProvince' => JModuleHelper::getLayoutPath('mod_mycityselector', '__province'),
            'layoutCity' => JModuleHelper::getLayoutPath('mod_mycityselector', '__city')
        ];
        if ($this->variables['cities_list_type'] > 1 ) { // Нужно узнать какой регион выбран, иначе в шаблоне придется перебирать весь массив
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)->select('a.subdomain')->from('#__mycityselector_province a')
                ->leftJoin('#__mycityselector_city b on a.id = b.province_id')
                ->where('a.status = 1 AND b.status = 1 AND b.subdomain ='. $db->quote($this->variables['city']));
            $this->variables['province'] = $db->setQuery($query)->loadResult();
        } else {
            $this->variables['province'] = null;
        }
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
        $listType = $this->get('cities_list_type', '1'); // 0 - cities only, 1 - provinces + cities, 2 - countries + provinces + cities
        $data = [
            'type' => $listType,
            'list' => []
        ];
        $db = JFactory::getDbo();
        if ($listType == '0') {
            // [code => cityName, code => cityName, ... ]
            $query = $db->getQuery(true)->select('name, subdomain')->from('#__mycityselector_city')->where('status = 1')->order('ordering');
            $data['list'] = $db->setQuery($query)->loadAssocList('subdomain', 'name');
        } else if ($listType == '1') {
            // [province => [code => cityName, code => cityName, ... ], province => [...], ...]
            $query = $db->getQuery(true)->select('a.name as province_name, a.subdomain as province_subdomain, b.name as city_name, b.subdomain as city_subdomain')
                ->from('#__mycityselector_province a')
                ->leftJoin('#__mycityselector_city b on a.id = b.province_id')->where('a.status = 1 AND b.status = 1')
                ->order('a.ordering, b.ordering');
            $provinces = $db->setQuery($query)->loadAssocList();
            foreach ($provinces as $province) {
                //$data['list'][$province['province_subdomain']] = [];
                $data['list'][$province['province_subdomain']]['name'] = $province['province_name'];
                $data['list'][$province['province_subdomain']]['list'][$province['city_subdomain']] = $province['city_name'];
            }
        } else {
            // [country => [province => [code => cityName, code => cityName, ... ], province => [...], ...], country => [...], ...]
            $query = $db->getQuery(true)->select('a.name as country_name, a.subdomain as country_subdomain,
                   b.name as province_name, b.subdomain as province_subdomain,c.name as city_name, c.subdomain as city_subdomain')
                ->from('#__mycityselector_country a')
                ->leftJoin('#__mycityselector_province b on a.id = b.country_id')
                ->leftJoin('#__mycityselector_city c on b.id = c.province_id')
                ->where('a.status = 1 AND b.status = 1 AND c.status = 1')
                ->order('a.ordering, b.ordering, c.ordering');
            $result = $db->setQuery($query)->loadAssocList();
            foreach($result as $item) {
                $data['list'][$item['country_subdomain']]['list'][$item['province_subdomain']]['list'][$item['city_subdomain']] = $item['city_name'];
                $data['list'][$item['country_subdomain']]['name'] = $item['country_name'];
                $data['list'][$item['country_subdomain']]['list'][$item['province_subdomain']]['name'] = $item['province_name'];
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
            $$varName = $varValue;
        }
        // include template file
        include($this->layout);
    }

}


// Start module
MyCitySelectorModule::run();