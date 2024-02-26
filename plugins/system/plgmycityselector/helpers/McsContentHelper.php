<?php
namespace joomx\mcs\plugin\helpers;

defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');

use Joomla\CMS\Language\Text;
use \Joomla\CMS\Factory;


/**
 * Content Helper
 */
class McsContentHelper {

    public static $cases = [2 => 'Genitive', 3 => 'Dative', 4 => 'Accusative', 5 => 'Ablative', 6 => 'Prepositional'];
    // вернул для обратной совместимости
    // {city_name2} или {cityName2} или {cityGenitive} => Родительный (Омска)
    // {city_name3} или {cityName3} или {cityDative} => Дательный (Омску)
    // {city_name4} или {cityName4} или {cityAccusative} => Винительный (Омск)
    // {city_name5} или {cityName5} или {cityAblative} => Творительный (Омском)
    // {city_name6} или {cityName6} или {cityPrepositional} => Предложный (Омске)


    /**
     * Возвращает контент по метке из базы или по простому ID
     * @param $fieldId
     * @return false|mixed|string
     */
    public static function getMcsTagValue($fieldId)
    {
        $fieldId = str_replace(['[', ']', 'mcs-'], '', $fieldId);
        return McsData::loadContent($fieldId);
    }


        /**
         * Replaces country tag to its content
         * @param $body
         * @param $place
         */
        public static function replaceCountryTag(&$body)
        {
            $body = self::filterWrappedTags($body);

            if (preg_match_all("/\[country\s+([^\]]+)\](.*)\[\/country\s*\]/isU", $body, $res)) {
                foreach ($res[1] as $i => $place) {
                    $currentCountry = McsData::getCurrentLocation()['country']['name'];

                    if ($currentCountry != $res[1][$i]) {
                        $body = str_replace($res[0][$i], '', $body);
                    } else {
                        $body = str_replace($res[0][$i], $res[2][$i], $body);
                    }
                }
            }
        }

