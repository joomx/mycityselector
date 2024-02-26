<?php

namespace joomx\mcs\module;

use joomx\mcs\plugin\helpers\McsData;
use JFactory;

defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');

class McsModHelper
{

    public static function groupLocationsByAlphabet($locations, $byKey = false)
    {
        $groupedCitiesByAlphabet = [];
        foreach ($locations as $key => $value) {
            $abcKey = mb_substr(($byKey ? $key : $value),0,1);
            $groupedCitiesByAlphabet[$abcKey][$key] = $value;
        }
        ksort($groupedCitiesByAlphabet);
        return $groupedCitiesByAlphabet;
    }


    public static function parseUrl($url)
    {
        $default = ['scheme' => 'http', 'host' => $_SERVER['HTTP_HOST'], 'port' => '', 'path' => $_SERVER['REQUEST_URI'], 'query' => ''];
        $url = parse_url($url);
        $url = array_merge($default, $url);
        if (!empty($url['query'])) {
            $q = str_replace('&amp;', '&', $url['query']);
            $q = explode('&', $q);
            $url['query'] = [];
            foreach ($q as $_qp) {
                $_qp = explode('=', $_qp);
                if (count($_qp) == 2) {
                    $url['query'][ $_qp[0] ] = $_qp[1];
                } else {
                    $url['query'][ $_qp[0] ] = '';
                }
            }
        } else {
            $url['query'] = [];
        }
        return $url;
    }


    public static function buildUrl($options)
    {
        $default = ['scheme' => 'http', 'host' => $_SERVER['HTTP_HOST'], 'port' => '', 'path' => $_SERVER['REQUEST_URI'], 'query' => []];
        $options = array_merge($default, $options);
        $query = '';
        if (!empty($options['query'])) {
            $query = [];
            foreach ($options['query'] as $param => $value) {
                $query[] = $param . '=' . $value;
            }
            $query = '?' . implode('=', $query);
        }
        if (!empty($url['port'])) {
            $url['port'] = ':' . $url['port'];
        }
        $url = $options['scheme'] . '://' . $options['host'] . $options['port'] . $options['path'] . $query;
        return $url;
    }


    public static function deleteLocationFromUrl($url)
    {
        static $_cityCurrent = ['subdomain' => null]; // типа кеш
        $default = ['scheme' => 'http', 'host' => $_SERVER['HTTP_HOST'], 'port' => '', 'path' => $_SERVER['REQUEST_URI'], 'query' => ''];
        $_url = parse_url($url);
        $_url = array_merge($default, $_url);
        $sections = explode('/', trim($_url['path'], '/'));
        $isIndexPhp = ($sections[0] === 'index.php');
        if ($isIndexPhp) {
            unset($sections[0]);
            $sections = array_values($sections);
        }
        if (count($sections) && $_cityCurrent['subdomain'] !== $sections[0]) {
            $_cityCurrent = McsData::findLocationByCode($sections[0]);
            if (empty($_cityCurrent)) {
                $_cityCurrent = ['subdomain' => $sections[0]];
            }
        }
        if (!empty($_cityCurrent['id'])) {
            unset($sections[0]);
            $sections = array_values($sections);
        }
        $_a = $isIndexPhp ? ['index.php'] : [];
        $sections = array_merge($_a, $sections);
        $_url['path'] = '/' . implode('/', $sections);
        if (!empty($_url['query'])) {
            $_url['query'] = '?' . $_url['query'];
        }
        if (!empty($_url['port'])) {
            $_url['port'] = ':' . $_url['port'];
        }
        $url = $_url['scheme'] . '://' . $_url['host'] . $_url['port'] . $_url['path'] . $_url['query'];
        return $url;
    }


    // мы используем эту штуку в шаблонах вместо класса URL, потому что возникают конфликты с другими компонентами (joomshoping например)
    public static function addLocationToUrl($url, $locationCode, $isDefaultCity=false)
    {
        static $_cityCurrent = ['subdomain' => null]; // типа кеш
        $default = ['scheme' => 'http', 'host' => $_SERVER['HTTP_HOST'], 'port' => '', 'path' => $_SERVER['REQUEST_URI'], 'query' => ''];
        $_url = parse_url($url);
        $_url = array_merge($default, $_url);
        $prefixUrl = [];
        $sections = explode('/', trim($_url['path'], '/'));
        $isIndexPhp = ($sections[0] === 'index.php');
        if ($isIndexPhp) {
            unset($sections[0]);
            $sections = array_values($sections);
        }

        if (count($sections) && $_cityCurrent['subdomain'] !== $sections[0]) {
            $_cityCurrent = McsData::findLocationByCode($sections[0]);

            if(!empty($_cityCurrent)) {
                switch ($_cityCurrent['type']) {
                    case 'city':
                        $_cityCurrent['subdomain'] = $_cityCurrent['city_code'];
                        break;
                    case 'province':
                        $_cityCurrent['subdomain'] = $_cityCurrent['province_code'];
                        break;
                    case 'country':
                        $_cityCurrent['subdomain'] = $_cityCurrent['country_code'];
                        break;
                }
            } else {
                $_cityCurrent = [
                    'subdomain' => $sections[0]
                ];
            }

        }
        if (!empty($_cityCurrent['type'])) {
            // если первый элемент является городом, значит удаляем его
            unset($sections[0]);
            $sections = array_values($sections);
        }



        //если это город по умолчанию, то не вставлем префикс
        if($isDefaultCity) {
            $prefixUrl = $isIndexPhp ? ['index.php'] : [];
        } else {
            $prefixUrl = $isIndexPhp ? ['index.php', $locationCode] : [$locationCode];
        }

        if(!empty($prefixUrl)) {
            $sections = array_merge($prefixUrl, $sections);
        }

        $_url['path'] = '/' . implode('/', $sections);
        if (!empty($_url['query'])) {
            $_url['query'] = '?' . $_url['query'];
        }
        if (!empty($_url['port'])) {
            $_url['port'] = ':' . $_url['port'];
        }
        $url = $_url['scheme'] . '://' . $_url['host'] . $_url['port'] . $_url['path'] . $_url['query'];
        return $url;
    }

}
