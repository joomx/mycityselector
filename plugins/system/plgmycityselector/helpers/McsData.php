<?php

namespace joomx\mcs\plugin\helpers;

defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Uri\Uri;
use morphos\Russian\GeographicalNamesInflection;
use joomx\mcs\module\McsModHelper;


/**
 * Class McsData
 *
 * like a STRUCT
 */
class McsData
{
    const MCS_FREE = true;
    const MCS_LIMIT_5 = 5;
    const MCS_LIMIT_15 = 15;

    const TYPE_CITY = 'city';
    const TYPE_PROVINCE = 'province';
    const TYPE_COUNTRY = 'country';


    /**
     * Component's settings
     * @var $compSettings Registry
     */
    private static $compSettings = null;

    /**
     * @var Registry Module's settings
     */
    private static $modSettings = null;

    /**
     * @var string ID of current location
     */
    private static $locationId = 0;

    /**
     * @var string ID of current city
     */
    private static $cityId = 0;

    /**
     * @var string ID of current province
     */
    private static $provinceId = 0;

    /**
     * @var string ID of current country
     */
    private static $countryId = 0;

    /**
     * @var string ID of current city
     */
    private static $locationType = '';

    /**
     * @var string Code of current city (subdomain)
     */
    private static $city = '';

    /**
     * @var string Code of current province (subdomain)
     */
    private static $province = '';

    /**
     * @var string Code of current country (subdomain)
     */
    private static $country = '';

    /**
     * @var string Code of current location (subdomain)
     */
    private static $location = '';

    /**
     * @var string Name of current city
     */
    private static $cityName = '';

    /**
     * @var string Name of current city
     */
    private static $provinceName = '';

    /**
     * @var string Name of current city
     */
    private static $countryName = '';

    /**
     * @var string Name of current location
     */
    private static $locationName = '';

    /**
     * @var bool Will set to TRUE if a city was already selected by user
     */
    private static $isLocationSelected = false;

    /**
     * @var bool Will set to FALSE for subdomains and subpaths
     */
    private static $isBaseDomain = true;

    /**
     * @var string Current domain
     */
    private static $cookieDomain = '';

    /**
     * @var int Module Id
     */
    private static $moduleId = 0;

    /**
     * @var string If SSL enabled will be https://
     */
    private static $http = 'http://';

    private static $langId = null;

    /**
     * @var array Названия псевдо свойств класса (склонения городов)
     */
    private static $nameCases = [
        'cityGenitive' => '',
        'cityDative' => '',
        'cityAccusative' => '',
        'cityAblative' => '',
        'cityPrepositional' => '',
        'provinceGenitive' => '',
        'provinceDative' => '',
        'provinceAccusative' => '',
        'provinceAblative' => '',
        'provincePrepositional' => '',
        'countryGenitive' => '',
        'countryDative' => '',
        'countryAccusative' => '',
        'countryAblative' => '',
        'countryPrepositional' => '',
    ];

    /**
     * Returns any MCS data/parameter by name
     *
     * @param string $name It may be a http, moduleId, cookieDomain, cityName, city, cities, provinces, countries, basedomain, default_city
     *                     or any of parameter names of component config of module.
     * @param        $default
     * @param        $options
     *
     * @return mixed
     */
    public static function get($name, $default = null, $options = null)
    {
        $value = null;
        if (property_exists('joomx\mcs\plugin\helpers\McsData', $name)) {
            $value = self::${$name};
        } else if (isset(self::$nameCases[$name])) {
            $value = self::$nameCases[$name];
        } else if (!empty(self::$compSettings) && self::$compSettings->get($name) !== null) {
            $value = self::$compSettings->get($name);
        } else if (!empty(self::$modSettings) && self::$modSettings->get($name) !== null) {
            $value = self::$modSettings->get($name);
        }
        // some filtering
        if ($name === 'basedomain') {
            if ($options === 'original') {
                $value = str_replace(['https://', 'http://'], '', $value);
            } else {
                $value = str_replace(['https://', 'http://', 'www.'], '', $value);
            }
        }
        return ($value === null) ? $default : $value;
    }


    /**
     * For tests
     */
    public static function set($name, $value)
    {
        if (property_exists('McsData', $name)) {
            self::${$name} = $value;
        } else if (!empty(self::$compSettings) && self::$compSettings->get($name) !== null) {
            self::$compSettings->set($name, $value);
        } else if (!empty(self::$modSettings) && self::$modSettings->get($name) !== null) {
            self::$modSettings->set($name, $value);
        }
    }


    /**
     * Loads all options of extension
     */
    public static function load()
    {
        // load component settings
        self::$compSettings = ComponentHelper::getParams('com_mycityselector');

        $db = Factory::getDbo();
        // load module settings
        $query = $db->getQuery(true);
        $query->select('id ,params')->from('#__modules')->where("module = 'mod_mycityselector' LIMIT 1");
        $query  = Factory::getDbo()->setQuery($query);
        $result = $query->loadAssoc();

        self::$modSettings = new Registry($result['params']);

        // определяем ID модуля
        self::$moduleId = $result['id'];

        // проверяем, не указано ли несколько доменов через запятую
        $baseDomain = self::$compSettings->get('basedomain');
        $multy = explode(',', $baseDomain);
        if (count($multy) > 1) {
            // нууу, тогда нужно свериться с HTTP_HOST
            foreach ($multy as $host) {
                $_host = str_replace('www.', '', $host);
                if (stripos($_SERVER['HTTP_HOST'], $_host) !== false) {
                    // похоже на наш домен
                    $baseDomain = $host;
                    break;
                }
            }
            if (strpos($baseDomain, ',') !== false) {
                // если текущий хост не совпал ни с одним доменом из списка, то берем первый из них
                $baseDomain = $multy[0];
            }
            self::$compSettings->set('basedomain', $baseDomain);
        }

        $query = $db->getQuery(true);
        $query->select('domain')->from('#__mycityselector_countries')->where("domain != ''");
        $query  = Factory::getDbo()->setQuery($query);
        $result = $query->loadAssoc();
        self::$compSettings->set('countries_domains', $result);

        self::$cookieDomain = self::getBaseNameOfCurrentDomain();

        // http || https ?
        if (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            $_SERVER['SERVER_PORT'] == 443 ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
        ) {
            self::$http = 'https://';
        } else {
            self::$http = 'http://';
        }

        self::$isLocationSelected = empty($_COOKIE['MCS_LOCATION_TYPE']) ? false : true; // если в куках есть ключ, значит город был ранее выбран
    }