    /**
     * Parses special MCS tags and returns array of its
     * @param $body
     * @return array
     */
    public static function parseMcsTags(&$body)
    {
        $index = 0;
        $tags = [];
        $body = self::filterWrappedTags($body);
        // ищем теги вида [city Name, Name, Name]
        if (preg_match_all('/\[city\s+([^\]]+)\](.*)\[\/city\s*\]/isU', $body, $res)) {
            // в цикле перебираем найденные куски
            foreach ($res[1] as $index => $city) { // цикл по названиям городов из результата regexp
                // $res[0][$i] - весь найденный блок
                // $res[1][$i] - название города
                // $res[2][$i] - контент блока
                // иногда бывает внутри блока перенос параграфа,
                // например так: <p><span.cityContent>ляляля</p><p>тополя</span></p> браузер
                // это не переваривает и давится, поэтому заменяем это дело внутри блока на <br/>
                $content = $res[2][$index];
                $content = preg_replace('/<\/p>\s*<p>/smU', '<br/>', $content);
                // проверяем какие теги внутри контента, если есть блочные элементы, то оборачиваем в DIV, иначе в SPAN
                $wrap = self::getWrapperTag($content);
                // один из плагинов, работающий выше по порядку, почему-то конвертирует русские буквы в html последовательности (вот гад)
                // пришлось сделать проверку названия города
                if (strpos($city, '&#x') !== false) {
                    $city = html_entity_decode($city, ENT_COMPAT, 'UTF-8');
                }
                // разделяем список городов из тега по ","
                $cities = explode(',', $city);
                // проверяем первый символ первого города, если он равен "!", значит это условие исключения
                if (mb_substr(trim($cities[0]), 0, 1, 'UTF-8') == '!') {
                    $cities[0] = str_replace('!', '', $cities[0]); // удаляем "!"
                    $ignore = true;
                } else {
                    $ignore = false;
                }
                // заменяем весь блок меткой
                $position = '{local-' . $index . '}';
                $body = str_replace($res[0][$index], $position, $body);
                $tags[] = [
                    'type' => 'local',
                    'cities' => $cities,
                    'ignore' => $ignore,
                    'content' => $content,
                    'wrap' => $wrap,
                    'position' => $position
                ];
                McsLog::add('[city ' . $city . ']');
            }
        }
        // ищем html теги со спец классом msc-case
        $dom = new \DomDocument;
        $dom->loadHTML($body, LIBXML_NOERROR);
        $xpath = new \DomXPath($dom);
        $nodes = $xpath->query('//*[contains(@class,"mcs_case_")]');
        if ($nodes->length > 0) {
            foreach ($nodes as $node) {
                $index++;
                $outerHtml = $node->ownerDocument->saveHTML($node);
                $classValue = null;
                foreach ($node->attributes as $attr) {
                    if ($attr->name === 'class') {
                        preg_match('/[^ ]*\s*mcs\_case\_([^ ]+)\s*[^ ]*/is', $attr->value, $math);
                        if (!empty($math) && isset($math[1])) {
                            $classValue = $math[1];
                        }
                        break;
                    }
                }
                if (empty($classValue)) break;
                // один из плагинов, работающий выше по порядку, почему-то конвертирует русские буквы в html последовательности (вот гад)
                // пришлось сделать проверку названия города
                if (strpos($classValue, '&#x') !== false) {
                    $classValue = html_entity_decode($classValue, ENT_COMPAT, 'UTF-8');
                }
                // поскольку у некоторых городов название из двух слов, и между ними пробел, то этот пробел нужно как-то обозначить, я выбрал "__"
                $classValue = str_replace('__', ' ', $classValue);
                // разделяем список городов из тега по "_"
                $cities = explode('_', $classValue);
                // заменяем весь блок меткой
                $position = '{local-' . $index . '}';
                $markElement = $dom->createTextNode('{local-' . $index . '}');
                $node->parentNode->replaceChild($markElement, $node);
                $tags[] = [
                    'type' => 'local',
                    'cities' => $cities,
                    'ignore' => false,
                    'content' => preg_replace('/<\/p>\s*<p>/smU', '<br/>', $outerHtml),
                    'wrap' => $wrap,
                    'position' => $position
                ];
                McsLog::add('[msc_case: ' . implode(',', $cities) . ']');
            }
            $body = $dom->saveHTML();
        } else {
            unset($xpath, $dom);
        }
        // ищем теги вида [mcs-ID title] где title не важен для кода, только для пользователя для наглядности
        if (preg_match_all('/\[mcs\-(\d+)\s+[^\]]+\]/is', $body, $res)) {
            foreach ($res[1] as $index => $city) { // цикл по названиям городов из результата regexp
                // $res[0][$i] - весь найденный блок
                // $res[1][$i] - ID контента
                // заменяем весь блок меткой
                $position = '{mcs-' . $res[1][$index] . '}';
                $body = str_replace($res[0][$index], $position, $body);
                $tags[] = ['type' => 'db', 'field_id' => $res[1][$index], 'position' => $position];
                McsLog::add('[mcs-' . $res[1][$index] . ']');
            }
        }
        return $tags;
    }


    private static function filterWrappedTags($body)
    {
        // сложность в том, что в визуальном редакторе метки обрамляются тегами
        for ($i = 3; $i--;) { // надеюсь, что больше трех оберточных тегов не будет
        	// Нельзя добавлять модификатор u (PCRE_UTF8). Если на странице будут битые данные UTF, выведет пустую страницу.
            $body = preg_replace('/<span[^>]+>(\[city\s+[^\]]+\])<\/span>/sU', '$1', $body);
            $body = preg_replace('/<span>(\[\/city\s*\])<\/span>/sU', '$1', $body);
            $body = preg_replace('/<p[^>]+>(\[city\s+[^\]]+\])<\/p>/sU', '$1', $body);
            $body = preg_replace('/<p>(\[\/city\s*\])<\/p>/sU', '$1', $body);
        }
        return $body;
    }


