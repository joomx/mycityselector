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
        $appRouter->attachParseRule([$this, 'parseRuleBefore'], Router::PROCESS_BEFORE); // в joomla 4 и 5 работает только с before параметром
        // $appRouter->attachParseRule([$this, 'parseRule'], Router::PROCESS_DURING);
        // $appRouter->attachParseRule([$this, 'parseRuleAfter'], Router::PROCESS_AFTER);
        $appRouter->attachBuildRule([$this, 'buildRule'], Router::PROCESS_DURING);
        $appRouter->attachBuildRule([$this, 'postprocessSEFBuildRule'], Router::PROCESS_AFTER);
    }


//    public function parseRule(&$router, &$uri)
//    {
//        file_put_contents(__DIR__ . '/.log.log', 'During: ' . $uri->getPath() . "\n", 8);
//    }


    /**
     * @param Joomla\CMS\Router\SiteRouter $router
     * @param Joomla\CMS\Uri\Uri $uri
     * @return void
     */
    public function parseRuleBefore(&$router, &$uri)
    {
        // file_put_contents(__DIR__ . '/.log.log', 'Before: ' . $uri->getPath() . "\n", 8);
        $path = $uri->getPath();
        $parts = explode('/', $path);
        $firstNode = $parts[0];
        if (McsData::findLocationByCode($firstNode)) {
            // если первая часть URL это код города, то вырезаем эту часть за запроса
            array_shift($parts);
            if (empty($parts)) {
                $path = '';
            } else {
                $path = implode('/', $parts);
            }
            $uri->setPath($path);
        }
    }

//    public function parseRuleAfter(&$router, &$uri)
//    {
//        file_put_contents(__DIR__ . '/.log.log', 'After: ' . $uri->getPath() . "\n", 8);
//    }


    /**
     * @param Joomla\CMS\Router\SiteRouter $router
     * @param Joomla\CMS\Uri\Uri $uri
     * @return void
     */
    public function buildRule(&$router, &$uri)
    {
        $cityCode = $uri->getVar('city', McsData::get('location'));
        $defaultCity = McsData::get('default_city');
        if (!empty($cityCode) && $cityCode != $defaultCity) {
            $segments = explode('/', trim($uri->getPath(), '/'));
            if ($segments[0] === 'index.php') {
                unset($segments[0]);
                $segments = array_merge([ 'index.php', $cityCode ], $segments);
            } else {
                $segments = array_merge([ $cityCode ], $segments);
            }
            $newUrl = implode('/', $segments);
            $uri->setPath($newUrl);
        }
    }


    public function postprocessSEFBuildRule(&$router, &$uri)
    {
        $uri->delVar('city');
    }

}