<?php
defined('_JEXEC') or die('Restricted access');
/*
* Плагин дополняющий модуль MyCitySelector
*/


jimport('joomla.plugin.plugin');

class plgSystemPlg_Mycityselector extends JPlugin
{

    /**
     * @var int Идентификатор модуля
     */
    private $modID = 0;

    /**
     * @var JDatabaseDriver Ссылка на объект базы данных
     */
    private $db = null;

    /**
     * @var string Название текущего города
     */
    private $city = 'Москва';

    /**
     * @var bool Флаг указывающий на режим редактирования материала на frontend'е
     */
    private $editMode = false;

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
    private $citiesList = array('__all__' => array());

    /**
     * @var bool Если в списке городов указаны поддомены, то равен true.
     */
    private $hasSubdomains = false;


    /**
     * Инициализация плагина
     */
    function __construct(&$subject, $params)
    {
        parent::__construct($subject, $params);
        $this->db = JFactory::getDbo();
        $this->params = new JRegistry();
        // определяем ID текущего модуля
        $this->loadModuleData();
        // проверка режима редактирования или админки
        $this->editMode = (JInput::get('view') == 'form' && JInput::get('layout') == 'edit');
        if (!$this->editMode && JFactory::getApplication()->getName() != 'administrator') {
            // формируем массив городов и определяем наличие поддоменов
            $this->parseCitiesList();
            // определяем базовый домен сайта
            $this->defineBaseDomain();
            // определяем текущий город
            $this->defineCity();
            // проверяем соответствие текущего города с текущим адресом (поддоменом или адресом страницы)
            $this->autoSwitchCity();
        }
        // запоминаем для модуля, который будет вызван позднее
        $this->storeData();
    }