    /**
     * Deletes rows of package elements from Extensions/Manage list
     * @param $body
     * @return mixed
     */
    public static function removePackageElements($body)
    {
        if (preg_match('/<table[^>]+id="manageList"[^>]*>(.*)<\/table>/isU', $body, $table)) {
            if (preg_match('/<tbody[^>]*>(.*)<\/tbody>/isU', $table[1], $tbody)) {
                if (preg_match_all('/(<tr[^>]*>.*<\/tr>)/isU', $tbody[1], $trows)) {
                    foreach ($trows[1] as $trow) {
                        if (preg_match('/my\s*city\s*selector/i', $trow)) {
                            if (stripos($trow, Text::_('COM_INSTALLER_TYPE_PACKAGE')) === false) {
                                // remove this row
                                $body = str_replace($trow, '', $body);
                            }
                        }
                    }
                }
            }
        }
        return $body;
    }


    /**
     * Processing old tags (local tags)
     * @param string $body
     * @param array $data
     * @return bool Returns True if content inserted
     */
    public static function processingLocalData(&$body, $data)
    {
        $currentCityName = McsData::get('locationName');
        $currentCityCode = McsData::get('location');
        if ($data['ignore'] == true) {
            // если это список отрицания, то работает все наоборот.
            // если текущего города нет в списке, то отображаем текст
            if (!in_array($currentCityName, $data['cities'])) {
                $cities = [$currentCityName];
            } else {
                $cities = [];
            }
        } else {
            $cities = $data['cities'];
        }
        // сверяем с текущим городом
        foreach ($cities as $city) {
            if ($city == $currentCityName) {
                $body = self::insertContentData($body, $data, $currentCityCode);
                return true;
            }
        }
        $body = str_replace($data['position'], '', $body);
        return false;
    }


    /**
     * Processing data from DB
     * @param string $body
     * @param array $data
     * @return bool Returns True if content inserted
     */
    public static function processingDbData(&$body, $data)
    {
        $content = McsData::loadContent($data['field_id']);

        if ($content !== false) {
            $data['wrap'] = self::getWrapperTag($content);
            $data['content'] = $content;
            $currentCityCode = McsData::get('location');
            $body = self::insertContentData($body, $data, $currentCityCode);
            return true;
        }
        $body = str_replace($data['position'], '', $body);
        return false;
    }


    /**
     * @param $body
     * @param $data
     * @param string $currentCityCode
     * @return mixed
     */
    public static function insertContentData($body, $data, $currentCityCode='local')
    {
        $isDebug = McsData::get('debug_mode');
        $isHiddenDebug = McsData::get('debug_mode_hidden') == '1';
        $html = '';
        if (!empty($data['wrap'])) {
            $color = $isDebug ? 'style="color: #2922ff;"' : '';
            $html .= '<' . $data['wrap'] . ' class="mcs-content-' . $currentCityCode . '" ' . $color . '>';
        }
        if ($isDebug) {
            if ($isHiddenDebug) {
                $html .= "<!-- {$data['position']} -->";
            } else {
                $html .= '<span style="color: #ff6f14;">' . $data['position'] . '</span>&nbsp;&nbsp;';
            }
        }
        McsLog::add('position ' . $data['position']);
        $html .= $data['content'];
        if (!empty($data['wrap'])) {
            $html .= '</' . $data['wrap'] . '>';
        }
        $body = str_replace($data['position'], $html, $body);
        return $body;
    }


    /**
     * Checking blocks elements in content
     * @param $content
     * @return bool
     */
    public static function getWrapperTag($content) {
        $blockTags = ['div', 'h1', 'h2', 'h3', 'h4', 'h5', 'p', 'hr', 'ul', 'ol',
            'blockquote', 'form', 'pre', 'table', 'address'];
        if (preg_match('/\<(\w+)[^>]*\>/', $content, $match)) {
            return (in_array($match[1], $blockTags)) ? 'div' : 'span';
        }
        return '';
    }


    /**
     * Заменяем метки локаций на метки города
     * @param $body
     * @return string|string[]
     */
    public static function replaceLocationsByCityMarkers($body)
    {
        $body = str_replace('{locationCode}', '{cityCode}', $body);
        $body = str_replace('{locationName}', '{cityName}', $body);
        // Склонения
        foreach (self::$cases as $_case) {
            $body = str_replace("{location{$_case}}", "{city{$_case}}", $body);
        }
        return $body;
    }


