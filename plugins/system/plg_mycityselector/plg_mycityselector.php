<?php
defined('_JEXEC') or die('Restricted access');
/*
* Плагин дополняющий модуль MyCitySelector
*/

jimport('joomla.plugin.plugin');

class plgSystemPlg_Mycityselector extends JPlugin
{

    private $city = 'Москва';
    private $editMode = false;
    public $params = null;
    private $domain = null;
    private $protocol = 'http://';
    private $citiesList = array();
    private $cookieDomain = null;
    private $modID = 0;
    private $citiesHaveSubdomains = false; // если в списке городов прописаны субдомены для редиректа

    // инициализация параметров
    function __construct(&$subject, $params)
    {
        parent::__construct($subject, $params);
        $this->protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $this->modID = $this->getId();
        // проверка режима редактирования/админки
        if ((JRequest::getVar('view') == 'form' && JRequest::getVar('layout') == 'edit')
            || substr($_SERVER['REQUEST_URI'], 0, 14) == '/administrator'
        ) {
            $this->editMode = true;
            return;
        }
        // загрузка параметров модуля mod_mycityselector
        $this->loadParams();
        // формируем массив городов и определяем наличие поддоменов
        $this->prepareCitiesList();
        // определяем базовый домен сайта
        $this->getDomain();
        // определяем текущий город
        $city = $this->defineCity();
        if (!empty($city)) {
            $this->city = $city;
        }
        // проверяем соответствие текущего города с текущим адресом (поддоменом или адресом страницы)
        $this->checkUrlCity();
        // запоминаем для модуля, который будет вызван позднее
        global $MSC_BASE_DOMAIN;
        global $MSC_CURRENT_CITY;
        $MSC_BASE_DOMAIN = $this->domain;
        $MSC_CURRENT_CITY = $this->city;
    }


