<?php
/**
 * Модуль MyCitySelector
 * Отвечает за вывод диалогового окна со списком городов и переключение между ними
 */

defined('_JEXEC') or exit('Restricted access');


class MyCitySelectorModule
{

    /**
     * @var int Идентификатор текущего объекта модуля
     */
    private $id = 0;

    /**
     * @var JDatabaseDriver Ссылка на объект базы данных
     */
    private $db = null;

    /**
     * @var string Название текущего города
     */
    private $city = '';

    /**
     * @var JRegistry Объект параметров модуля
     */
    public $params = null;

    /**
     * @var string Основной домен сайта
     */
    private $baseDomain = '';

    /**
     * @var string Имя домена, для которого будут устанавливаться cookie
     */
    private $cookieDomain = '';

    /**
     * @var array Список городов из настроек модуля
     */
    private $citiesList = array();


    /**
     * Инициализация модуля
     */
    public function __construct()
    {
        global $MCS_BUFFER;
        $this->db = JFactory::getDbo();
        if (!empty($MCS_BUFFER)) {
            // берем данные из плагина (он вызывается раньше модуля)
            $this->id = $MCS_BUFFER['mod_id'];
            $this->city = $MCS_BUFFER['city_name'];
            $this->baseDomain = $MCS_BUFFER['base_domain'];
            $this->cookieDomain = $MCS_BUFFER['cookie_domain'];
            $this->params = $MCS_BUFFER['params'];
            $this->citiesList = $MCS_BUFFER['citiesList'];
            // загружаем шаблон
            $this->loadTemplate();
        } else {
            $find = $this->db->setQuery("SELECT COUNT(*) AS `cnt` FROM `#__extensions` WHERE `element`='plg_mycityselector'")->loadResult();
            if ($find) {
                $err = 'Плагин MyCitySelector не активен!';
            } else {
                $err = 'Плагин MyCitySelector не установлен!';
            }
            echo '<span style="color:red;">' . $err . '</span>';
        }
    }


    /**
     * Загружает шаблон модуля
     */
    private function loadTemplate()
    {
        // определяем требуемые для шаблона переменные
        $city = $this->city;
        $citiesList = $this->citiesList;
        $baseDomain = $this->baseDomain;
        $cookieDomain = $this->cookieDomain;
        $params = $this->params;
        $layoutPath = JModuleHelper::getLayoutPath('mod_mycityselector', $this->params->get('layout', 'default'));
        $myUrl = JURI::base() . str_replace(JPATH_BASE . '/', '', dirname($layoutPath)) . '/'; // URL до папки с шаблоном

        // подключаем файл шаблона
        require $layoutPath;
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
    public function addStyleSheet($url, $type = 'text/css', $media = null, $attribs = array())
    {
        JFactory::getDocument()->addStyleSheet($url, $type, $media, $attribs);
    }


    /**
     * Переводит названия городов в транслит, чтобы формировать идентификаторы для js
     * @param String $str Строка для транслитерации
     * @return String
     */
    public static function translit($str)
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