    /**
     * @param $host
     *
     * @return mixed|string
     */
    private static function defineBaseDomain($host)
    {
        $baseDomain       = '';
        $countriesDomains = self::$compSettings->get('countries_domains');

        if (is_array($countriesDomains) && in_array($host, $countriesDomains)) {
            return $host;
        }

        $parts = explode('.', $host);

        if ($parts[0] == 'www') {
            unset($parts[0]);
            $parts = array_values($parts);
        }

        if (count($parts) > 2) {
            // возможно, это поддомен. попытаемся его найти в базе
            $location = self::findLocationByCode($parts[0]);
            if (!empty($location)) {
                unset($parts[0]); // да это поддомен, удаляем его и остается только имя домена
            }
            $baseDomain = implode('.', $parts);
        }

        return $baseDomain;
    }


    private static function setNameCases()
    {
        // страна
        $country = self::get('countryName');
        if (!empty($country)) {
            $countryNames = GeographicalNamesInflection::getCases($country);
            // Родительный
            self::$nameCases['countryGenitive'] = $countryNames['genitive'];
            // Дательный
            self::$nameCases['countryDative'] = $countryNames['dative'];
            // Винительный
            self::$nameCases['countryAccusative'] = $countryNames['accusative'];
            // Творительный
            self::$nameCases['countryAblative'] = $countryNames['ablative'];
            // Предложный
            self::$nameCases['countryPrepositional'] = $countryNames['prepositional'];
        } else {
            self::$nameCases['countryGenitive'] = self::$nameCases['countryDative'] = self::$nameCases['countryAccusative'] =
            self::$nameCases['countryAblative'] = self::$nameCases['countryPrepositional'] = '';
        }

        // регион
        $region = self::get('provinceName');
        if (!empty($region)) {
            $regionNames = GeographicalNamesInflection::getCases($region);
            // Родительный
            self::$nameCases['provinceGenitive'] = $regionNames['genitive'];
            // Дательный
            self::$nameCases['provinceDative'] = $regionNames['dative'];
            // Винительный
            self::$nameCases['provinceAccusative'] = $regionNames['accusative'];
            // Творительный
            self::$nameCases['provinceAblative'] = $regionNames['ablative'];
            // Предложный
            self::$nameCases['provincePrepositional'] = $regionNames['prepositional'];
        } else {
            self::$nameCases['provinceGenitive'] = self::$nameCases['provinceDative'] = self::$nameCases['provinceAccusative'] =
                self::$nameCases['provinceAblative'] = self::$nameCases['provincePrepositional'] = '';
        }

        // город
        $cityName = self::get('cityName');
        if (!empty($cityName)) {
            $city = self::get('city');
            $langId = self::getLangId();
            // читаем склонения из базы (введенные вручную)
            $db = Factory::getDbo();
            $query = $db->getQuery(true);
            $query->select('b.case_id as `case`, b.value as `value`')
                ->from('#__mycityselector_cities a')
                ->leftJoin('#__mycityselector_city_cases b ON a.id = b.city_id')
                ->where('a.subdomain = ' . $db->q($city) . ' AND b.lang_id = ' . $db->q($langId));
            $cityDbNames = $db->setQuery($query)->loadAssocList('case');
            // автоматом
            $cityNames = GeographicalNamesInflection::getCases($cityName);

            // Родительный
            self::$nameCases['cityGenitive'] = isset($cityDbNames[2]) ? $cityDbNames[2]['value'] : $cityNames['genitive'];
            // Дательный
            self::$nameCases['cityDative'] = isset($cityDbNames[3]) ? $cityDbNames[3]['value'] : $cityNames['dative'];
            // Винительный
            self::$nameCases['cityAccusative'] = isset($cityDbNames[4]) ? $cityDbNames[4]['value'] : $cityNames['accusative'];
            // Творительный
            self::$nameCases['cityAblative'] = isset($cityDbNames[5]) ? $cityDbNames[5]['value'] : $cityNames['ablative'];
            // Предложный
            self::$nameCases['cityPrepositional'] = isset($cityDbNames[6]) ? $cityDbNames[6]['value'] : $cityNames['prepositional'];
        } else {
            self::$nameCases['cityGenitive'] = self::$nameCases['cityDative'] = self::$nameCases['cityAccusative'] =
            self::$nameCases['cityAblative'] = self::$nameCases['cityPrepositional'] = '';
        }

        McsLog::add('Склонения установлены: ' . print_r(self::$nameCases, true), McsLog::WARN);
    }


