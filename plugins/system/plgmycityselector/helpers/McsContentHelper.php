<?php

/**
 * Content Helper
 */
class McsContentHelper {

    /**
     * Парсинг спец. тегов
     * @param $body
     * @return array
     */
    public static function parseCitiesTags($body)
    {
        $tags = [];
        $body = self::filterWrappedTags($body);
        // ищем теги вида [city Name, Name, Name]
        if (preg_match_all('/\[city\s+([^\]]+)\](.+)\[\/city\s*\]/isU', $body, $res)) {
            // в цикле перебираем найденные куски
            $isCityFound = false; // флаг: был ли найден
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
                $tag = self::isHasBlockTag($content) ? 'div' : 'span';
                // один из плагинов, работающий выше по порядку, почему-то конвертирует русские буквы в html последовательности (вот гад)
                // пришлось сделать проверку названия города
                if (strpos($city, '&#x') !== false) {
                    $city = html_entity_decode($city, ENT_COMPAT, 'UTF-8');
                }
                // разделяем список городов из тега по ","
                $cities = explode(',', $city);
                // проверяем первый символ первого города, если он равен "!", значит это условие исключения
                if (mb_substr(trim($cities[0]), 0, 1, 'UTF-8') == '!') {
                    // условие исключения
                    $cities[0] = str_replace('!', '', $cities[0]); // удаляем "!"
                    // еще нужно понять, чем является список: города, регионы, страны
                    $type = McsData::getTypeByName($cities[0]);
                    if ($type == 'city') {
                        $newCities = McsData::getCities();
                        foreach ($cities as $cityName) {
                            if (isset($newCities[$cityName])) {
                                unset($newCities[$cityName]); // исключаем города, которые перечислены в теге
                            }
                        }


                    } else if ($type == 'province') {



                    } else if ($type == 'country') {



                    }
                    // теперь нужно составить новый список городов, за исключением тех, которые перечислены в теге
                    $cities = array_keys($newCities); // получаем список нужных городов
                    unset($newCities);
                }

                // заменяем найденные теги на контент соответствующего города
                $html = 'TODO';
                $body = str_replace($res[0][$i], $html, $body);

            }

        }

        // todo
        // ищем теги вида [msc-ID title] где title не важен для кода, только для пользователя для наглядности
        // не забыть проверять is_published!
        if (preg_match_all('/\[city\s+([^\]]+)\](.+)\[\/city\s*\]/usU', $body, $res)) {
            foreach ($res[1] as $i => $city) { // цикл по названиям городов из результата regexp



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


    // OLD CODE
    public static function _parseCitiesTags($body, $currentCity, $citiesList)
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
        if (preg_match_all('/\[city\s+([^\]]+)\](.+)\[\/city\s*\]/usU', $body, $res)) {
            $json = [];
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
                // проверяем какие теги внутри контента, если есть блочные элементы, то оборачиваем в DIV, иначе в SPAN
                $tag = self::isHasBlockTag($content) ? 'div' : 'span';
                // один из плагинов, работающий выше по порядку, почему-то конвертирует русские буквы в html последовательности (вот гад)
                // пришлось сделать проверку названия города
                if (strpos($city, '&#x') !== false) {
                    $city = html_entity_decode($city, ENT_COMPAT, 'UTF-8');
                }
                // разделяем по ","
                $cities = explode(',', $city);
                // проверяем первый символ первого города, если он равен "!", значит это условие исключения
                if (mb_substr(trim($cities[0]), 0, 1, 'UTF-8') == '!') {
                    // условие исключения
                    $cities[0] = str_replace('!', '', $cities[0]);
                    // теперь нужно составить новый список городов, за исключением тех, которые перечислены в теге
                    $newCities = $citiesList['__all__']; // копируем список всех городов
                    foreach ($cities as $cityName => $data) {
                        if (isset($newCities[$cityName])) {
                            unset($newCities[$cityName]); // исключаем города, которые перечислены в теге
                        }
                    }
                    $cities = array_keys($newCities); // получаем список нужных городов
                    unset($newCities);
                }
                if (count($cities) > 1) {
                    // формируем групповой блок, если городов > 1
                    $trcity = 'cities-group'; // группа
                    if (!isset($json[$trcity])) {
                        $json[$trcity] = [];
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
                            $trcityG = MCSTranslit::convert($cval);
                        }
                        if (!isset($json[$trcityG])) {
                            $json[$trcityG] = [];
                        }
                        $indexG = count($json[$trcityG]);
                        $json[$trcityG][$indexG] = '{{$cities-group}}=' . $index; // ссылка на групповой блок с общим контентом
                        $class .= ' city-' . $trcityG . '-' . $indexG;
                        if ($cval == $currentCity) {
                            $findedInGroup = true; // этот блок для текущего выбранного города, ставим флаг, чтобы не отображался блок [city *]
                            $finded = true;
                        }
                    }
                    $html = '<' . $tag . ' class="cityContent' . $class . '">';
                    if ($findedInGroup == true) {
                        $html .= $content;
                    }
                    $html .= '</' . $tag . '>';
                } else {
                    // иначе одиночный блок
                    $cval = trim($cities[0]);
                    if ($cval == '*') {
                        $trcity = 'other';
                    } else {
                        $trcity = MCSTranslit::convert($cval);
                    }
                    if (!isset($json[$trcity])) {
                        $json[$trcity] = [];
                    }
                    $index = count($json[$trcity]);
                    // $json[ город ][номер блока с текстом ]
                    $json[$trcity][$index] = $content;
                    $class = ' city-' . $trcity . '-' . $index;
                    $html = '<' . $tag . ' class="cityContent' . $class . '">';
                    if ($cval == $currentCity) {
                        $finded = true; // этот блок для текущего выбранного города, ставим флаг, чтобы не отображался блок [city *]
                        $html .= $content;
                    }
                    $html .= '</' . $tag . '>';
                }
                $body = str_replace($res[0][$i], $html, $body);
            }
            if ($finded == false && isset($json['other'])) {
                // если город не был найден, то при наличии блока "прочие", подставляем текст обратно в страницу
                foreach ($json['other'] as $index => $content) {
                    $tag = self::isHasBlockTag($content) ? 'div' : 'span';
                    $body = str_replace(
                        '<' . $tag . ' class="cityContent city-other-' . $index . '"></' . $tag . '>',
                        '<' . $tag . ' class="cityContent city-other-' . $index . '">' . $content . '</' . $tag . '>',
                        $body
                    );
                }
            }
            // inject json to page
            $json = '<script type="text/javascript">var citySelectorContents = ' . json_encode($json) . ';</script>';
            $body = str_replace('</head>', $json . "\n</head>", $body);
        }

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