    /**
     * Загружает все данные текущего модуля (ID и params)
     */
    private function loadModuleData()
    {
        // сначала пытаемся получить ID из строки запроса (для админки)
        $id = JInput::Get('id', 0, 'int');
        $option = JInput::Get('option', '');
        $data = array();
        if ($option != 'com_modules' && $id == 0) {
            // определяем текущий язык сайта
            $lang = JFactory::getLanguage()->getTag();
            // и по нему находим в базе запись о модуле с требуемым языком
            $res = $this->db->setQuery("SELECT * FROM `#__modules` WHERE `module`='mod_mycityselector'
                AND `language` IN (" . $this->db->quote($lang) . ", " . $this->db->quote('*') . ")")->loadAssocList();
            if (count($res) > 1) {
                foreach ($res as $row) {
                    if ($row['language'] == $lang) { // берем первое совпадение по языку
                        $data = $row;
                        break;
                    }
                }
            } elseif (count($res) == 1) {
                $data = $res[0];
            }
        } else {
            $res = $this->db->setQuery("SELECT * FROM `#__modules` WHERE `module`='mod_mycityselector'
                AND `id`={$id}")->loadAssocList();
            if (!empty($res)) {
                $data = $res[0];
            }
        }
        if (!empty($data)) {
            // если данные были загружены
            $id = $data['id']; // корректный id модуля
            $this->params->loadString($data['params']); // и его параметры
        }
        $this->modID = $id;
    }


    /**
     * Подготавливает список городов, преобразуя его из строки в массив требуемого формата
     */
    private function parseCitiesList()
    {
        // Если сайт сам по себе находится на поддомене,
        // (например sub.domain.ru это базовый адрес), то не должно происходить
        // никаких редиректов на domain.ru при выборе города пользователем.
        // Чтобы это определить, смотрим прописаны ли у каких-нибудь городов
        // субдомены (страницы не в счет) для редиректов.
        // И если нет, значит текущий субдомен основной.
        // Кроме того, запоминаем список городов в массив, для последующей проверки редиректа
        $citiesList = explode("\n", $this->params->get('cities_list', "Москва\nСанкт-Петербург"));
        $groupName = '';
        foreach ($citiesList as $index => $value) {
            // если для города указан субдомен или страница, то запись выглядит так: "Москва=moscow"
            list($name, $url) = explode('=', $value . '='); // поэтому отделим название города от субдомена
            $name = trim($name);
            $url = trim($url);
            if (!empty($name)) {
                if (substr($name, 0, 1) == '[' && substr($name, -1, 1) == ']') {
                    // это название группы
                    $groupName = trim(trim($name, '[]'));
                } else {
                    // это название города
                    $citiesList['__all__'][$name] = array('subdomain' => '', 'path' => ''); // общий список городов
                    if (!empty($url)) {
                        if (stripos($url, '/') === false) {
                            // если указанный адрес для редиректа не содержит слеш, значит это субдомен
                            $this->hasSubdomains = true;
                            $citiesList['__all__'][$name]['subdomain'] = $url;
                        } else {
                            $citiesList['__all__'][$name]['path'] = $url;
                        }
                    }
                    if (!empty($groupName)) { // если название группы не пустое, дублируем город в эту группу
                        $citiesList[$groupName][$name] = $citiesList['__all__'][$name];
                    }
                }
            }
            unset($citiesList[$index]); // удаляем числовой ключ
        }
        // Получается массив вида: [
        //   '__all__' => [
        //      'Москва' => ['subdomain' => 'moscow', 'path' => ''],
        //      'Санкт-Петербург' => ['subdomain' => 'spb', 'path' => ''],
        //      'Черемушки' => ['subdomain' => '', 'path' => '/other/cities']
        //   ],
        //   'Московская область' => [   # если группа была задана в настройках модуля
        //      'Москва' => ['subdomain' => 'moscow', 'path' => ''],
        //   ],
        //   ...
        // ]
        $this->citiesList = $citiesList;
    }


    /**
     * Определение базового домена
     */
    private function defineBaseDomain()
    {
        $host = $_SERVER['HTTP_HOST'];
        // проверяем параметр модуля main_domain, если основной домен указан, то автоматическое определение пропускаем
        $baseDomain = trim($this->params->get('main_domain'));
        $baseDomain = str_replace(array('https://', 'http://'), array('', ''), $baseDomain);
        if (substr($baseDomain, 0, 4) == 'www.') {
            $baseDomain = substr($baseDomain, 5);
        }
        if (!empty($baseDomain) && strpos($host, $baseDomain) !== false) {
            $this->baseDomain = $baseDomain;
            $this->cookieDomain = '.' . $this->baseDomain;
        } else {
            // автоматическое определение
            $this->baseDomain = $host; // по умолчанию считаем текущий хост основным доменом
            $this->cookieDomain = '.' . $host;
            $part = explode('.', $host);
            $len = count($part);
            if ($this->hasSubdomains || ($len > 2 && $part[0] == 'www')) {
                // сайт имеет поддомены для нескольких городов, а значит кукисы должны распростарняться на всех
                $this->baseDomain = $part[$len - 2] . '.' . $part[$len - 1];
                $this->cookieDomain = '.' . $this->baseDomain;
            }
        }
        // передаем в браузер параметры о домене
        JFactory::getDocument()->addScriptDeclaration(
            'window.mcs_base_domain="' . $this->baseDomain . '";' . // основной домен сайта, если есть еще и субдомены
            'window.mcs_cur_domain="' . $host . '";' . // текущий хост (например субдомен)
            'window.mcs_cookie_domain="' . $this->cookieDomain . '";' // домен для которого нужно устанавливать кукисы
        );
    }


    /**
     * Меняет название города в кукисах, если мы зашли на соответствущий поддомен или страницу
     */
    private function autoSwitchCity()
    {
        // это необходимо делать только когда пользователь первый раз открыл сайт,
        // то есть referer не соответствует нашему основному домену
        $referer = @$_SERVER['HTTP_REFERER'];
        if (!$this->editMode && strpos($referer, $this->baseDomain) === false) {
            // определяем, находимся ли мы на субдомене, и какой ему принадлежит город
            $hostParts = explode('.', $_SERVER['HTTP_HOST']);
            $subDomain = (count($hostParts) > 2) ? $hostParts[0] : null;  // $hostParts[0] - текущий субдомен
            if ($this->hasSubdomains && !empty($subDomain) && $subDomain != 'www') {
                foreach ($this->citiesList as $city => $data) {
                    if ($data['subdomain'] == $subDomain) {
                        $this->city = $city;
                        setcookie('MCS_CITY_NAME', $city, time() + 3600 * 24 * 30, '/', $this->cookieDomain);
                        break;
                    }
                }
            } else {
                // иначе, проверяем соответствие адреса странице (городу может быть назначена своя страница)
                $url = $_SERVER['REQUEST_URI'];
                foreach ($this->citiesList as $city => $data) {
                    $len = mb_strlen($data['path'], 'utf8');
                    $path = mb_substr($url, 0, $len, 'utf8');
                    if ($data['path'] == $path) {
                        $this->city = $city;
                        setcookie('MCS_CITY_NAME', $city, time() + 3600 * 24 * 30, '/', $this->cookieDomain);
                        break;
                    }
                }
            }
        }
    }


    // определение текущего города
    private function defineCity()
    {
        $doc = JFactory::getDocument();
        $defaultCity = $this->params->get('default_city', 'Москва');
        $baseIP = $this->params->get('baseip', 'none');
        // берем название текущего города из кукисов (город может быть любым, не обязательно из настроек)
        $city = isset($_COOKIE['MCS_CITY_NAME']) ? $_COOKIE['MCS_CITY_NAME'] : '';

        if (empty($city)) {
            // пользователь еще не выбирал свой город (он не сохранен в кукисах)
            if ($this->params->get('let_select', '1') == '1') {
                $doc->addScriptDeclaration('window.mcs_dialog=1;'); // отобразить окно выбора города
            } else {
                $doc->addScriptDeclaration('window.mcs_dialog=2;'); // отобразить предложение о смене города
            }
            // если по поддомену город не определен, переходим к geoip базам
            if ($baseIP == 'none') {
                // не использовать автоопределение города
                $city = $defaultCity;
            } elseif ($baseIP == 'sypex') {
                // делаем запрос на API Sypex Geo
                $city = $this->sypexGeoIP($_SERVER['REMOTE_ADDR'], $defaultCity);
            } elseif ($baseIP == 'sypex_yandexgeo') {
                // делаем запрос на API Sypex Geo + корректируем город через Яндекс geolocation
                $city = $this->sypexGeoIP($_SERVER['REMOTE_ADDR'], $defaultCity);
                $doc->addScriptDeclaration('window.mcs_yandexgeo=true;');
            } elseif ($baseIP == 'yandexgeo') {
                // делаем запрос на API Sypex Geo + корректируем город через Яндекс geolocation
                $city = $this->sypexGeoIP($_SERVER['REMOTE_ADDR'], $defaultCity);
                $doc->addScriptDeclaration('window.mcs_yandexgeo=true;');
            }
            // сохраняем определенный город в cookie
            setcookie('MCS_CITY_NAME', $city, time() + 3600 * 24 * 30, '/', $this->cookieDomain);
        } else {
            $doc->addScriptDeclaration('window.mcs_dialog=0;'); // никаких действий с выбором города
        }
        $this->city = $city;
    }


    /**
     * Сохраняет все полученные плагином данные в глобальную переменную
     */
    private function storeData(){
        global $MCS_BUFFER; // глобальная переменная для передачи информации от плагина к модулю
        // поскольку плагин вызывается раньше модуля, то все полученные им данные мы передаем модулю
        // в готовом виде, чтобы не делать таких же вычислений повторно в модуле
        $MCS_BUFFER = array(
            'mod_id' => $this->modID,
            'base_domain' => $this->baseDomain,
            'cookie_domain' => $this->cookieDomain,
            'city_name' => $this->city,
            'params' => $this->params,
            'citiesList' => $this->citiesList,
        );
    }


    /**
     * Определяет город с помощью сервиса sypexgeo.net
     */
    private function sypexGeoIP($ip, $defaultCity=''){
        $ch = curl_init();
        // документация: http://sypexgeo.net/ru/api/
        curl_setopt($ch, CURLOPT_URL, 'http://api.sypexgeo.net/json/' . $ip);
        curl_setopt($ch, CURLOPT_USERAGENT, 'User-Agent:Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/537.36');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2); // устанавливаем минимальные временные рамки для связи с api
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $resp = curl_exec($ch);
        $resp = json_decode($resp, true); // Array ( [ip] => 127.0.0.1 [city] => [region] => [country] => )
        if (is_array($resp)
            && isset($resp['city'])
            && isset($resp['city']['name_ru'])
            && !empty($resp['city']['name_ru'])) {
                // город успешно определен
                return $resp['city']['name_ru'];
        }
        return $defaultCity;
    }


    /**
     * Метод для вызова системным триггером.
     * Парсинг контента и "обворачивание" текста городов спец. тегами
     */
    public function onAfterRender()
    {
        $app = JFactory::getApplication();
        // $body = JResponse::getBody(); OLD
        $body = $app->getBody();
        if ($app->getName() == 'administrator') { // не делаем замену в админке
            if ($this->modID > 0 && JInput::Get('option') == 'com_modules' && JInput::Get('id') == $this->modID) {
                // подключаем скрипт расширенных параметров модуля
                $body = str_replace('</head>', '<script src="/modules/mod_mycityselector/ext-params.php"></script>' . "\n</head>", $body);
                $app->setBody($body);
            }
        } else {
            if (!$this->editMode) { // не делаем замену в режиме редактирования статьи
                $body = $this->parsePage_ReplaceContent($body); // парсим контент
                $app->setBody($body);
            }
        }
        return true;
    }


    private function parsePage_ReplaceContent($body)
    {
        // сложность в том, что в визуальном редакторе метки обрамляются тегами
        for ($i = 3; $i--;) { // надеюсь, что больше трех оберточных тегов не будет
            $body = preg_replace('/<span[^>]+>(\[city\s+[^\]]+\])<\/span>/usU', '$1', $body);
            $body = preg_replace('/<span>(\[\/city\s*\])<\/span>/sU', '$1', $body);
            $body = preg_replace('/<p[^>]+>(\[city\s+[^\]]+\])<\/p>/usU', '$1', $body);
            $body = preg_replace('/<p>(\[\/city\s*\])<\/p>/sU', '$1', $body);
        }
        // теперь, когда лищнее почистили, ищем блоки
        $reg = '/\[city\s+([^\]]+)\](.+)\[\/city\s*\]/usU';
        $cnt = preg_match_all($reg, $body, $res);
        if ($cnt > 0) {
            $json = array();
            // в цикле перебираем найденные куски, прячем все в js массив, кроме совпадающего с текущим городом
            $finded = false;
            // echo '<pre>'; print_r($res); echo '</pre>';
            foreach ($res[1] as $i => $city) { // цикл по названиям городов из результата regexp
                // $res[0][$i] - весь найденный блок
                // $res[1][$i] - название города
                // $res[2][$i] - контент блока
                // иногда бывает внутри блока перенос параграфа, например так: <p><span.cityContent>ляляля</p><p>тополя</span></p> браузер это не переваривает и давится
                // поэтому заменяем это дело внутри блока на <br/>
                $content = $res[2][$i];
                $content = preg_replace('/<\/p>\s*<p>/smU', '<br/>', $content);
                // один из плагинов, работающий выше по порядку, почему-то конвертирует русские буквы в html последовательности (вот гад)
                // пришлось сделать проверку названия города
                if (strpos($city, '&#x') !== false) {
                    $city = html_entity_decode($city, ENT_COMPAT, 'UTF-8');
                }
                // разделяем по ","
                $cities = explode(',', $city);
                // проверяем первый символ первого города, если он равен "!", значит это условие исключения
                if (mb_substr(trim($cities[0]), 0, 1, 'UTF-8') == '!') {
                    $cities[0] = str_replace('!', '', $cities[0]);
                    // теперь нужно составить новый список городов, за исключением тех, что в текущем
                    $newCities = $this->citiesList;
                    foreach ($cities as $cval) {
                        if (isset($newCities[$cval])) {
                            unset($newCities[$cval]);
                        }
                    }
                    $cities = array_keys($newCities);
                    unset($newCities);
                }
                if (count($cities) > 1) {
                    // формируем групповой блок, если городов > 1
                    $trcity = 'cities-group'; // группа
                    if (!isset($json[$trcity])) {
                        $json[$trcity] = array();
                    }
                    $index = count($json[$trcity]);
                    $json[$trcity][$index] = $content;
                    // для всех городов в группе, создаем блоки со ссылкой на групповой
                    $class = '';
                    $findedInGroup = false;
                    foreach ($cities as $cval) {
                        $cval = trim($cval);
                        if ($cval == '*') {
                            continue;
                        } else {
                            $trcityG = $this->translit($cval);
                        }
                        if (!isset($json[$trcityG])) {
                            $json[$trcityG] = array();
                        }
                        $indexG = count($json[$trcityG]);
                        $json[$trcityG][$indexG] = '{{$cities-group}}=' . $index; // ссылка на групповой блок с общим контентом
                        $class .= ' city-' . $trcityG . '-' . $indexG;
                        if ($cval == $this->city) {
                            $findedInGroup = true; // этот блок для текущего выбранного города, ставим флаг, чтобы не отображался блок [city *]
                            $finded = true;
                        }
                    }
                    $html = '<span class="cityContent' . $class . '">';
                    if ($findedInGroup == true) {
                        $html .= $content;
                    }
                    $html .= '</span>';
                } else {
                    // иначе одиночный блок
                    $cval = trim($cities[0]);
                    if ($cval == '*') {
                        $trcity = 'other';
                    } else {
                        $trcity = $this->translit($cval);
                    }
                    if (!isset($json[$trcity])) {
                        $json[$trcity] = array();
                    }
                    $index = count($json[$trcity]);
                    // $json[ город ][номер блока с текстом ]
                    $json[$trcity][$index] = $content;
                    $class = ' city-' . $trcity . '-' . $index;
                    $html = '<span class="cityContent' . $class . '">';
                    if ($cval == $this->city) {
                        $finded = true; // этот блок для текущего выбранного города, ставим флаг, чтобы не отображался блок [city *]
                        $html .= $content;
                    }
                    $html .= '</span>';
                }
                $body = str_replace($res[0][$i], $html, $body);
            }
            if ($finded == false && isset($json['other'])) {
                // если город не был найден, то при наличии блока "прочие", подставляем текст обратно в страницу
                foreach ($json['other'] as $index => $content) {
                    $body = str_replace(
                        '<span class="cityContent city-other-' . $index . '"></span>',
                        '<span class="cityContent city-other-' . $index . '">' . $content . '</span>',
                        $body
                    );
                }
            }
            // формируем json
            $json = '<script type="text/javascript">var citySelectorContents = ' . json_encode($json) . ';</script>';
            $body = str_replace('</head>', $json . "\n</head>", $body);
        }

        return $body;
    }


    private function translit($str)
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