    /**
     * @param $code
     *
     * @return mixed|null
     */
    public static function findCityByCode($code)
    {
        $langId = self::getLangId();
        static $cache = [];
        if (!empty($code)) {
            $code = strtolower($code);

            if (isset($cache[$code]))
                return $cache[$code];

            $db    = Factory::getDbo();
            $query = $db->getQuery(true);
            $query->select('city.id as city_id,
			                        city.subdomain as city_code,
			                        city_name.name as city_name,
			                        province.id as province_id,
			                        province.subdomain as province_code,
			                        province_name.name as province_name,
                                    country.id as country_id,
			                        country.domain as country_domain,
			                        country.subdomain as country_code,
			                        country_name.name as country_name')
                ->from('#__mycityselector_cities as city')
                ->innerJoin("`#__mycityselector_city_names` AS `city_name` ON `city_name`.`city_id` = `city`.`id`")
                ->leftJoin("`#__mycityselector_provinces` AS `province` ON `province`.`id` = `city`.`province_id`")
                ->innerJoin("`#__mycityselector_province_names` AS `province_name` ON `province_name`.`province_id` = `province`.`id`")
                ->leftJoin("`#__mycityselector_countries` AS `country` ON `country`.`id` = `city`.`country_id`")
                ->innerJoin("`#__mycityselector_country_names` AS `country_name` ON `country_name`.`country_id` = `country`.`id`")
                ->where("`city_name`.`lang_id` = {$langId} AND `province_name`.`lang_id` = {$langId} AND `country_name`.`lang_id` = {$langId}
                                  AND `city`.`subdomain` = {$db->quote($code)}
                                  AND `city`.`published` > 0");

            if (McsData::MCS_FREE == true) {
                $db->setQuery($query, 0, McsData::MCS_LIMIT_5);
            } else {
                $db->setQuery($query);
            }

            $cache[$code] = $db->loadAssoc();

            if (!empty($cache[$code])) {
                $cache[$code]['type'] = 'city';
            }

            return $cache[$code];
        }
        return false;
    }

    /**
     * @param $code
     *
     * @return mixed|null
     */
    public static function findProvinceByCode($code)
    {
        $langId = self::getLangId();
        static $cache = [];
        if (!empty($code)) {
            $code = strtolower($code);
            if (isset($cache[$code])) {
                return $cache[$code];
            }
            $db = Factory::getDbo();
            $query = $db->getQuery(true);
            $query->select('province.id as province_id,
                    province.subdomain as province_code,
                    province_name.name as province_name,
                    country.id as country_id,
                    country.domain as country_domain,
                    country.subdomain as country_code,
                    country_name.name as country_name')
                ->from('#__mycityselector_provinces as province')
                ->innerJoin("`#__mycityselector_province_names` AS `province_name` ON `province_name`.`province_id` = `province`.`id`")
                ->leftJoin("`#__mycityselector_countries` AS `country` ON `country`.`id` = `province`.`country_id`")
                ->innerJoin("`#__mycityselector_country_names` AS `country_name` ON `country_name`.`country_id` = `country`.`id`")
                ->where("`province_name`.`lang_id` = {$langId} AND `country_name`.`lang_id` = {$langId}
                  AND `province`.`subdomain` = {$db->quote($code)}
                  AND `province`.`published` > 0");
            if (McsData::MCS_FREE === true) {
                $db->setQuery($query, 0, McsData::MCS_LIMIT_5);
            } else {
                $db->setQuery($query);
            }
            $cache[$code] = $db->loadAssoc();
            if (!empty($cache[$code])) {
                $cache[$code]['type'] = 'province';
            }
            return $cache[$code];
        }
        return false;
    }

    /**
     * @param $code
     *
     * @return mixed|null
     */
    public static function findCountryByCode($code)
    {
        $langId = self::getLangId();
        static $cache = [];
        if (!empty($code)) {
            $code = strtolower($code);

            if (isset($cache[$code]))
                return $cache[$code];

            $db    = Factory::getDbo();
            $query = $db->getQuery(true);

            $query->select('country.id as country_id,
                                    country.domain as country_domain,
			                        country.subdomain as country_code,
			                        country_name.name as country_name')
                ->from('#__mycityselector_countries as country')
                ->innerJoin("`#__mycityselector_country_names` AS `country_name` ON `country_name`.`country_id` = `country`.`id`")
                ->where("`country_name`.`lang_id` = {$langId}
                                  AND `country`.`subdomain` = {$db->quote($code)}
                                  AND `country`.`published` > 0");

            if (McsData::MCS_FREE == true) {
                $db->setQuery($query, 0, McsData::MCS_LIMIT_5);
            } else {
                $db->setQuery($query);
            }
            $cache[$code] = $db->loadAssoc();
            if (!empty($cache[$code])) {
                $cache[$code]['type'] = 'country';
            }

            return $cache[$code];
        }
        return false;
    }


    public static function findLocationByCode($code)
    {
        $city = self::findCityByCode($code);
        if (!empty($city)) {
            return $city;
        }
        $province = self::findProvinceByCode($code);
        if (!empty($province)) {
            return $province;
        }
        $country = self::findCountryByCode($code);
        if (!empty($country)) {
            return $country;
        }
        return false;
    }

    /**
     * @param $code
     *
     * @return mixed|null
     */
    public static function findLocationByDomain($domain)
    {
        $langId = self::getLangId();
        static $cache = [];
        if (!empty($domain)) {
            $domain = strtolower($domain);

            if (isset($cache[$domain]))
                return $cache[$domain];

            $db    = Factory::getDbo();
            $query = $db->getQuery(true);

            $query->select('country.id as country_id,
			                        country.subdomain as country_code,
			                        country_name.name as country_name,
                                    city.id as city_id,
			                        city.subdomain as city_code,
			                        city_name.name as city_name,
                                    province.id as province_id,
			                        province.subdomain as province_code,
			                        province_name.name as province_name')
                ->from('#__mycityselector_countries as country')
                ->innerJoin("`#__mycityselector_country_names` AS `country_name` ON `country_name`.`country_id` = `country`.`id`")
                ->leftJoin('#__mycityselector_cities as city on country.default_city_id = city.id')
                ->innerJoin("#__mycityselector_city_names AS city_name ON city_name.city_id = city.id")
                ->leftJoin('#__mycityselector_provinces as province on province.id = city.province_id')
                ->innerJoin("#__mycityselector_province_names AS province_name ON province_name.province_id = province.id")
                ->where("`country_name`.`lang_id` = {$langId}
                                AND `city_name`.`lang_id` = {$langId}
                                AND `province_name`.`lang_id` = {$langId}
                                AND `country`.`domain` = {$db->quote($domain)}
                                AND `country`.`published` > 0
                                AND `city`.`published` > 0
                                AND `province`.`published` > 0");

            $db->setQuery($query);
            $result = $db->loadAssoc();

            // если город не найден, тогда ищем страну
            if (empty($result)) {
                $query = $db->getQuery(true);
                $query->select('country.id as country_id,
			                        country.subdomain as country_code,
			                        country_name.name as country_name')
                    ->from('#__mycityselector_countries as country')
                    ->innerJoin("`#__mycityselector_country_names` AS `country_name` ON `country_name`.`country_id` = `country`.`id`")
                    ->where("`country_name`.`lang_id` = {$langId}
                                AND `country`.`domain` = {$db->quote($domain)}
                                AND `country`.`published` > 0");

                $db->setQuery($query);
                $result = $db->loadAssoc();
            }

            $cache[$domain] = $result;

            if (!empty($cache[$domain])) {
                if (isset($result['city_code']) && !empty($result['city_code'])) {
                    $cache[$domain]['type'] = 'city';
                } else {
                    $cache[$domain]['type'] = 'country';
                }
            }

            return $cache[$domain];
        }
        return false;
    }

    /**
     * Search Item (city or province of cities)
     *
     * @param $name
     *
     * @return mixed|null
     */
    public static function getTypeByName($name)
    {
        $type = null;
        if (!empty($name)) {
            $db    = Factory::getDbo();
            $name  = $db->quote('%' . $name . '%');
            $query = $db->getQuery(true);
            $query->select('city_id as id')->from('#__mycityselector_city_names')->where("`name` LIKE {$name}");
            if (McsData::MCS_FREE == true) {
                $db->setQuery($query, 0, McsData::MCS_LIMIT_5);
            } else {
                $db->setQuery($query);
            }
            $res = $db->loadAssocList();
            if (!empty($res)) {
                $type = 'city';
            } else {
                $query = $db->getQuery(true);
                $query->select('province_id as id')->from('#__mycityselector_province_names')->where("`name` LIKE {$name}");
                $db->setQuery($query);
                $res = $db->loadAssocList();
                if (!empty($res)) {
                    $type = 'province';
                } else {
                    $query = $db->getQuery(true);
                    $query->select('country_id as id')->from('#__mycityselector_country_names')->where("`name` LIKE {$name}");
                    $db->setQuery($query);
                    $res = $db->loadAssocList();
                    if (!empty($res)) $type = 'country';
                }
            }
        }

        return $type;
    }


    /**
     * Эта функция не используется в коде, но может быть использована сторонним разработчиком как McsData::getCitiesOfProvince('moscow')
     *
     * @param $code
     * @return mixed
     */
    public static function getCitiesOfProvince($code)
    {
        $langId = self::getLangId();
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        // смотрим чем является код, может это код города, тогда нужно взять его код региона
        $query->select('*')
            ->from('#__mycityselector_cities as city')
            ->where("`city`.`subdomain` = {$db->quote($code)} AND `city`.`published` > 0");
        $db->setQuery($query);
        $isCity = $db->loadAssoc();

        $query = $db->getQuery(true);
        if (!empty($isCity)) {
            $provinceId = $isCity['province_id'];
            $query->select('city.id as city_id,
			                     city.subdomain as city_code,
			                     city_name.name as city_name,
                                 province.id as province_id,
			                     province.subdomain as province_code,
			                     province_name.name as province_name')
                ->from('#__mycityselector_cities as city')
                ->innerJoin("#__mycityselector_city_names AS city_name ON city_name.city_id = city.id")
                ->leftJoin('#__mycityselector_provinces as province on province.id = city.province_id')
                ->innerJoin("#__mycityselector_province_names AS province_name ON province_name.province_id = province.id")
                ->where("`city_name`.`lang_id` = {$langId}
                    AND `province_name`.`lang_id` = {$langId}
                    AND `province`.`id` = {$provinceId}
                    AND `city`.`published` > 0");
        } else {
            $query->select('city.id as city_id,
			                     city.subdomain as city_code,
			                     city_name.name as city_name,
                                 province.id as province_id,
			                     province.subdomain as province_code,
			                     province_name.name as province_name')
                ->from('#__mycityselector_cities as city')
                ->innerJoin("#__mycityselector_city_names AS city_name ON city_name.city_id = city.id")
                ->leftJoin('#__mycityselector_provinces as province on province.id = city.province_id')
                ->innerJoin("#__mycityselector_province_names AS province_name ON province_name.province_id = province.id")
                ->where("`city_name`.`lang_id` = {$langId}
                    AND `province_name`.`lang_id` = {$langId}
                    AND `province`.`subdomain` LIKE {$db->quote($code)}
                    AND `city`.`published` > 0");
        }
        $db->setQuery($query);
        $cities = $db->loadAssocList();

        if (!empty($cities)) {
            foreach ($cities as &$city) {
                $city['url'] = McsData::buildUrlByLocation($city, 'city');
            }
        }

        return $cities;
    }



    /**
     * Loads content
     *
     * @param int $id
     * @param int $locationId optional
     *
     * @return mixed Returns FALSE if content doesn't exists or it's unpublished,
     *                  otherwise it returns a text (content)
     */
    public static function loadContent($id, $locationId = null)
    {
        $db = Factory::getDbo();
        $id = $db->quote($id);

        if ($locationId === null) {
            $locationId = self::get('locationId');
        }

        $locationType = self::get('locationType', 'city');
        if (empty($locationType)) $locationType = 'city';

        switch ($locationType) {
            case "country":
                $fieldValueIdField = 'country_id';
                $fieldValueTable   = '#__mycityselector_value_country';
                break;
            case "province":
                $fieldValueIdField = 'province_id';
                $fieldValueTable   = '#__mycityselector_value_province';
                break;
            case "city":
                $fieldValueIdField = 'city_id';
                $fieldValueTable   = '#__mycityselector_value_city';
                break;
        }

        $locationId = $db->quote($locationId);
        // сначала ищем текст который соответствует текущему городу
        $query = "SELECT `fv`.`value` FROM `#__mycityselector_field` `fld` "
            . "INNER JOIN `#__mycityselector_field_value` `fv` ON `fv`.`field_id` = `fld`.`id` "
            . "INNER JOIN `{$fieldValueTable}` `vc` ON `vc`.`field_value_id` = `fv`.`id` "
            . "WHERE `fld`.`id` = {$id} AND `fld`.`published` = 1 AND `vc`.`{$fieldValueIdField}` = {$locationId} AND `fv`.`is_ignore` = 0";
        $db->setQuery($query);
        $result = $db->loadAssocList();
        if (!empty($result[0]['value'])) {
            $test = trim(strip_tags($result[0]['value'], '<iframe>'));
            return empty($test) ? '' : $result[0]['value'];
        } else {
            // теперь смотрим, нет ли отрицаний
            $query = "SELECT `fv`.`value` FROM `#__mycityselector_field` `fld` "
                . "INNER JOIN `#__mycityselector_field_value` `fv` ON `fv`.`field_id` = `fld`.`id` "
                . "INNER JOIN `{$fieldValueTable}` `vc` ON `vc`.`field_value_id` = `fv`.`id` "
                . "WHERE `fld`.`id` = {$id} AND `fv`.`is_ignore` = 1 "
                . "AND {$locationId} NOT IN (
                    SELECT `vc2`.`{$fieldValueIdField}` FROM `#__mycityselector_field` `fld2`
                    INNER JOIN `#__mycityselector_field_value` `fv2` ON `fv2`.`field_id` = `fld2`.`id`
                    INNER JOIN `{$fieldValueTable}` `vc2` ON `vc2`.`field_value_id` = `fv2`.`id`
                    WHERE `fld2`.`id` = {$id} AND `fld`.`published` = 1 AND `fv2`.`is_ignore` = 1
                )";
            $db->setQuery($query);
            $result = $db->loadAssocList();
            if (!empty($result[0]['value'])) {
                $test = trim(strip_tags($result[0]['value'], '<iframe>'));
                return empty($test) ? '' : $result[0]['value'];
            }
            // иначе, ищем текст по умолчанию
            $query = "SELECT `fv`.`value` FROM `#__mycityselector_field` `fld` "
                . "INNER JOIN `#__mycityselector_field_value` `fv` ON `fv`.`field_id` = `fld`.`id` "
                . "WHERE `fv`.`field_id` = {$id} AND `fld`.`published` = 1 AND `fv`.`default` = 1";
            $db->setQuery($query);
            $result = $db->loadAssocList();
            if (!empty($result[0]['value'])) {
                $test = trim(strip_tags($result[0]['value'], '<iframe>'));
                return empty($test) ? '' : $result[0]['value'];
            }
        }

        return false;
    }


    // местоположение на поддоменах
    public static function detectLocationFromDomain()
    {
        $baseDomain = '';

        // берем базовый домен сайта из настроек
        if (!empty(self::$compSettings) && self::$compSettings->get('basedomain')) {
            $baseDomain = self::$compSettings->get('basedomain');
            $baseDomain = str_replace(['https://', 'http://'], '', $baseDomain);
        }

        //если базовый домен не найден или он не совпадает с текущим, то проверям домены других стран
        if (empty($baseDomain) || (strripos($_SERVER['HTTP_HOST'], $baseDomain) === false)) {
            // иначе, пытаемся определить базовый домен самостоятельно
            McsLog::add('Базовый домен не определен, автоопределение ...', McsLog::WARN);
            $baseDomain = self::defineBaseDomain($_SERVER['HTTP_HOST']);
        }

        McsLog::add('Базовый домен: ' . $baseDomain);
        // проверяем текущий поддомен
        $subDomain = trim(str_replace($baseDomain, '', $_SERVER['HTTP_HOST']), '.');
        $subDomain = strtolower($subDomain);

        if (empty($subDomain)) {
            McsLog::add("Текущий домен: '{$baseDomain}'");
            $location = self::findLocationByDomain($baseDomain);
        } else {
            McsLog::add("Текущий поддомен: '{$subDomain}'");
            $location = self::findLocationByCode($subDomain);
        }

        if (!empty($location)) {
            self::assignLocation($location);
            return true;
        }

        return false;
    }


    /**
     * Определяем локацию по slug в адресе
     * @return bool
     */
    public static function detectLocationFromUrl()
    {
        McsLog::add('Определяем текущее местоположение');
        McsLog::add('Ищем местоположение в строке URL');
        $uri   = Uri::getInstance();
        $path  = strtolower($uri->getPath());
        $parts = explode('/', $path);

        if (isset($parts[1]) && ($parts[1] == 'index.php')) {
            unset($parts[1]);
            $parts = array_values($parts);
        }

        if (isset($parts[1])) {
            $locationCode = $parts[1];
            $location = self::findLocationByCode($locationCode);
            if (!empty($location)) {
                self::assignLocation($location);
                return true;
            }
            McsLog::add('Местоположение ' . $locationCode . ' не найдено', McsLog::WARN);
        }
        return self::detectDefaultLocation();
    }


    /**
     * Определяет локацию по utm меткам в запросе
     *
     * @param false $saveToCookie
     * @return bool
     */
    public static function detectLocationFromQueryParam($saveToCookie = false)
    {
        $location = null;
        McsLog::add('Определяем текущее местоположение (utm)');
        if (!empty($_GET['utm_location']) && !empty($_GET['utm_loctype'])) {
            switch ($_GET['utm_loctype']) {
                case 'country':
                    $location = self::findCountryByCode($_GET['utm_location']);
                    break;
                case 'province':
                    $location = self::findProvinceByCode($_GET['utm_location']);
                    break;
                case 'city':
                    $location = self::findCityByCode($_GET['utm_location']);
                    break;
            }
        } else if (!empty($_GET['utm_location'])) {
            $location = self::findCityByCode($_GET['utm_location']);
        }
        if (!empty($location)) {
            McsLog::add('Местополложение определено');
            setcookie('MCS_NOASK', '1', time()+3600, '/');
            if ($saveToCookie) {
                McsLog::add('Сохраняем в cookies ' . $location['city_name']);
                setcookie('MCS_LOCATION_TYPE', $location['type'], time()+3600, '/');
                setcookie('MCS_COUNTRY_CODE', $location['country_code'], time()+3600, '/');
                setcookie('MCS_COUNTRY_NAME', $location['country_name'], time()+3600, '/');
                setcookie('MCS_PROVINCE_CODE', $location['province_code'], time()+3600, '/');
                setcookie('MCS_PROVINCE_NAME', $location['province_name'], time()+3600, '/');
                setcookie('MCS_CITY_CODE', $location['city_code'], time()+3600, '/');
                setcookie('MCS_CITY_NAME', $location['city_name'], time()+3600, '/');
            }
            self::assignLocation($location, true);
            return true;
        }
        return false;
    }


    public static function detectLocationFromCookies()
    {
        // Город уже был выбран пользователем
        McsLog::add('Определяем текущее местоположение (cookies)');
        if (!empty($_COOKIE['MCS_LOCATION_TYPE'])) {
            McsLog::add('Cookie не пустые');
            $location = null;
            switch ($_COOKIE['MCS_LOCATION_TYPE']) {
                case 'country':
                    $location = self::findCountryByCode($_COOKIE['MCS_COUNTRY_CODE']);
                    break;
                case 'province':
                    $location = self::findProvinceByCode($_COOKIE['MCS_PROVINCE_CODE']);
                    break;
                case 'city':
                    $location = self::findCityByCode($_COOKIE['MCS_CITY_CODE']);
                    break;
            }
            if (!empty($location)) {
                self::assignLocation($location, true);
                return true;
            } else {
                McsLog::add('Местоположение не найдено, загружаем местоположение по умолчанию');
                return self::detectDefaultLocation();
            }
        } else {
            McsLog::add('Cookie пустые, берем город по умолчанию');
            return self::detectDefaultLocation();
        }
    }


    public static function detectDefaultLocation()
    {
        McsLog::add('Определяем местоположение по умолчанию');
        if (!empty(self::$compSettings) && self::get('default_city')) {
            $location = self::findLocationByCode(self::get('default_city'));
            if (!empty($location)) {
                self::assignLocation($location, true);
                return true;
            } else {
                McsLog::add('Базовое местоположение не определено! Возможно, местоположение по умолчанию не опубликовано', McsLog::WARN);
                return false;
            }
        }
        McsLog::add('default_city не определен');
        return false;
    }


    public static function detectLocationFromIp()
    {
        McsLog::add('Автоопределение города Ip-Api');
        include(__DIR__ . '/geo.php');
        $geoIp = new \GeoIP();
        $location = $geoIp->detectLocation(true);
        if (isset($location['error'])) {
            McsLog::add('Error: City was not detected.');
            return false;
        } else {
            McsLog::add('Город определен как ' . $location['city']);
            return $location;
        }
    }

    /**
     *
     * @return bool
     * @since version
     */
    public static function isBaseUrl()
    {
        $uri = Uri::getInstance();
        $url = strtolower($uri->toString());

        switch (McsData::get('seo_mode', 0)) {
            // SEO выключено
            case 0:
                $baseDomain = McsData::get('basedomain', null, 'original');
                $host       = strtolower($uri->getHost());
                $port       = $uri->getPort();
                if ($port &&  ($port != 80 || $port != 443)) {
                    $host .= ':' . $uri->getPort();
                }
                $rootUrl   = strtolower(Uri::root());
                $rootLen   = strlen($rootUrl);
                $urlSubStr = substr($url, 0, $rootLen);
                if ($urlSubStr == $rootUrl && ($host == $baseDomain || $host == str_replace('www.', '', $baseDomain))) {
                    return true;
                }
                break;

            // Режим поддоменов
            case 1:
                $baseDomain       = McsData::get('basedomain', null, 'original');
                $baseDomainUrl    = McsData::get('http') . $baseDomain . Uri::root(true);
                $baseDomainUrlLen = strlen($baseDomainUrl);
                $urlSubStr        = substr($url, 0, $baseDomainUrlLen);
                if ($urlSubStr == $baseDomainUrl || $urlSubStr == str_replace('www.', '', $baseDomainUrl)) {
                    return true;
                }
                break;

            // Режим подкаталогов
            case 2:
                $path     = $uri->getPath();
                $pathArr  = explode('/', $path);
                $city     = strtolower($pathArr[1]);
                $cityData = self::findLocationByCode($city);
                if (empty($cityData)) {
                    return true;
                }

                break;
        }

        return false;
    }

    static function getLangId()
    {
        if (self::$langId)
            return self::$langId;

        $langId = self::searchLangIdByCurrentLang();

        if ($langId === false)
            $langId = self::searchDefaultLangId();

        self::$langId = $langId;

        return $langId;
    }

    private static function searchLangIdByCurrentLang()
    {
        $localeOfCurrentLang = Factory::getLanguage()->getTag();

        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id')
            ->from('#__mycityselector_langs')
            ->where("`locale` = '{$localeOfCurrentLang}'");

        $db->setQuery($query);

        $result = $db->loadAssoc();

        if (isset($result['id'])) {
            return $result['id'];
        } else {
            return false;
        }
    }

    private static function searchDefaultLangId()
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id')
            ->from('#__mycityselector_langs')
            ->where("`default` = 1");

        $db->setQuery($query);

        $result = $db->loadAssoc();

        if (isset($result['id'])) {
            return $result['id'];
        } else {
            return false;
        }
    }

    public static function assignLocation($location, $isBaseDomain = false)
    {
        switch ($location['type']) {
            case 'country':
                McsLog::add('Местоположение найдено: ' . $location['country_name']);
                self::$isBaseDomain = $isBaseDomain;
                self::$countryId    = $location['country_id'];
                self::$country      = $location['country_code'];
                self::$countryName  = $location['country_name'];
                self::$locationType = $location['type'];
                self::$locationId   = $location['country_id'];
                self::$location     = $location['country_code'];
                self::$locationName = $location['country_name'];
                break;
            case 'province':
                McsLog::add('Местоположение найдено: ' . $location['province_name']);
                self::$isBaseDomain = $isBaseDomain;
                self::$provinceId   = $location['province_id'];
                self::$province     = $location['province_code'];
                self::$provinceName = $location['province_name'];
                self::$countryId    = $location['country_id'];
                self::$country      = $location['country_code'];
                self::$countryName  = $location['country_name'];
                self::$locationType = $location['type'];
                self::$locationId   = $location['province_id'];
                self::$location     = $location['province_code'];
                self::$locationName = $location['province_name'];
                break;
            case 'city':
                McsLog::add('Местоположение найдено: ' . $location['city_name']);
                self::$isBaseDomain = $isBaseDomain;
                self::$cityId       = $location['city_id'];
                self::$city         = $location['city_code'];
                self::$cityName     = $location['city_name'];
                self::$provinceId   = $location['province_id'];
                self::$province     = $location['province_code'];
                self::$provinceName = $location['province_name'];
                self::$countryId    = $location['country_id'];
                self::$country      = $location['country_code'];
                self::$countryName  = $location['country_name'];
                self::$locationType = $location['type'];
                self::$locationId   = $location['city_id'];
                self::$location     = $location['city_code'];
                self::$locationName = $location['city_name'];
                break;
        }
        self::setNameCases();
    }


    public static function getLocationFromCookie()
    {
        if (isset($_COOKIE['MCS_LOCATION_TYPE'])) {
            switch ($_COOKIE['MCS_LOCATION_TYPE']) {
                case 'country':
                    return $_COOKIE['MCS_COUNTRY_CODE'];
                case 'province':
                    return $_COOKIE['MCS_PROVINCE_CODE'];
                case 'city':
                    return $_COOKIE['MCS_CITY_CODE'];
            }
        }

        return false;
    }

    public static function getCurrentLocation()
    {
        return [
            'country'  => [
                'id'   => self::$countryId,
                'code' => self::$country,
                'name' => self::$countryName
            ],
            'province' => [
                'id'   => self::$provinceId,
                'code' => self::$province,
                'name' => self::$provinceName
            ],
            'city'     => [
                'id'   => self::$cityId,
                'code' => self::$city,
                'name' => self::$cityName
            ]
        ];
    }

    //TODO refactor
    public static function buildUrlByLocation(array $location, $typeLocation)
    {
        $currentUrl = Uri::getInstance()->toString();
        $seoMode = self::get('seo_mode');
        $redirectMode = self::get('redirect_mode'); // при редиректе на поддомен, кидать лио на такой-же url, либо в корень поддомена
        $baseDomain = self::get('basedomain');
        $baseDomainOriginal = self::get('basedomain', null, 'original');
        $isBaseDomainWasReplaced = false; // базовый домен подменен на домен привязанный к городу (то есть переход на другой домен)
        $domainOfLocation = $location['country_domain'] ? $location['country_domain'] : '';
        $defaultLocationCode = self::get('default_city');
        $defaultLocationCodeOfDomain = (isset($location['default_city']) && !empty($location['default_city'])) ? $location['default_city'] : '';
        $isDefaultDomain = empty($domainOfLocation) || $domainOfLocation == $baseDomain || $domainOfLocation == $baseDomainOriginal;
        $locationCode = null;

        switch ($typeLocation) {
            case 'country':
                $locationCode = $location['country_code'];
                break;
            case 'province':
                $locationCode = $location['province_code'];
                break;
            case 'city':
                $locationCode = $location['city_code'];
                break;
        }

        //определяем домен, если он задан стране.
        if (!$isDefaultDomain && ($defaultLocationCode !== $locationCode)) {
            $isBaseDomainWasReplaced = true;
            $baseDomain = $domainOfLocation; // подменяем базовый домен на домен завязанный на городе
            if (!empty($defaultLocationCodeOfDomain)) {
                $defaultLocationCode = $defaultLocationCodeOfDomain;
            } else {
                $defaultLocationCode = $location['country_code'];
            }
        }

        $isDefaultCity = ($defaultLocationCode === $locationCode);

        switch ($seoMode) {
            case 1:
                // Режим поддоменов и доменов стран (http://город.домен/)
                if ($isDefaultCity) { // if this city is city of base domain
                    $url = preg_replace('#^(http|https)\:\/\/([^\/]+)(.*)$#i', '$1://' . $baseDomain . ($redirectMode == 0 ? '$3' : '/'), $currentUrl);
                } else {
                    $url = preg_replace('#^(http|https)\:\/\/([^\/]+)(.*)$#i', '$1://' . $locationCode . '.' . $baseDomain . ($redirectMode == 0 ? '$3' : '/'), $currentUrl);
                }
                $getParamsStr = parse_url($url, PHP_URL_QUERY);
                $url = str_replace(['?', $getParamsStr], '', $url);
                parse_str($getParamsStr, $getParamsArr);
                if ($isBaseDomainWasReplaced) { // если текущий домен не совпадает с базовым доменом, то добавляем параметры query вместо кукисов
                    switch ($typeLocation) {
                        case 'country':
                            $locationParams['MCS_COUNTRY_CODE'] = $location['country_code'];
                            $locationParams['MCS_COUNTRY_NAME'] = $location['country_name'];
                            $locationParams['MCS_PROVINCE_CODE'] = '';
                            $locationParams['MCS_PROVINCE_NAME'] = '';
                            $locationParams['MCS_CITY_CODE'] = '';
                            $locationParams['MCS_CITY_NAME'] = '';
                            break;
                        case 'province':
                            $locationParams['MCS_COUNTRY_CODE'] = $location['country_code'];
                            $locationParams['MCS_COUNTRY_NAME'] = $location['country_name'];
                            $locationParams['MCS_PROVINCE_CODE'] = $location['province_code'];
                            $locationParams['MCS_PROVINCE_NAME'] = $location['province_name'];
                            $locationParams['MCS_CITY_CODE'] = '';
                            $locationParams['MCS_CITY_NAME'] = '';
                            break;
                        case 'city':
                            $locationParams['MCS_COUNTRY_CODE'] = $location['country_code'];
                            $locationParams['MCS_COUNTRY_NAME'] = $location['country_name'];
                            $locationParams['MCS_PROVINCE_CODE'] = $location['province_code'];
                            $locationParams['MCS_PROVINCE_NAME'] = $location['province_name'];
                            $locationParams['MCS_CITY_CODE'] = $location['city_code'];
                            $locationParams['MCS_CITY_NAME'] = $location['city_name'];
                            break;
                    }

                    $locationParams['MCS_LOCATION_TYPE'] = $typeLocation;
                    $locationParams['MCS_NOASK'] = 1;

                    $queryArr = array_merge($getParamsArr, $locationParams);
                    $queryArr['mcs'] = 'mcs_set_location';
                } else {
                    unset($getParamsArr['MCS_COUNTRY_CODE']);
                    unset($getParamsArr['MCS_COUNTRY_NAME']);
                    unset($getParamsArr['MCS_PROVINCE_CODE']);
                    unset($getParamsArr['MCS_PROVINCE_NAME']);
                    unset($getParamsArr['MCS_CITY_CODE']);
                    unset($getParamsArr['MCS_CITY_NAME']);
                    unset($getParamsArr['MCS_LOCATION_TYPE']);
                    unset($getParamsArr['MCS_NOASK']);
                    unset($getParamsArr['mcs']);
                    $queryArr = $getParamsArr;
                }
                $url .= $queryArr ? '?' . http_build_query($queryArr) : '';
                break;
            case 2:
                // Режим подкаталогов (http://домен/город)
                $url = McsModHelper::addLocationToUrl($currentUrl, $locationCode, $isDefaultCity);
                break;
            case 3: case 4:
                // Режим UTM меток
                $currentUrl = self::http_strip_query_param($currentUrl, 'utm_location');
                $currentUrl = self::http_strip_query_param($currentUrl, 'utm_loctype');
                $utm = 'utm_loctype=' . $typeLocation . '&utm_location=' . $locationCode;
                $url = (stripos($currentUrl, '?') !== false) ? $currentUrl . '&' . $utm : $currentUrl . '?' . $utm;
                break;
            default:
                // нет поддоменов или суффиксов, только кукисы
                $url = $currentUrl;
        }

        if (!empty(McsData::get('force_http_url'))) {
            $url = preg_replace("/^https:/i", "http:", $url);
        }

        return $url;
    }

    public static function getHostByLocation($location)
    {
        $url = McsData::buildUrlByLocation($location, $location['type']);
        return parse_url($url, PHP_URL_HOST);
    }

    public static function getBaseNameOfCurrentDomain()
    {
        $host_names = explode('.', $_SERVER['HTTP_HOST']);
        if (count($host_names) > 1) {
            $bottom_host_name = $host_names[count($host_names) - 2]
                . '.' . $host_names[count($host_names) - 1];
        } else {
            $bottom_host_name = $host_names[0];
        }
        return $bottom_host_name;
    }


    public static function http_strip_query_param($url, $param)
    {
        $pieces = parse_url($url);
        if (!$pieces['query']) {
            return $url;
        }
        $query = [];
        parse_str($pieces['query'], $query);
        if (!isset($query[$param])) {
            return $url;
        }
        unset($query[$param]);
        $pieces['query'] = http_build_query($query);

        return
            ((isset($pieces['scheme'])) ? $pieces['scheme'] . '://' : '')
            .((isset($pieces['user'])) ? $pieces['user'] . ((isset($pieces['pass'])) ? ':' . $pieces['pass'] : '') .'@' : '')
            .((isset($pieces['host'])) ? $pieces['host'] : '')
            .((isset($pieces['port'])) ? ':' . $pieces['port'] : '')
            .((isset($pieces['path'])) ? $pieces['$pieces'] : '')
            .((isset($pieces['query'])) ? '?' . $pieces['query'] : '')
            .((isset($pieces['fragment'])) ? '#' . $pieces['fragment'] : '');
    }

}
