<?php
/**
 * Делает запрос на сервис геолокации Яндекс и возвращает название города
 */

if (!isset($_REQUEST['key']) || $_REQUEST['key'] != 'sv84ts934pesgs037cw0bynh23z0-203c0-039c9ru') {
    exit(header("HTTP/1.0 404 Not Found") . '404 Not Found (pk empty)');
}

if (!isset($_REQUEST['lon']) || !isset($_REQUEST['lat'])) {
    exit(json_encode(['error' => 1]));
}

$userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] :
    'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36';
$latitude = $_REQUEST['lat']; // широта
$longitude = $_REQUEST['lon']; // долгота

// для тестов
//$latitude = '55.7522200'; $longitude = '37.6155600'; // Москва
//$latitude = '59.9386300'; $longitude = '30.3141300'; // Санкт-Петербург
//$latitude = '43.1056200'; $longitude = '131.8735300'; // Владивосток


// http://geocode-maps.yandex.ru/1.x/?format=json&lang=RU_ru&kind=locality&geocode={longitude},{latitude}
$url = 'https://geocode-maps.yandex.ru/1.x/?'
    . 'format=json&lang=RU_ru&kind=locality&spn=0.001,0.001&geocode=' . $longitude . ',' . $latitude;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1); // устанавливаем минимальные временные рамки для связи с api
curl_setopt($ch, CURLOPT_TIMEOUT, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$resp = curl_exec($ch);
$json = json_decode($resp, true);

if (is_array($json)) {
    if (isset($json['response'])
        && isset($json['response']['GeoObjectCollection'])
            && isset($json['response']['GeoObjectCollection']['featureMember'])
                && count($json['response']['GeoObjectCollection']['featureMember']) > 0) {
        $data = $json['response']['GeoObjectCollection']['featureMember'][0]['GeoObject'];
        exit(json_encode(array('error' => 0, 'name' => $data['name'], 'description' => $data['description'])));
    }
}

exit(json_encode(['error' => 2]));