    private function getId()
    {
        $id = intval(JRequest::getVar('id'));
        $option = JRequest::getVar('option');
        if ($option != 'com_modules' && $id == 0) {
            $lang = JFactory::getLanguage();
            $lang = $lang->getTag();
            $db = JFactory::getDbo();
            $db->setQuery("SELECT * FROM `#__modules` WHERE `module`='mod_mycityselector'
                AND `language` IN (" . $db->quote($lang) . ", " . $db->quote('*') . ")");
            if ($res = $db->loadAssocList()) {
                if (count($res) > 1) {
                    foreach ($res as $row) {
                        if ($row['language'] == $lang) {
                            $id = $row['id'];
                            break;
                        }
                    }
                } else {
                    $id = $res[0]['id'];
                }
            }
        }
        return $id;
    }


    // загрузка параметров
    private function loadParams()
    {
        $db = JFactory::getDbo();
        if ($this->modID > 0) {
            $db->setQuery("SELECT * FROM `#__modules` WHERE `module`='mod_mycityselector' AND `id`={$this->modID}");
        } else {
            $db->setQuery("SELECT * FROM `#__modules` WHERE `module`='mod_mycityselector'");
        }
        $res = $db->loadAssocList();
        $params = new JRegistry();
        if (!empty($res)) {
            $params->loadString($res[0]['params']);
        }
        $this->params = $params;
    }


    private function prepareCitiesList()
    {
        // тут внимание, если сайт сам по себе находится на поддомене,
        // то есть sub.domain.ru это его базовый адрес, то не должно происходить
        // никаких редиректов на domain.ru при выборе города польхователем
        // Чтобы это определить, смотрим прописаны ли у каких-нибудь городов
        // субдомены (страницы не в счет) для редиректов.
        // И если нет, значит текущий субдомен основной.
        // Кроме того, запоминаем список городов в массив, для последующей проверки редиректа
        $citiesList = explode("\n", $this->params->get('cities_list', "Москва\nСанкт-Петербург"));
        foreach ($citiesList as $i => $v) {
            $v = trim($v);
            if (!empty($v)) {
                $v = explode('=', $v);
                $v[0] = trim($v[0]);
                if (isset($v[1])) {
                    if (substr(trim($v[1]), 0, 1) != '/') {
                        $this->citiesHaveSubdomains = true; // есть субдомены
                    }
                    $this->citiesList[$v[0]] = trim($v[1]);
                } else {
                    $this->citiesList[$v[0]] = false;
                }
            }
        }
    }


    // определение параметров домена
    private function getDomain()
    {
        $host = explode('.', $_SERVER['HTTP_HOST']);
        $dlen = count($host);
        $this->domain = $this->cookieDomain = $_SERVER['HTTP_HOST']; // по умолчанию считаем текущий хост основным доменом
        if ($this->citiesHaveSubdomains || ($dlen > 2 && $host[0] == 'www')) {
            // сайт имеет поддомены для нескольких городов, а значит кукисы должны распростарняться на всех
            $this->domain = $host[$dlen - 2] . '.' . $host[$dlen - 1];
            $this->cookieDomain = '.' . $this->domain;
        }
        // передаем в браузер некоторые параметры о домене
        $doc = JFactory::getDocument();
        $doc->addScriptDeclaration(
            'window.mcs_base_domain="' . $this->domain . '";' . // основной домен сайта, если есть еще и субдомены
            'window.msc_cur_domain="' . $_SERVER['HTTP_HOST'] . '";' . // текущий хост (например субдомен)
            'window.msc_cookie_domain="' . $this->cookieDomain . '";' // домен для которого нужно устанавливать кукисы
        );
    }


    // меняет название города в кукисах, если мы зашли на соответствущий поддомен
    private function checkUrlCity()
    {
        if ($this->editMode) {
            return;
        }
        // определяем, находимся ли мы на субдомене, и какой ему принадлежит город
        $host = explode('.', $_SERVER['HTTP_HOST']);
        if ($this->citiesHaveSubdomains && count($host) > 2 && $host[0] != 'www') {
            // $host[0] - текущий субдомен
            foreach ($this->citiesList as $city => $url) {
                if ($url == $host[0]) {
                    $this->city = $city;
                    setcookie('mycity_selected_name', $city, time() + 3600 * 24 * 30, '/', $this->cookieDomain);
                    return;
                }
            }
        }
    }


    // определение текущего города
    private function defineCity()
    {
        $defaultCity = $this->params->get('default_city', 'Москва');
        $citiesList = explode("\n", $this->params->get('cities_list', "Москва\nСанкт-Петербург"));
        $jcList = "\n" . 'window.msc_list = [';
        foreach ($citiesList as $i => $_city) {
            $_city = explode('=', trim($_city));
            if (isset($_city[1]) && !empty($_city[1]) && substr($_city[1], 0, 1) != '/') {
                // подменяем числовой ключ на имя поддомена (нужно для поиска ниже)
                unset($citiesList[$i]);
                $i = $_city[1];
            }
            $citiesList[$i] = trim($_city[0]);
            $jcList .= '"' . $citiesList[$i] . '",';
        }
        $jcList = trim($jcList, ',') . '];' . "\n";
        $doc = JFactory::getDocument();
        $doc->addScriptDeclaration($jcList);
        $baseIP = $this->params->get('baseip', 'none');
        $city = JRequest::getVar('mycity');
        if (empty($city) || empty($_COOKIE['mycity_selected_name'])) {
            if (isset($_COOKIE['mycity_selected_name']) && !empty($_COOKIE['mycity_selected_name'])) {
                // выбранный город
                $city = $_COOKIE['mycity_selected_name'];
                $doc->addScriptDeclaration('window.mcsdialog=0;'); // никаких действий с выбором города
            } else {
                // пользователь еще не выбирал свой город (он не сохранен в кукисах)
                if ($this->params->get('let_select', '1') == '1') {
                    $doc->addScriptDeclaration('window.mcsdialog=1;' . "\n"); // отобразить окно выбора города
                } else {
                    $doc->addScriptDeclaration('window.mcsdialog=2;' . "\n"); // помаячить предложением сменить город
                }
                // прежде чем делать автоопределение города, посмотрим, на каком поддомене мы находимся
                // и соответствует ли он какому-то городу
                if ($this->citiesHaveSubdomains) {
                    $subdomain = str_replace($this->domain, '', $_SERVER['HTTP_HOST']);
                    $subdomain = trim(trim($subdomain, '.'));
                    if (!empty($subdomain) && isset($citiesList[$subdomain])) {
                        $city = $citiesList[$subdomain];
                    }
                }
                if (empty($city)) {
                    // если по поддомену город не определен, переходим к geoip базам
                    if ($baseIP == 'none') {
                        // не использовать автоопределение города
                        $city = $defaultCity;
                    } elseif ($baseIP == 'sypex') {
                        // делаем запрос на API Sypex Geo
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, 'http://api.sypexgeo.net/json/' . $_SERVER['REMOTE_ADDR']);
                        // curl_setopt( $ch, CURLOPT_URL, 'http://api.sypexgeo.net/json/217.118.79.19' );
                        curl_setopt($ch, CURLOPT_USERAGENT, 'User-Agent:Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/537.36');
                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2); // устанавливаем минимальные временные рамки для связи с api
                        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
                        curl_setopt($ch, CURLOPT_HEADER, 0);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        $resp = curl_exec($ch);
                        $resp = json_decode($resp, true); // Array ( [ip] => 127.0.0.1 [city] => [region] => [country] => )
                        if (!is_array($resp)) {
                            $resp = array();
                            /* ответ типа такого
                            Array(
                                [ip] => 217.118.79.19
                                [city] => Array
                                    (
                                        [id] => 498817
                                        [lat] => 59.93863
                                        [lon] => 30.31413
                                        [name_ru] => Санкт-Петербург
                                        [name_en] => Saint Petersburg
                                        [okato] => 40
                                    )

                                [region] => Array
                                    (
                                        [id] => 536203
                                        [lat] => 59.92
                                        [lon] => 30.25
                                        [name_ru] => Санкт-Петербург
                                        [name_en] => Sankt-Peterburg
                                        [iso] => RU-SPE
                                        [timezone] => Europe/Moscow
                                        [okato] => 40
                                    )

                                [country] => Array
                                    (
                                        [id] => 185
                                        [iso] => RU
                                        [continent] => EU
                                        [lat] => 60
                                        [lon] => 100
                                        [name_ru] => Россия
                                        [name_en] => Russia
                                        [timezone] => Europe/Moscow
                                    )

                            ) */
                        }
                        $city = isset($resp['city']) ? $resp['city']['name_ru'] : $defaultCity;
                    }
                }
                if (empty($city)) {
                    $city = $defaultCity;
                }
                setcookie('mycity_selected_name', $city, time() + 3600 * 24 * 30, '/', $this->cookieDomain);
            }
        }
        return $city;
    }


    // парсинг контента и "обворачивание" текста городов спец. тегами
    function onAfterRender()
    {
        $app = JFactory::getApplication();
        $body = JResponse::getBody();
        if ($app->getName() == 'administrator') { // не делаем замену в админке
            if ($this->modID > 0 && JRequest::getVar('option') == 'com_modules' && JRequest::getVar('id') == $this->modID) {
                // подключаем скрипт расширенных параметров модуля
                $body = str_replace('</head>', '<script src="/modules/mod_mycityselector/ext-params.php"></script>' . "\n</head>", $body);
                JResponse::setBody($body);
            }
        } else {
            if ($this->editMode) {
                return;
            } // не делаем замену в режиме редактирования статьи
            $body = $this->parsePage_ReplaceContent($body); // парсим контент
            JResponse::setBody($body);
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
