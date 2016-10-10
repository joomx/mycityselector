<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 22.06.16
 * Time: 15:25
 */
function yaGeoCode($city)
{
    $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] :
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36';
    $url = 'https://geocode-maps.yandex.ru/1.x/?format=json&lang=RU_ru&kind=locality&geocode=' . $city;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2); // устанавливаем минимальные временные рамки для связи с api
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    $resp = curl_exec($ch);

    if ($json = json_decode($resp, true)) {
        $geos = $json['response']['GeoObjectCollection']['featureMember'];
        foreach ($geos as $geo) {
            $geoMetaData = $geo['GeoObject']['metaDataProperty']['GeocoderMetaData'];
            if ($geoMetaData['kind'] == 'locality') {
                if (isset($geoMetaData['AddressDetails']['Country']['AdministrativeArea']['SubAdministrativeArea']['Locality']['LocalityName'])) {
                    return array(
                        $geoMetaData['AddressDetails']['Country']['AdministrativeArea']['SubAdministrativeArea']['Locality']['LocalityName'],
                        $geoMetaData['AddressDetails']['Country']['AdministrativeArea']['AdministrativeAreaName']

                    );
                } elseif (isset($geoMetaData['AddressDetails']['Country']['AdministrativeArea']['Locality']['LocalityName'])) {
                    return array(
                        $geoMetaData['AddressDetails']['Country']['AdministrativeArea']['Locality']['LocalityName'],
                        $geoMetaData['AddressDetails']['Country']['AdministrativeArea']['AdministrativeAreaName']
                    );
                } else {
                    return '';
                }
            }
        }
    } else {
        return;
    }

}


$db = JFactory::getDbo();
$query = $db->getQuery(true)->select('a.name, b.name')->from('#__mycityselector_city a')->leftJoin('#__mycityselector_region b on a.province_id=b.id');
$cities = $db->setQuery($query)->loadRowList();
foreach ($cities as $city) {
    $yaCity = yaGeoCode($city[1] . ', ' . $city[0]);
    if ($yaCity[0] == $city[0]) {
        out($city[0] . '=' . $yaCity[0] . "     Регион: " . $yaCity[1] . "\r\n", 'blue');
    } else {
        out($city[0] . '=' . $yaCity[0] . "     Регион: " . $yaCity[1] . "\r\n", 'red');
    }
    usleep(500);

}