    /**
     * Заменяет метки локаций на метки региона
     * @param $body
     * @return string
     */
    public static function replaceLocationsByProvinceMarkers($body)
    {
        $body = str_replace('{locationCode}', '{regionCode}', $body);
        $body = str_replace('{locationName}', '{regionName}', $body);
        // Склонения
        foreach (self::$cases as $_case) {
            $body = str_replace("{location{$_case}}", "{province{$_case}}", $body);
        }
        return $body;
    }


    /**
     * Замена {locationCode} на метку Страны
     * @param $body
     * @return array|string|string[]
     */
    public static function replaceLocationsByCountryMarkers($body)
    {
        $body = str_replace('{locationCode}', '{countryCode}', $body);
        $body = str_replace('{locationName}', '{countryName}', $body);
        // Склонения
        foreach (self::$cases as $_case) {
            $body = str_replace("{location{$_case}}", "{country{$_case}}", $body);
        }
        return $body;
    }


    /**
     * Когда выбран город (и мы знаем страну и регион)
     * @param $body
     * @return string
     */
    public static function replaceStaticMarkersByCity($body)
    {
        // Страна, Область, Город
        $body = str_replace(['{country_code}', '{countryCode}'], McsData::get('country'), $body);
        $body = str_replace(['{country}', '{countryName}'], McsData::get('countryName'), $body);
        $body = str_replace(['{province_code}', '{provinceCode}'], McsData::get('province'), $body);
        $body = str_replace(['{province}', '{provinceName}'], McsData::get('provinceName'), $body);
        $body = str_replace(['{city_code}', '{cityCode}'], McsData::get('city'), $body);
        $body = str_replace(['{city_name}', '{cityName}'], McsData::get('cityName'), $body);

        // Склонения
        foreach (self::$cases as $_key => $_case) {
            // страна
            $body = str_replace("{country{$_case}}", McsData::get("country{$_case}"), $body);
            // регион
            $body = str_replace("{province{$_case}}", McsData::get("province{$_case}"), $body);
            // город
            $body = str_replace("{city{$_case}}", McsData::get("city{$_case}"), $body);
            $body = str_replace("{city_name{$_key}}", McsData::get("city{$_case}"), $body); // для обратной совместимости
        }

        return $body;
    }


    /**
     * Когда выбрана только страна и регион, но не город
     * @param $body
     * @return string
     */
    public static function replaceStaticMarkersByProvince($body)
    {
        // страна, регион
        $body = str_replace(['{country_code}', '{countryCode}'], McsData::get('country'), $body);
        $body = str_replace(['{country}', '{countryName}'], McsData::get('countryName'), $body);
        $body = str_replace(['{province_code}', '{provinceCode}'], McsData::get('province'), $body);
        $body = str_replace('{provinceName}', McsData::get('provinceName'), $body);
        // удаляем метки города, так как для него нет данных
        $body = str_replace(['{city_code}', '{cityCode}', '{city_name}', '{cityName}'], '', $body);

        // Склонения
        foreach (self::$cases as $_case) {
            $body = str_replace("{province{$_case}}", McsData::get("province{$_case}"), $body);
            $body = str_replace("{country{$_case}}", McsData::get("country{$_case}"), $body);
            // удаляем метки города, так как для него нет данных
            $body = str_replace("{city{$_case}}", '', $body);
        }

        return $body;
    }


    /**
     * Когда выбрана только страна (не регион или город)
     * @param $body
     * @return string
     */
    public static function replaceStaticMarkersByCountry($body)
    {
        $body = str_replace(['{country_code}', '{countryCode}'], McsData::get('country'), $body);
        $body = str_replace(['{country}', '{countryName}'], McsData::get('countryName'), $body);
        // удаляем метки не относящиеся к стране, потому что для них нет данных
        $body = str_replace(['{region}', '{region_code}', '{provinceCode}', '{provinceName}', '{province}'], '', $body);
        $body = str_replace(['{city_code}', '{cityCode}', '{city_name}', '{cityName}'], '', $body);

        // Склонения
        foreach (self::$cases as $_case) {
            $body = str_replace("{country{$_case}}", McsData::get("country{$_case}"), $body);
            // удаляем метки не относящиеся к стране, потому что для них нет данных
            $body = str_replace("{province{$_case}}", '', $body);
            $body = str_replace("{city{$_case}}", '', $body);
        }

        return $body;
    }



