<?php

namespace joomx\mcs\module;

/**
 * Модуль MyCitySelector
 * Отвечает за вывод диалогового окна со списком городов и переключение между ними
 *
 * @var $module   \stdClass with options: id, title, module, position, content, showtitle, params, menuid, name, style
 * @var $params   \Joomla\Registry\Registry (module's parameters)
 * @var $app      \JApplicationSite
 * @var $template string (site's template name)
 * @var $path     string (path of this file like __FILE__)
 */

defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');

use joomx\mcs\plugin\helpers\McsData;

use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Factory;
use \Joomla\Registry\Registry;
use \Joomla\CMS\Helper\ModuleHelper;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\HTML\HTMLHelper;

require_once __DIR__ . '/McsModHelper.php';

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
     * @var stdClass|null
     */
    private $module = null;

    private $langId;

    /**
     * @param stdClass                 $module
     * @param \Joomla\Registry\Registry $params
     *
     * @return MyCitySelectorModule
     */
    public static function run($module, $params)
    {
        if (self::$instance === null)
        {
            self::$instance = new self($module, $params);
        }
        if (empty(self::$instance->error))
        {
            self::$instance->render();
        }
        else
        {
            echo self::$instance->error;
        }
    }


    /**
     * @param stdClass $module
     * @param Registry $params
     */
    private function __construct($module, $params)
    {
        if (!class_exists('joomx\mcs\plugin\helpers\McsData')) {
            $find = Factory::getDbo()
                ->setQuery("SELECT COUNT(*) AS `cnt` FROM `#__extensions` WHERE `element`='plgmycityselector'")
                ->loadResult();
            if ($find) {
                $err = Text::_('MOD_MYCITYSELECTOR_PLUGIN_NOT_ACTIVE');
            } else {
                $err = Text::_('MOD_MYCITYSELECTOR_PLUGIN_NOT_INSTALLED');
            }
            $this->error = '<span style="color:red;">' . $err . '</span>';
            return;
        }

	    // $this->addScripts();

        $this->langId = McsData::getLangId();
        $locationsList = $this->loadLocations();

        $this->layout = ModuleHelper::getLayoutPath('mod_mycityselector', $this->get('layout', 'default'));
        $this->path = parse_url($_SERVER['REQUEST_URI']);
        $this->module = $module;
        $this->variables = [
            'modID'            => $module->id,
            'params'           => $params,
            'comParams'        => $this->get('compSettings'),
            'http'             => $this->get('http'),
            'cookieDomains'    => $this->get('cookieDomain'),
            'location'         => $this->get('location'),
            'city'             => $this->get('city'),
            'province'         => $this->get('province'),
            'country'          => $this->get('country'),
            'locationName'     => $this->get('locationName'),
            'layoutUrl'        => Uri::base() . str_replace(JPATH_BASE . '/', '', dirname($this->layout)) . '/',
            'locationsList'    => $locationsList,
            'locationsListType'=> $this->get('cities_list_type', 1),
            'text_before'      => McsData::get('text_before'),
            'text_after'       => McsData::get('text_after'),
        ];
        McsData::set('moduleId', $module->id);
        McsData::set('modSettings', $params);

        // TODO почему мне кажется, что эти переменные больше не нужны, мы же через vue.js все делаем
        $debug  = $this->get('debug_mode');
        $script = "window.mcs_let_select='" . $this->get('let_select') . "';\n"
            . "window.mcs_base_domain='" . $this->get('basedomain') . "';\n"
            . "window.mcs_list_type='" . $this->get('cities_list_type', 1) . "';\n"
            . "window.mcs_cookie_domain='" . $this->get('cookieDomain') . "';\n"
            . "window.mcs_http='" . $this->get('http') . "';\n"
            . "window.mcs_baseip='" . $this->get('baseip') . "';\n"
            . "window.mcs_seo_mode='" . $this->get('seo_mode', 0) . "';\n"
            . "window.mcs_default_city='" . $this->get('default_city') . "';\n"
            . "window.mcs_debug_mode=" . (empty($debug) ? 'false' : 'true') . ";\n"
            . "window.mcs_city='" . $this->get('city') . "'\n"
            . "window.mcs_uri_root='" . Uri::root(true) . "';\n";

        $doc = Factory::getDocument();
        $doc->addScriptDeclaration($script);
    }


    /**
     * Load all cities
     */
    private function loadLocations()
    {
        $db = Factory::getDbo();
        // [country => [province => [code => cityName, code => cityName, ... ], province => [...], ...], country => [...], ...]
        $query = $db->getQuery(true)->select('country_name.name as country_name, country.subdomain as country_code,
               country.code as country_code, country.domain as country_domain, country.ordering as country_ordering, default_city_of_country.subdomain as default_city,
               province_name.name as province_name, province.subdomain as province_code, city_name.name as city_name, city.subdomain as city_code')
            ->from('#__mycityselector_countries as country')
            ->innerJoin("#__mycityselector_country_names AS country_name ON country_name.country_id = country.id")
            ->leftJoin('#__mycityselector_cities as default_city_of_country on country.default_city_id = default_city_of_country.id')
            ->leftJoin('#__mycityselector_provinces as province on country.id = province.country_id')
            ->innerJoin("#__mycityselector_province_names AS province_name ON province_name.province_id = province.id")
            ->leftJoin('#__mycityselector_cities city on province.id = city.province_id')
            ->innerJoin("#__mycityselector_city_names AS city_name ON city_name.city_id = city.id")
            ->where('country.published = 1 AND province.published = 1 AND city.published = 1')
            ->where("country_name.lang_id = {$this->langId} AND province_name.lang_id = {$this->langId} AND city_name.lang_id = {$this->langId}")
            ->order('country.ordering, province.ordering, city.ordering');
        if (McsData::MCS_FREE == true) {
            $result = $db->setQuery($query, 0, 5)->loadAssocList();
        } else {
            $result = $db->setQuery($query)->loadAssocList();
        }

        //формируем ссылки на поддомены
        foreach ($result as &$location) {
            $location['country_link'] = McsData::buildUrlByLocation($location, 'country');
	        $location['province_link'] = McsData::buildUrlByLocation($location, 'province');
	        $location['city_link'] = McsData::buildUrlByLocation($location, 'city');
        }

        return $result;
    }

    /**
     * Alias for JDocument::addScript
     * Uses in template as $this->addScript()
     */
    public function addScript($url, $type = "text/javascript", $defer = false, $async = false)
    {
        Factory::getDocument()->addScript($url, $type, $defer, $async);
    }


    /**
     * Alias for JDocument::addStyleSheet
     * Uses in template as $this->addStyle()
     */
    public function addStyle($url, $type = 'text/css', $media = null, $attribs = array())
    {
        Factory::getDocument()->addStyleSheet($url, $type, $media, $attribs);
    }


    /**
     * Short alias for McsData::get()
     *
     * @param String $name
     * @param String $default
     *
     * @return mixed
     */
    public function get($name, $default = '')
    {
        return McsData::get($name, $default);
    }


    public function render()
    {
        $triggerBeforeRender = 'mcsHook_beforeRender';
        if (function_exists($triggerBeforeRender)) {
            $this->variables = $triggerBeforeRender($this->variables);
        }
        // init variables
        extract($this->variables);
        // include template file
        include($this->layout);
    }

}
