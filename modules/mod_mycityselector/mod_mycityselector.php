<?php
/**
 * Модуль MyCitySelector
 * Отвечает за вывод диалогового окна со списком городов и переключение между ними
 */
defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');


class MyCitySelectorModule
{

    /**
     * @var JRegistry|null Объект параметров модуля
     */
    private $params = null;

    /**
     * @var JRegistry Объект параметров компонента
     */
    private $comParams = null;


    /**
     * Инициализация модуля
     */
    public function __construct()
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
            echo '<span style="color:red;">' . $err . '</span>';
            return;
        } else {
            $this->params = plgSystemPlgMycityselector::$mcs_buffer['params'];
            $this->comParams = plgSystemPlgMycityselector::$mcs_buffer['comParams'];
            // => берем данные из настроек компонента
            $modID = plgSystemPlgMycityselector::$mcs_buffer['mod_id'];
            $http = plgSystemPlgMycityselector::$mcs_buffer['http'];
            $baseDomain = plgSystemPlgMycityselector::$mcs_buffer['base_domain'];
            $cookieDomain = plgSystemPlgMycityselector::$mcs_buffer['cookie_domain'];
            $currentCity = plgSystemPlgMycityselector::$mcs_buffer['cityByDomain'] ? plgSystemPlgMycityselector::$mcs_buffer['cityByDomain'] : $this->comParams->get('default_city');
            $citiesList = plgSystemPlgMycityselector::$mcs_buffer['citiesList'];

            //$hasGroups = (count($citiesList) > 1); // todo не нужен вроде

            $layoutPath = JModuleHelper::getLayoutPath('mod_mycityselector', $this->params->get('layout', 'default')); // => путь до файла шаблона
            $myUrl = JURI::base() . str_replace(JPATH_BASE . '/', '', dirname($layoutPath)) . '/'; // => URL до папки с шаблоном
            // => подключаем файл шаблона
            // передаем параметры в JS
            $this->transferParamsToJS();
            require($layoutPath);
        }

    }


    /**
     * Inject jQuery framework for Joomla 2.5
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
     * Alias для JDocument::addScript
     */
    public function addScript($url, $type = "text/javascript", $defer = false, $async = false)
    {
        JFactory::getDocument()->addScript($url, $type, $defer, $async);
    }


    /**
     * Alias для JDocument::addStyleSheet
     */
    public function addStyle($url, $type = 'text/css', $media = null, $attribs = array())
    {
        JFactory::getDocument()->addStyleSheet($url, $type, $media, $attribs);
    }


    /**
     * Метод для доступа к параметрам модуля или другим данным, вроде списка городов
     *
     * @param String $param Название параметра
     * @param String $default
     * @return String
     */
    public function get($param, $default = '')
    {
        if (isset($MCS_BUFFER[$param])) {
            $default = $MCS_BUFFER[$param];
        } else {
            if (is_object($this->comParams)) {
                $default = $this->comParams->get($param, $default);
            }
        }
        return $default;
    }


    /**
     * Переводит названия городов в транслит, чтобы формировать идентификаторы для js
     * @param String $str Строка для транслитерации
     * @return String
     */
    public function translit($str)
    {
        if (!class_exists('MCSTranslit')) {
            require_once JPATH_ROOT . '/modules/mod_mycityselector/MCSTranslit.php';
        }
        return MCSTranslit::convert($str);
    }
    private function transferParamsToJS() {
        if ($this->comParams->get('let_select', '1') == '1') {
            $script = 'window.mcs_dialog=1;'; // отобразить окно выбора города
        } else {
            $script = 'window.mcs_dialog=2;'; // отобразить предложение о смене города
        }

        $script .= 'window.mcs_base_domain="' . plgSystemPlgMycityselector::$mcs_buffer['basedomain'] . '";' . // основной домен сайта, если есть еще и субдомены
                    'window.mcs_cookie_domain="' . plgSystemPlgMycityselector::$mcs_buffer['cookie_domain'] . '";'. // домен для которого нужно устанавливать кукисы
                    'window.mcs_http="'.plgSystemPlgMycityselector::$mcs_buffer['http'].'";';
        JFactory::getDocument()->addScriptDeclaration($script);
    }

}

// ===================================================

// === Start module ===
new MyCitySelectorModule();