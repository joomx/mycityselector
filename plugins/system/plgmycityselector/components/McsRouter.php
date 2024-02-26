<?php
namespace joomx\mcs\plugin\components;

defined('_JEXEC') or exit(header('HTTP/1.0 404 Not Found') . '404 Not Found');

use \Joomla\CMS\Router\Router;
use \joomx\mcs\plugin\helpers\McsData;


/**
 * Обработчик адресов
 *
 * Class McsRouter
 * @package joomx\mcs\plugin\components
 */
class McsRouter
{

    public function __construct($appRouter)
    {
        $appRouter->attachParseRule([$this, 'parseRule'], Router::PROCESS_DURING);
        $appRouter->attachBuildRule([$this, 'buildRule'], Router::PROCESS_DURING);
        $appRouter->attachBuildRule([$this, 'postprocessSEFBuildRule'], Router::PROCESS_AFTER);
    }


    public function parseRule(&$router, &$uri)
    {
        $path = $uri->getPath();
        $parts = explode('/', $path);
        $firstNode = $parts[0];
        if (McsData::findLocationByCode($firstNode)) {
            // если первая часть URL это код города, то вырезаем эту часть за запроса
            array_shift($parts);
            $path = implode('/', $parts);
            $uri->setPath($path);
        }
    }


    public function buildRule(&$router, &$uri)
    {
        $cityCode = $uri->getVar('city', McsData::get('location'));
        $defaultCity = McsData::get('default_city');
        if (!empty($cityCode) && $cityCode != $defaultCity) {
            $uri->setPath($uri->getPath() . '/' . $cityCode . '/');
        }
    }


    public function postprocessSEFBuildRule(&$router, &$uri)
    {
        $uri->delVar('city');
    }

}