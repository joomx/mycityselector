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
        global $MCS_BUFFER;
        if (!empty($MCS_BUFFER)) {
            // => берем данные из плагина (он вызывается раньше модуля)
            $modID = $MCS_BUFFER['mod_id'];
            $http = $MCS_BUFFER['http'];
            $currentCity = $MCS_BUFFER['city_name'];
            $baseDomain = $MCS_BUFFER['base_domain'];
            $cookieDomain = $MCS_BUFFER['cookie_domain'];
            $this->params = $params = $MCS_BUFFER['params'];
            $citiesList = $MCS_BUFFER['citiesList'];
            $hasGroups = (count($citiesList) > 1);
            // => путь до файла шаблона
            $layoutPath = JModuleHelper::getLayoutPath('mod_mycityselector', $params->get('layout', 'default'));
            // => URL до папки с шаблоном
            $myUrl = JURI::base() . str_replace(JPATH_BASE . '/', '', dirname($layoutPath)) . '/';
            // => подключаем файл шаблона
            require $layoutPath;
        } else {
            $find = JFactory::getDbo()
                ->setQuery("SELECT COUNT(*) AS `cnt` FROM `#__extensions` WHERE `element`='plg_mycityselector'")
                ->loadResult();
            if ($find) {
                $err = 'Плагин MyCitySelector не активен!';
            } else {
                $err = 'Плагин MyCitySelector не установлен!';
            }
            echo '<span style="color:red;">' . $err . '</span>';
        }
    }


    /**
     * Inject jQuery framework for Joomla 2.5
     */
    public function addJQuery(){
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
    public function get($param, $default='')
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
        $str = trim($str);
        $letters = array(
            'й' => 'j', 'ц' => 'ts', 'у' => 'u', 'к' => 'k', 'е' => 'e', 'н' => 'n', 'г' => 'g', 'ш' => 'sh', 'щ' => 'sch',
            'з' => 'z', 'х' => 'h', 'ъ' => '', 'ф' => 'f', 'ы' => 'y', 'в' => 'v', 'а' => 'a', 'п' => 'p', 'р' => 'r', 'о' => 'o',
            'л' => 'l', 'д' => 'd', 'ж' => 'zh', 'э' => 'e', 'я' => 'ya', 'ч' => 'ch', 'с' => 's', 'м' => 'm', 'и' => 'i',
            'т' => 't', 'ь' => '', 'б' => 'b', 'ю' => 'yu', 'ё' => 'e',
            'Й' => 'j', 'Ц' => 'ts', 'У' => 'u', 'К' => 'k', 'Е' => 'e', 'Н' => 'n', 'Г' => 'g', 'Ш' => 'sh', 'Щ' => 'sch',
            'З' => 'z', 'Х' => 'h', 'Ъ' => '', 'Ф' => 'f', 'Ы' => 'y', 'В' => 'v', 'А' => 'a', 'П' => 'p', 'Р' => 'r', 'О' => 'o',
            'Л' => 'l', 'Д' => 'd', 'Ж' => 'zh', 'Э' => 'e', 'Я' => 'ya', 'Ч' => 'ch', 'С' => 's', 'М' => 'm', 'И' => 'i',
            'Т' => 't', 'Ь' => '', 'Б' => 'b', 'Ю' => 'yu', 'Ё' => 'e', ' ' => '_', '-' => '_', ',' => '_', '?' => '_', '!' => '_',
            '/' => '_', '(' => '_', ')' => '_', '___' => '_', '__' => '_'
        );
        foreach ($letters as $key => $value) {
            $str = str_replace($key, $value, $str);
        }
        $str = strtolower($str);
        return $str;
    }

}

// === Start module ===
new MyCitySelectorModule();