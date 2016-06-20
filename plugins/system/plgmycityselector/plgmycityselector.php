<?php
/**
 * Plugin of MyCitySelector extension
 */

defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');

JLoader::import('joomla.plugin.plugin');
JLoader::import('plugins.system.plgmycityselector.helpers.CitiesTagsHelper', JPATH_ROOT);

class plgSystemPlgMycityselector extends JPlugin
{
    /**
     * @var int Идентификатор модуля
     */
    private $modID = 0; // stay OK

    /**
     * @var JDatabaseDriver Ссылка на объект базы данных
     */
    private $db = null; // stay OK

    // todo все параметры свести к одному объекту (одному свойству)
    private $options = null;

    private $stat = null;


    /**
     * @var string Название текущего города
     */
    private $city = 'Москва'; // todo move to options object

    /**
     * @var bool Флаг указывающий на режим редактирования материала на frontend'е
     */
    private $editMode = false; // todo move to flags object

    /**
     * @var JRegistry Объект параметров модуля
     */
    public $params = null; // todo move to options object

    /**
     * @var \Joomla\Registry\Registry Объект параметров компонента
     */
    private $config;

    /**
     * @var string Основной домен сайта
     */
    private $baseDomain = ''; // todo move to options object

    /**
     * @var string Имя домена, для которого будут устанавливаться cookie
     */
    private $cookieDomain = ''; // todo move to options object

    /**
     * @var array Список городов из настроек модуля
     */
    public static $citiesList = []; // todo move to options object

    /**
     * @var bool Если в списке городов указаны поддомены, то равен true.
     */
    private $hasSubdomains = false; // todo move to options object

    /**
     * @var bool Если в списке городов указаны поддомены, то равен true.
     */
    private $http = 'http://'; // todo move to options object


