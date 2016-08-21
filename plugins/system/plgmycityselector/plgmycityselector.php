<?php
/**
 * Plugin of MyCitySelector extension
 */

defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');

JLoader::import('joomla.plugin.plugin');
JLoader::import('plugins.system.plgmycityselector.helpers.McsContentHelper', JPATH_ROOT);
JLoader::import('plugins.system.plgmycityselector.helpers.McsData', JPATH_ROOT);

class plgSystemPlgMycityselector extends JPlugin
{

    /**
     * @var bool
     */
    private $editMode = false;

    /**
     * Initialization
     */
    function __construct(&$subject, $params)
    {
        parent::__construct($subject, $params);
        $jInput = JFactory::getApplication()->input;
        if (isset($_GET['mcs']) && $_GET['mcs'] == 'cls') {
            unset($_COOKIE['MCS_CITY_CODE']);
        }
        // load data and settings
        McsData::load();
        // check for "backend mode" and "frontend edit mode"
        $this->editMode = ($jInput->get('view') == 'form' && $jInput->get('layout') == 'edit');
        if (!$this->editMode && JFactory::getApplication()->getName() != 'administrator') {
            // check for redirect
            if (McsData::get('needRedirectTo')) {
                exit(header('Location: ' . McsData::get('needRedirectTo')));
            }
        }
    }

    /**
     * Метод для вызова системным триггером.
     * Парсинг контента и "обворачивание" текста городов спец. тегами
     */
    public function onAfterRender()
    {
        $isAdmin = (JFactory::getApplication()->getName() == 'administrator');
        if (!$this->editMode && !$isAdmin) { // не делаем замену блоков в админке и в режиме редактирования статьи
            $body = $this->getPageBody();
            $tags = McsContentHelper::parseCitiesTags($body);
            // анализируем
            foreach ($tags as $data) {
                if ($data['type'] == 'db') {
                    McsContentHelper::processingDbData($body, $data);
                }
            }
            $isMatchCity = false; // is there match of any tag with currant city?
            $forAnyCity = [];
            foreach ($tags as $data) {
                if ($data['type'] == 'local') {
                    if ($data['cities'][0] != '*') { // любой город
                        // тут все как было, проверяем город и подставляем текст если нужно
                        if (McsContentHelper::processingLocalData($body, $data)) {
                            $isMatchCity = true;
                        }
                    } else {
                        $forAnyCity = $data;
                    }
                }
            }
            if (!empty($forAnyCity)) {
                if (!$isMatchCity) {
                    $body = McsContentHelper::insertContentData($body, $forAnyCity);
                } else {
                    $body = str_replace($forAnyCity['position'], '', $body);
                }
            }
            $this->setPageBody($body);
        } else if ($isAdmin && @$_GET['option'] == 'com_installer' && @$_GET['view'] == 'manage') {
            // just for hide package elements from "Extensions/Manage" list.
            // Sometimes users uninstall not a package but one of extension of package.
            $body = McsContentHelper::removePackageElements($this->getPageBody());
            $this->setPageBody($body);
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
