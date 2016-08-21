<?php

/**
 * Content Helper
 */
class McsContentHelper {

    /**
     * Parses special MCS tags and returns array of its
     * @param $body
     * @return array
     */
    public static function parseCitiesTags(&$body)
    {
        $tags = [];
        $body = self::filterWrappedTags($body);
        // ищем теги вида [city Name, Name, Name]
        if (preg_match_all('/\[city\s+([^\]]+)\](.+)\[\/city\s*\]/isU', $body, $res)) {
            // в цикле перебираем найденные куски
            foreach ($res[1] as $i => $city) { // цикл по названиям городов из результата regexp
                // $res[0][$i] - весь найденный блок
                // $res[1][$i] - название города
                // $res[2][$i] - контент блока
                // иногда бывает внутри блока перенос параграфа,
                // например так: <p><span.cityContent>ляляля</p><p>тополя</span></p> браузер
                // это не переваривает и давится, поэтому заменяем это дело внутри блока на <br/>
                $content = $res[2][$i];
                $content = preg_replace('/<\/p>\s*<p>/smU', '<br/>', $content);
                // проверяем какие теги внутри контента, если есть блочные элементы, то оборачиваем в DIV, иначе в SPAN
                $wrap = self::isHasBlockTag($content) ? 'div' : 'span';
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
                $position = '{local-' . $i . '}';
                $body = str_replace($res[0][$i], $position, $body);
                $tags[] = ['type' => 'local', 'cities' => $cities, 'ignore' => $ignore,
                    'content' => $content, 'wrap' => $wrap, 'position' => $position];
            }
        }
        // ищем теги вида [mcs-ID title] где title не важен для кода, только для пользователя для наглядности
        if (preg_match_all('/\[mcs\-(\d+)\s+[^\]]+\]/is', $body, $res)) {
            foreach ($res[1] as $i => $city) { // цикл по названиям городов из результата regexp
                // $res[0][$i] - весь найденный блок
                // $res[1][$i] - ID контента
                // заменяем весь блок меткой
                $position = '{mcs-' . $res[1][$i] . '}';
                $body = str_replace($res[0][$i], $position, $body);
                $tags[] = ['type' => 'db', 'field_id' => $res[1][$i], 'position' => $position];
            }
        }
        return $tags;
    }


    private static function filterWrappedTags($body)
    {
        // сложность в том, что в визуальном редакторе метки обрамляются тегами
        for ($i = 3; $i--;) { // надеюсь, что больше трех оберточных тегов не будет
            $body = preg_replace('/<span[^>]+>(\[city\s+[^\]]+\])<\/span>/usU', '$1', $body);
            $body = preg_replace('/<span>(\[\/city\s*\])<\/span>/sU', '$1', $body);
            $body = preg_replace('/<p[^>]+>(\[city\s+[^\]]+\])<\/p>/usU', '$1', $body);
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
                            if (stripos($trow, 'Package') === false) {
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
        $currentCityName = McsData::get('cityName');
        $currentCityCode = McsData::get('city');
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
            $data['wrap'] = self::isHasBlockTag($content) ? 'div' : 'span';
            $data['content'] = $content;
            $currentCityCode = McsData::get('city');
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
        $color = $isDebug ? 'style="color: #2922ff;"' : '';
        $html = '<' . $data['wrap'] . ' class="mcs-content-' . $currentCityCode . '" ' . $color . '>';
        if ($isDebug) {
            $html .= '<span style="color: #ff6f14;">' . $data['position'] . '</span>&nbsp;&nbsp;';
        }
        $html .= $data['content'];
        $html .= '</' . $data['wrap'] . '>';
        $body = str_replace($data['position'], $html, $body);
        return $body;
    }


    /**
     * Checking blocks elements in content
     * @param $content
     * @return bool
     */
    public static function isHasBlockTag($content) {
        if (stripos($content, '<div') === false
            && stripos($content, '<h1') === false
            && stripos($content, '<h2') === false
            && stripos($content, '<h3') === false
            && stripos($content, '<h4') === false
            && stripos($content, '<h5') === false
            && stripos($content, '<p') === false
            && stripos($content, '<hr') === false
            && stripos($content, '<ul') === false
            && stripos($content, '<ol') === false
            && stripos($content, '<blockquote') === false
            && stripos($content, '<form') === false
            && stripos($content, '<pre') === false
            && stripos($content, '<table') === false
            && stripos($content, '<address') === false) {
            return false;
        }
        return true;
    }

} 