    /**
     * Initialization
     */
    function __construct(&$subject, $params)
    {
        parent::__construct($subject, $params);

        // todo move methods to PlgOptionsHelper

        $this->db = JFactory::getDbo();
        $this->params = new JRegistry();
        // определяем ID текущего модуля
        $this->loadModuleData();
        $this->config = JComponentHelper::getParams('com_mycityselector');
        // проверка режима редактирования или админки
        $jInput = JFactory::getApplication()->input;
        $this->editMode = ($jInput->get('view') == 'form' && $jInput->get('layout') == 'edit');
        if (!$this->editMode && JFactory::getApplication()->getName() != 'administrator') {
            // https ?
            $this->http = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) ?
                'https://' : 'http://';
            // определяем базовый домен сайта
            $this->defineBaseDomain();

            // передаем в браузер параметры о домене
            JFactory::getDocument()->addScriptDeclaration(
                'window.mcs_base_domain="' . $this->baseDomain . '";' . // основной домен сайта, если есть еще и субдомены
                'window.mcs_cookie_domain="' . $this->cookieDomain . '";' // домен для которого нужно устанавливать кукисы
            );

            // формируем массив городов и определяем наличие поддоменов
            $this->getCitiesList();
            // определяем текущий город
            $this->defineCity();
            // проверяем соответствие текущего города с текущим адресом (поддоменом или адресом страницы)
            $this->autoSwitchCity();
        }
        // запоминаем для модуля, который будет вызван позднее
        $this->storeData();
    }


    /**
     * Event listener for content edit form
     * Adding our tab in form
     * @param $form
     * @param $data
     * @return bool
     */
    function onContentPrepareForm($form, $data) {

        // TODO move code to ArticleFormHelper

        $app = JFactory::getApplication();
        switch ($app->input->get('option')) {
            case 'com_content':
                if ($app->isAdmin() && JFactory::getApplication()->getName() == 'administrator') {
                    $string = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<form>
	<fields name="params" label="MyCitySelector">
	    <fieldset name="params" label="&#9733; MyCitySelector &#9733;">
			<field
				name="field1"
				type="text"
				label="Bla bla bla"
				/>
			<field
				name="field2"
				type="text"
				label="tututu"
			/>
        </fieldset>
	</fields>
</form>
XML;

                    $xml = simplexml_load_string($string);
                    $form->load($xml, true, false);
                }
                return true;
        }
        return true;
    }





    /**
     * Загружает все данные текущего модуля (ID и params)
     */
    private function loadModuleData()// todo move methods to PlgOptionsHelper
    {
        // сначала пытаемся получить ID из строки запроса (для админки)
        $jInput = JFactory::getApplication()->input;
        $id = $jInput->get('id', 0, 'int');
        $option = $jInput->get('option', '');
        $data = array();
        if ($option != 'com_modules' || $id == 0) {
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


    // todo здесь будет просто чтение из базы (из компонента)
    // todo move methods to PlgOptionsHelper
    /**
     * Подготавливает список городов, преобразуя его из строки в массив требуемого формата
     */
    private function getCitiesList()
    {
        // Если сайт сам по себе находится на поддомене,
        // (например sub.domain.ru это базовый адрес), то не должно происходить
        // никаких редиректов на domain.ru при выборе города пользователем.
        // Чтобы это определить, смотрим прописаны ли у каких-нибудь городов
        // субдомены (страницы не в счет) для редиректов.
        // И если нет, значит текущий субдомен основной.
        // Кроме того, запоминаем список городов в массив, для последующей проверки редиректа
/*        $citiesList = explode("\n", $this->params->get('cities_list', "Москва\nСанкт-Петербург"));
        $groupName = '';
        foreach ($citiesList as $index => $value) {
            // если для города указан субдомен или страница, то запись выглядит так: "Москва=moscow"
            list($name, $address) = explode('=', $value . '='); // поэтому отделим название города от субдомена
            $name = trim($name);
            $address = trim($address);
            if (!empty($name)) {
                if (substr($name, 0, 1) == '[' && substr($name, -1, 1) == ']') {
                    // это название группы
                    $groupName = trim(trim($name, '[]'));
                } else {
                    // это название города
                    $citiesList['__all__'][$name] = array('url' => '/#', 'subdomain' => '', 'path' => ''); // общий список городов
                    if (!empty($address)) {
                        if (stripos($address, '/') === false) {
                            // если указанный адрес для редиректа не содержит слеш, значит это субдомен
                            $this->hasSubdomains = true;
                            $citiesList['__all__'][$name]['subdomain'] = $address;
                            $citiesList['__all__'][$name]['url'] = $this->http . $address . '.' . $this->baseDomain;
                        } else {
                            $citiesList['__all__'][$name]['url'] = $this->http . $this->baseDomain . $address;
                            $citiesList['__all__'][$name]['path'] = $address;
                        }
                    }
                    if (!empty($groupName)) { // если название группы не пустое, дублируем город в эту группу
                        $citiesList[$groupName][$name] = $citiesList['__all__'][$name];
                    }
                }
            }
            unset($citiesList[$index]); // удаляем числовой индекс
        }*/
        // Получается массив вида: [
        //   '__all__' => [
        //      'Москва' => ['url' => 'moscow.site.ru', 'subdomain' => 'moscow', 'path' => ''],
        //      'Санкт-Петербург' => ['url' => 'spb.site.ru', 'subdomain' => 'spb', 'path' => ''],
        //      'Черемушки' => ['url' => '/other/cities', 'subdomain' => '', 'path' => '/other/cities']
        //   ],
        //   'Московская область' => [   # если группа была задана в настройках модуля
        //      'Москва' => ['url' => 'moscow.site.ru', 'subdomain' => 'moscow', 'path' => ''],
        //   ],
        //   ...
        // ]
        if (sizeof(self::$citiesList) > 0) return
        $citiesList = [];
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)->select('c.name as country, b.name as region, a.name as name, a.subdomain as subdomain')
            ->from('#__mycityselector_city a')
            ->leftJoin('#__mycityselector_region b on a.region_id = b.id')
            ->leftJoin('#__mycityselector_country c on a.country_id = c.id')
            ->where('a.status=1 AND b.status=1 AND c.status=1');
        $db->setQuery($query);
        $cities = $db->loadAssocList('name');
        $citiesList['__all__'] = $cities;
        foreach ($cities as $city ) {
            $citiesList[$city['region']][$city['name']] = $city;
        }


        self::$citiesList = $citiesList;
    }


    /**
     * Определение базового домена
     */
    private function defineBaseDomain()// todo move methods to PlgOptionsHelper
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
    }


    /**
     * Меняет название города в кукисах, если мы зашли на соответствущий поддомен или страницу
     */
    private function autoSwitchCity()
    {
        //TODO текущий город не переключается на значение по умолчанию при возврате с поддомена на основной домен.

        // это необходимо делать только когда пользователь первый раз открыл сайт,
        // то есть referer не соответствует нашему основному домену
        $referer = @$_SERVER['HTTP_REFERER'];
        if (!$this->editMode && strpos($referer, $this->baseDomain) === false) {
            // определяем, находимся ли мы на субдомене, и какой ему принадлежит город
            $hostParts = explode('.', $_SERVER['HTTP_HOST']);
            $subDomain = (count($hostParts) > 2) ? $hostParts[0] : null;  // $hostParts[0] - текущий субдомен
            if ($this->hasSubdomains && !empty($subDomain) && $subDomain != 'www') {
                foreach ($this->citiesList['__all__'] as $city => $data) {
                    if ($data['subdomain'] == $subDomain) {
                        $this->city = $city;
                        setcookie('MCS_CITY_NAME', $city, time() + 3600 * 24 * 30, '/', $this->cookieDomain);
                        break;
                    }
                }
            } else {
                // иначе, проверяем соответствие адреса странице (городу может быть назначена своя страница)
                $url = $_SERVER['REQUEST_URI'];
                if (strlen($url) > 1){
                    foreach ($this->citiesList['__all__'] as $city => $data) {
                        if (strlen($data['path']) > 1) {
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
        }
    }


    // определение текущего города
    private function defineCity()// todo move methods to PlgOptionsHelper
    {
        $doc = JFactory::getDocument();
        $defaultCity = $this->params->get('default_city', 'Москва');
        $baseIP = $this->params->get('baseip', 'none');
        if (isset($_GET['mcs']) && $_GET['mcs'] == 'clscookie') {
            unset($_COOKIE['mycity_selected_name']);
            unset($_COOKIE['MCS_CITY_NAME']);
        }
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
                // делаем запрос на Яндекс geolocation
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
            'http' => $this->http,
            'base_domain' => $this->baseDomain,
            'cookie_domain' => $this->cookieDomain,
            'city_name' => $this->city,
            'params' => $this->params,
            'citiesList' => $this->citiesList,
        );
        // дублируем в сессию для доступа из отдельных скриптов
        JFactory::getSession()->set('MCS_BUFFER', $MCS_BUFFER);
    }



    /**
     * Метод для вызова системным триггером.
     * Парсинг контента и "обворачивание" текста городов спец. тегами
     */
    public function onAfterRender()
    {

        // todo move to PlgContentParserHelper ?? need to think

        $jInput = JFactory::getApplication()->input;
        $option = $jInput->get('option');
        $id = $jInput->get('id');
        if (JFactory::getApplication()->getName() == 'administrator') { // не делаем замену блоков в админке
            if ($this->modID > 0 && $option == 'com_modules' && $id == $this->modID) {
                // подключаем скрипт расширенных параметров модуля
                $this->setPageBody($this->addBackendAssets($this->getPageBody()));
            }
        } else {
            if (!$this->editMode) { // не делаем замену в режиме редактирования статьи
                $body = $this->getPageBody();
                $body = CitiesTagsHelper::parseCitiesTags($body, $this->city, $this->citiesList); // парсим контент
                $body = $this->injectJSCallbackFunction($body);
                $this->setPageBody($body);
            }
        }
        return true;
    }


    /**
     * Внедряет на страницу настроек модуля дополнительные js скрипты
     * @param $body
     * @return mixed
     */
    private function addBackendAssets($body)
    {
        $css = '<link rel="stylesheet" href="/modules/mod_mycityselector/ext-params.css" type="text/css"/>' . "\n";
        $script = '';
        if (strpos($body, '/jquery.js') === false && strpos($body, '/jquery.min.js') === false) {
            // нужно подключить jQuery
            $script = '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js">'
                . "</script>\n" . '<script>jQuery.noConflict();</script>' . "\n";
        }
        $script .= '<script'
            . ' src="/modules/mod_mycityselector/jquery.tablednd.min.js">'
            . "</script>\n<script charset=\"utf-8\""
            . ' src="/modules/mod_mycityselector/ext-params.js.php?vpb8t9s23hx09g80hj56i345hiasdtf6q2">'
            . "</script>\n</head>";
        return str_replace('</head>', $css . $script, $body);
    }


    /**
     * Alias for APP->getBody();
     * @return string
     */
    private function getPageBody(){
        $app = JFactory::getApplication();
        if (method_exists($app, 'getBody')) {
            return $app->getBody(); // Joomla 3.x
        }
        // joomla 2.5
        return JResponse::getBody();
    }


    /**
     * Alias for APP->setBody();
     */
    private function setPageBody($body){
        $app = JFactory::getApplication();
        if (method_exists($app, 'setBody')) {
            $app->setBody($body); // Joomla 3.x
        } else {
            // joomla 2.5
            JResponse::setBody($body);
        }
    }


    /**
     * Добавляет на страницу js callback функцию
     * @param $body
     * @return mixed
     */
    private function injectJSCallbackFunction($body){
        $callback = trim($this->params->get('js_callback'));
        if (!empty($callback)) {
            return str_replace('</head>', "<script>\nfunction mcs_callback(){\n" . $callback . "\n}\n</script>\n</head>", $body);
        }
        return $body;
    }




}
