<?php
/**
 * Plugin of MyCitySelector extension
 */

defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');

JLoader::import('joomla.plugin.plugin');
JLoader::import('plugins.system.plgmycityselector.helpers.CitiesTagsHelper', JPATH_ROOT);
JLoader::import('plugins.system.plgmycityselector.helpers.ArticleFormHelper', JPATH_ROOT);
JLoader::import('plugins.system.plgmycityselector.helpers.McsSettings', JPATH_ROOT);

class plgSystemPlgMycityselector extends JPlugin
{

    /* TODO
        1. Запоминать в кукисы будем не название города, а slug.
        2. Все настройки и города будут считываться в McsData
        3.
     */


    /**
     * Initialization
     */
    function __construct(&$subject, $params)
    {
        parent::__construct($subject, $params);
        // check for "backend mode" and "frontend edit mode"
        $jInput = JFactory::getApplication()->input;
        $this->editMode = ($jInput->get('view') == 'form' && $jInput->get('layout') == 'edit');
        if (!$this->editMode && JFactory::getApplication()->getName() != 'administrator') {
            McsData::load(false); // load only settings

            // TODO
            // Определяем город из cookie
            $this->defineCityByCookie();
            // определяем город по домену
            $this->defineCityByDomain();

        } else {
            McsData::load(true); // load all settings and cities
        }
    }


    /**
     * Event listener for content edit form
     * Adding our tab in form
     * @param $form
     * @param $data
     * @return bool
     */
    function onContentPrepareForm($form, $data) {
        // TODO
        // ArticleFormHelper::addWidget();
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
    private function defineCity()
    {
        $doc = JFactory::getDocument();
        $defaultCity = $this->comParams->get('default_city', 'Москва');
        $baseIP = $this->comParams->get('baseip', 'none');
        if (isset($_GET['mcs']) && $_GET['mcs'] == 'clscookie') {
            unset($_COOKIE['mycity_selected_name']);
            unset($_COOKIE['MCS_CITY_NAME']);
        }
        // берем название текущего города из кукисов (город может быть любым, не обязательно из настроек)
        $city = isset($_COOKIE['MCS_CITY_NAME']) ? $_COOKIE['MCS_CITY_NAME'] : '';
        if (empty($city)) {
            // пользователь еще не выбирал свой город (он не сохранен в кукисах)
            if ($this->comParams->get('let_select', '1') == '1') {
                $doc->addScriptDeclaration('window.mcs_dialog=1;'); // отобразить окно выбора города
            } else {
                $doc->addScriptDeclaration('window.mcs_dialog=2;'); // отобразить предложение о смене города
            }
            // если по поддомену город не определен, переходим к geoip базам
            if ($baseIP == 'none') {
                // не использовать автоопределение города
                $city = $defaultCity;
            } elseif ($baseIP == 'yandexgeo') {
                // делаем запрос на Яндекс geolocation
                $doc->addScriptDeclaration('window.mcs_yandexgeo=true;');
            }
            // сохраняем определенный город в cookie
            //setcookie('MCS_CITY_NAME', $city, time() + 3600 * 24 * 30, '/', $this->cookieDomain);
        } else {
            $doc->addScriptDeclaration('window.mcs_dialog=0;'); // никаких действий с выбором города
        }
        $this->city = $city;
    }

    /**
     * Определяет город по домену (если город есть в базе и он опубликован)
     */
    private function defineCityByDomain () {
        $domain = $_SERVER['HTTP_HOST'];
        $sdl = stripos($domain,$this->baseDomain);
        if ($sdl == 0) {
            $this->cityByDomain = '';
            return;
        } else {
            $subdomain = substr($domain,0,$sdl-1);
            $query = $this->db->getQuery(true)->select('name')->from('#__mycityselector_city')
                ->where('subdomain='.$this->db->quote(strtolower($subdomain)).' AND status=1');
            $this->db->setQuery($query);
            if ($result = $this->db->loadRow()) {
                $this->cityByDomain = $result[0];
                return;
            } else {
                $this->cityByDomain = '';
                return;
            }
        }
    }

    private function defineCityByCookie() {
        if ($this->comParams->get('debug_mode') == 1) {
            unset($_COOKIE['MCS_CITY_NAME']);
            unset($_COOKIE['MCS_NOASK']);
            setcookie('MCS_CITY_NAME','',time() + 3600 * 24 * 30, '/', $this->cookieDomain);
            setcookie('MCS_NOASK','',time() + 3600 * 24 * 30, '/', $this->cookieDomain);
        }
        $defaultCity = $this->comParams->get('default_city', 'Москва');
        if ($city = $_COOKIE['MCS_CITY_NAME']) {
            $query = $this->db->getQuery(true)->select('name')->from('#__mycityselector_city')
                ->where('name='.$this->db->quote($city).' AND status=1');
            $this->db->setQuery($query);
            if ($result = $this->db->loadRow()) {
                $this->city = $result[0];
            } else {
                $this->city = '';
            }

        } else {
            $this->city = '';
        }
    }


    /**
     * Метод для вызова системным триггером.
     * Парсинг контента и "обворачивание" текста городов спец. тегами
     */
    public function onAfterRender()
    {
        if (!JFactory::getApplication()->getName() == 'administrator') { // не делаем замену блоков в админке
            if (!$this->editMode) { // не делаем замену в режиме редактирования статьи
                $body = $this->getPageBody();
                $body = CitiesTagsHelper::parseCitiesTags($body, $this->city, $this->citiesList); // парсим контент
                $this->setPageBody($body);
            }
        }
        return true;
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

}
