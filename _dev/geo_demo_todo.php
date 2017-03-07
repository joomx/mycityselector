<?php

// TODO новая геолокация

error_reporting(E_ALL);
ini_set('display_errors', true);
header('Content-type: text/html; charset=UTF-8');


include('geo.php');
$geo = new Geo(); // запускаем класс
// Если хотите передать в функцию уже известный IP, то можно сделать так
// $o['ip'] = '178.204.102.30'; <-- Пример IP адреса г. Казань
// $geo = new Geo($o);
// этот метод позволяет получить все данные по ip в виде массива.
// массив имеет ключи 'inetnum', 'country', 'city', 'region', 'district', 'lat', 'lng'
$data = $geo->get_value();

// если нужен какой то отдельный параметр, передаем его в функцию в виде первого значения
//$data = $geo->get_value('city'); // например, вернет название города
# $data = $geo->get_value('country'); // вернет название страны
# $data = $geo->get_value('region'); // вернет название региона
# $data = $geo->get_value('district'); // вернет название района
# lat - географическая ширина и lng - долгота
# inetnum - диапазон ip адресов, в который входит проверяемый ip адрес
// чтобы использовать кеширование нужно в функцию передать второй параметр - true или false
# пример 
//$data = $geo->get_value('city', true); 
// если true, то данные о городе пользователя сохранятся в куки браузера
// в этом случае повторный запрос для проверки происходить не будет. 
// это рекомендуется и поэтому по-умолчанию кешеривание включено
# пример 
//$data = $geo->get_value('city', false);
// если false, то данные каждый раз будут запрашиваться с сервера ipgeobase    
//также кеширование используется и для других параметров   
// показ информации в зависимости от города
# пример работы

echo '<p style="color:blue;">Пример работы:</p>';

$city = $geo->get_value('city', true);
if ($city == 'Казань') {
    echo 'Наш телефон для Казани 1111111';
} elseif ($city == 'Москва') {
    echo 'Телефон для Москвы 2222222';
} elseif ($city == 'Санкт-Петербург') {
    echo 'Телефон для Санкт-Петербурга 3333333';
} elseif ($city == 'Тюмень') {
    echo 'Телефон для Тюмени 4444444';
} else {
    echo 'Ваш город ' . $city . '.';
    var_dump($city);
}
// конец примера
echo '<hr/>';
echo '<p style="color:blue;">Расширенные данные полученные из ipgeobase:</p>';
echo '<pre>';
echo 'Все данные: ' . "\n";
print_r($data);
echo '</pre>';

// также данный класс можно использовать для получения и проверки валидности реального ip адреса посетителя

$city = $geo->get_value('city', true); // получаем название города
echo 'Достаем город<br />';
echo $city . '<br />';

$ip = $geo->get_ip(); // получаем ip адрес
echo 'Достаем IP<br />';
echo $ip . '<br />';

if ($geo->is_valid_ip($ip)) {
    echo 'IP адрес валиден';
} else {
    echo 'IP адрес не валиден';
}

// Пробуем достать данные из куки
//echo "<hr/>Данные из Cookie<br/>";
echo '<hr/><p style="color:blue;">Сохраненные данные из Cookie</p>';
if (isset($_COOKIE['geobase'])) {
    echo '<p style="color:green">В cookie содержатся гео-данные. Можно их использовать без повторного запроса к ipgeobae!</p>';
    $geobase = $_COOKIE['geobase'];
    echo '<pre>';
    print_r(unserialize($geobase));
    echo '</pre>';

    $city = $geo->get_value('city', false);
    echo $city;
} else {
    echo '<p style="color:red">Это первое открытие страницы. Данные из куки будут получены после перезагрузки страницы<p>';
}

exit();
/**
 * с вопросами
 * @site http://faniska.ru
 * @email ya.faniska@gmail.com
 */