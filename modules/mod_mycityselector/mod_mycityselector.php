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
     * Инициализация модуля
     */
    public function __construct()
    {
        if (!class_exists('plgSystemPlgMycityselector')) {


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
            $parameters = JComponentHelper::getParams('com_mycityselector');
            // => берем данные из настроек компонента
            $modID = JModuleHelper::getModule('mod_mycityselector')->id;
            //$http = $MCS_BUFFER['http'];
            $http = 'http'; //TODO добавить метод определения http/https
            $currentCity = $parameters->get('defaultcity'); //TODO заменить на результат работы метода определения города
            $baseDomain = $parameters->get('basedomain');
            $cookieDomain = $parameters->get('basedomain');
            $this->params = $params = $MCS_BUFFER['params'];
            //$citiesList = $MCS_BUFFER['citiesList'];
            $citiesList = \plgSystemPlgMycityselector::$citiesList;
            $hasGroups = (count($citiesList) > 1);
            // => путь до файла шаблона
            //$layoutPath = JModuleHelper::getLayoutPath('mod_mycityselector', $params->get('layout', 'default'));
            $layoutPath = JModuleHelper::getLayoutPath('mod_mycityselector', 'default');
            // => URL до папки с шаблоном
            $myUrl = JURI::base() . str_replace(JPATH_BASE . '/', '', dirname($layoutPath)) . '/';
            // => подключаем файл шаблона

            // адрес возврата при редиректе на поддомен
            $returnUrl = JUri::getInstance()->toString();
            require $layoutPath;
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
            if (is_object($this->params)) {
                $default = $this->params->get($param, $default);
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

}

// ===================================================

// === Start module ===
new MyCitySelectorModule();