    /**
     * Это экспериментальная функция, она заменяет параметры сайта на данные из спец конфига
     *
     * @param array $experimentalConfig
     */
    public static function experimentalModifyConfig($experimentalConfig)
    {
        $config = Factory::getConfig();
        if (!empty($experimentalConfig['configuration'])) {
            $cfg = $experimentalConfig['configuration'];
            foreach ($cfg as $param => $values) {
                foreach ($values as $value) {
                    if (!empty($value['city'])) {
                        $cityId = McsData::get('cityId');
                        if ($cityId && in_array($cityId, $value['city'])) {
                            $config->set($param, $value['new_value']);
                        }
                    } elseif (!empty($value['province'])) {
                        $provinceId = McsData::get('provinceId');
                        if ($provinceId && in_array($provinceId, $value['province'])) {
                            $config->set($param, $value['new_value']);
                        }
                    } elseif (!empty($value['country'])) {
                        $countryId = McsData::get('countryId');
                        if ($countryId && in_array($countryId, $value['country'])) {
                            $config->set($param, $value['new_value']);
                        }
                    }
                }
            }
        }
    }


    /**
     * Это экспериментальная функция, она заменяет данные прочитанные из базы на данные из спец конфига
     *
     * @param array $stack
     * @param array $experimentalConfig
     */
    public static function experimentalModifyDb($stack, $experimentalConfig)
    {
        if (!empty($experimentalConfig['database'])) {
            $cfg = $experimentalConfig['database'];

            // TODO пока что меняем данные без проверки на таблицу и поле
            if (isset($cfg['any'])) { // ANY Table & ANY Field
                foreach ($cfg['any'] as $value) {
                    if (!empty($value['city'])) {
                        $cityId = McsData::get('cityId');
                        if ($cityId && in_array($cityId, $value['city'])) {
                            self::_replaceDbValue($stack, $value['original'], $value['new_value']);
                        }
                    } elseif (!empty($value['province'])) {
                        $provinceId = McsData::get('provinceId');
                        if ($provinceId && in_array($provinceId, $value['province'])) {
                            self::_replaceDbValue($stack, $value['original'], $value['new_value']);
                        }
                    } elseif (!empty($value['country'])) {
                        $countryId = McsData::get('countryId');
                        if ($countryId && in_array($countryId, $value['country'])) {
                            self::_replaceDbValue($stack, $value['original'], $value['new_value']);
                        }
                    }
                }
            }
        }
        return $stack;
    }


    // TODO это отстой, написать что-то получше
    private static function _replaceDbValue($stack, $oldValue, $newValue)
    {
        if ($stack['method'] === 'fetchArray') {
            if (!empty($stack['data'])) {
                if (is_array($stack['data'])) {
                    foreach ($stack['data'] as $key => $value) {
                        if ($value == $oldValue) {
                            $stack['data'][$key] = $newValue;
                        }
                    }
                }
                if (is_string($stack['data'])) {
                    if ($stack['data'] == $oldValue) {
                        $stack['data'] = $newValue;
                    }
                }
            }
        } else if ($stack['method'] === 'fetchAssoc') {
            if (!empty($stack['data'])) {
                foreach ($stack['data'] as $key => $value) {
                    if ($value == $oldValue) {
                        $stack['data'][$key] = $newValue;
                    }
                }
            }
        } else if ($stack['method'] === 'fetchObject') {
            if (!empty($stack['data'])) {
                $properties = get_object_vars($stack['data']);
                foreach ($properties as $property => $value) {
                    if ($value == $oldValue) {
                        $stack['data']->{$property} = $newValue;
                    }
                }
            }
        }
        return $stack;
    }

}