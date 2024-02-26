My City Selector Joomla Extension
=================================

## Внимание

ИСХОДНЫЙ КОД ВЫЛОЖЕН В ПУБЛИЧНЫЙ ДОСТУП НАВСЕГДА.
НЕ ПОКУПАЙТЕ данное расширение ни у кого, так как теперь оно БЕСПЛАТНОЕ.

Рано или поздно приходит момент, когда приоритеты меняются и такой момент пришел и к нам.
Больше мы не можем заниматься поддержкой расширения. Поэтому было решено выложить все в открытый доступ,
чтобы те, кто уже покупал или кто собирался использовать это расширение,
смогли самостоятельно продолжить его поддержку.

Я готов добавить в соавторы тех, всех захочет продолжить делать комиты в этот репозиторий.
Помимо прочего, буду стараться принимать pull request'ы время от времени.

Оставляем три ветки:
 
 - master (самая последняя версия под joomla 4/5, по сути просто адаптация старой версии)
 - mcs_3_40_0 (последний релиз старой версии для joomla 3)
 - development (для работы над текущими обновлениями) 

Информация о сборке установочного пакета находится здесь: [jexter/readme.md](jexter/readme.md)


##Системные требования

Joomla >= 4.0 (последний тест на 5.0)<br>
PHP >= 8.1<br>
PHP Extension [ionCube](http://jbzoo.ru/docs/ioncube-installing)

## Общие сведения

My City Selector (MCS) - это расширение для CMS Joomla, позволяющее отображать разную информацию для разных городов.

<img src="https://raw.githubusercontent.com/joomx/mycityselector/free/doc_images/image-1.png" alt="" />

> *!* Ваш домен должен быть настроен так, чтобы любой произвольный поддомен открывал основной сайт.
> Не нужно создавать много сайтов :) это неверно. Подробнее о настройках домена можно прочитать [тут](cookbook/configure_domain.md).

*Примечание: все изображения приводимые здесь основаны на версии Joomla 3.6.x

## Установка

Скачиваете отсюда: https://github.com/art-programming-team/mycityselector/releases

Расширение включает в себя два плагина (system/plgmycityselector & editors-xtd/mcsinsert), компонент (com_mycityselector) и модуль (mod_mycityselector). Все они ставяться одним пакетом,
поэтому загруженный архив распаковывать не нужно. Устанавливайте как есть.
Помимо самого расширения, вам потребуется установленный php модуль [ionCube](loader.md).

## Настройка

Для того, чтобы начать пользоваться расширением, необходимо сделать две вещи:

 - Включить модуль "My City Selector MOD" и настроить его.
 - Прописать в настройках компонента "MyCitySelector" базовый домен вашего сайта.

<img src="https://raw.githubusercontent.com/joomx/mycityselector/free/doc_images/config.jpg" alt="" />

После этого, всё должно заработать.

## Как это использовать?

Расширение позволяет создавать заготовленные тексты для разных городов и в зависимости от
выбранного пользователем города, подставлять их в страницу. Каждому городу на сайте будет
соответствовать свой поддомен, а главный домен будет соответствовать вашему городу
(который вы можете указать в настройках).
Например, ваш сайт krakozyabra.org для вашего родного города по умолчанию. А остальные
города на поддоменах:<br>
spb.krakozyabra.org<br>
minsk.krakozyabra.org<br>
kiev.krakozyabra.org<br>
и так далее в соответствии с настройками.
Позже мы добавим (вернем) возможность указывать для городов не только поддомены но и страницы (в рамках одного домена).

Управление текстами происходит через компонент MyCitySelector в админке, там же и управление
списком городов и настройки компонента.

<img src="https://raw.githubusercontent.com/joomx/mycityselector/free/doc_images/image-2.png" alt="" />

Часть настроек находится в модуле, который отвечает за отображаемое окно выбора города на сайте.

Для вставки заготовленных текстов на страницы сайта используются специальные маркеры (теги).
Всего есть три вида маркеров:

 - [city Город] текст [/city] - из первой версии
 - {mcs-N} - новый маркер, появился во второй версии.
 - {city_name} , {location_name} - спец. маркеры позволяющие выводить название города либо локации в мета теги (подробнее ниже)
 - {city_code}, {cityCode}, {province_code}, {provinceCode}, {country_code}, {countryCode} - символьные коды города, региона, страны

Еще, Вы можете получить название текущего города в своем коде черезе команды

```
$cityCode = McsData::get('city');
$cityName = McsData::get('cityName');
```

Итак, тег [city] удобен для небольших надписей или сообщений (и для небольшего количества городов).
Но если городов много и информация для каждого своя, то лучше воспользоваться компонентом
MSC в админке. Основной недостаток этих тегов в том, что на одной странице все теги взаимосвязаны
и отобразить разную информацию в нескольких местах страницы может быть невозможным.
Но в то же время, они могут быть незаменимы, если вам нужно включать разные позиции
модулей для разных городов.
Например так:

```
[city Омск]<jdoc:include type="modules" name="demo1" style="" />[/city]
[city Чита]<jdoc:include type="modules" name="demo2" style="" />[/city]
```

В этом случае, при выборе города "Омск" будут отображаться все модули из позиции "demo1",
а при выборе города "Чита" - из "demo2". Советуем использовать их только при необходимости.

Маркеры {mcs-ID} более продвинутые. Их может быть много на одной странице
и у каждого свои условия по городам. Кроме того, вам не нужно вводить их вручную.
В редакторе Вы можете найти кнопку для вставки маркера в текущую позицию курсора.

<img src="https://raw.githubusercontent.com/joomx/mycityselector/free/doc_images/image-3.jpg" alt="" />

Выбираете нужный контент из списка и вставляете маркер в текст. Все просто.

Перейдем к рассмотрению компонента "MyCitySelector". Откройте подпункт "Страны".

<img src="https://raw.githubusercontent.com/joomx/mycityselector/free/doc_images/image-5.png" alt="" />

При клике по ссылке "регионы" вы сможете открыть список регионов, относящихся к данной стране. Аналогично и в списке
регионов, так есть ссылка "города". Делая элементы списков неактывными, вы запрещаете их отображение в окне выбора города.
Ничего сложного.

*Примечание*: Регионы Украины и Беларусии еще не заполнены...просим прощения, мы не смогли в георгафию :P Дополним в ближайшее вреееемя.
 Но не расстраивайтесь, Вы ведь можете заполнить их сами, просто используйте кнопку "создать".

Перейдем к управлению текстами. Что тут у нас...?

<img src="https://raw.githubusercontent.com/joomx/mycityselector/free/doc_images/image-6.jpg" alt="" />

эммм...ну тут собственно пусто. Самое время что-нибудь создать. Предположим (совершенно точно), нам нужно для разных
городов отображать разные адреса и контакты.
Создадим новый текст с названием "Контакты". В качестве текста "по умолчанию" укажем адрес для
основного города. А для остальных городов необходимо воспользоваться
кнопкой "Добавить поле". В добавленное поле вбиваем желаемый город и вводим для него
текст (адрес).

<img src="https://raw.githubusercontent.com/joomx/mycityselector/free/doc_images/image-7.jpg" alt="" />

## Специальные маркеры

Как было сказано выше имеются дополнительные маркеры, позволяющие выводить название города в title или meta тегах.
Вот их полный перечень:

 - {cityName} => Именительный (Омск)
 - {cityGenitive} => Родительный (Омска)
 - {cityDative} => Дательный (Омску)
 - {cityAccusative} => Винительный (Омск)
 - {cityAblative} => Творительный (Омском)
 - {cityPrepositional} => Предложный (Омске)
 
 (аналогично для страны и региона)
 
 - {provinceName}, {countryName}
 - {provinceGenitive}, {countryGenitive}
 - {provinceDative}, {countryDative}
 - {provinceAccusative}, {countryAccusative}
 - {provinceAblative}, {countryAblative}
 - {provincePrepositional}, {countryPrepositional}

Эти маркеры можно использовать как в шаблоне так и в полях ввода при редактировании контента.

## Helper для интеграций

Расширение содержит глобальный класс McsData который который подключается плагином в самом начале инициализации Joomla,
поэтому к его методам можно обращаться из сторонних расширений.

Вот перечень данных, которые вы можете получить в своем коде:

```
// use activeprogramming\mcs\plugin\helpers\McsData; - старый namespace!
use joomx\mcs\plugin\helpers\McsData;

McsData::isBaseUrl(); // (true|false) является ли текущий хост базовым доменом
McsData::findCityByName($name); // (array|null) ищет город в базе по название (Омск, Владивосток) и возвращает в виде массива
McsData::findCityByCode($code); // (array|false) ищет город в базе по коду (совпадает с названием поддомена: omsk)
McsData::findProvinceByCode($code); // (array|false) ищет область в базе по коду
McsData::findCountryByCode($code); // (array|false) ищет страну в базе по коду
McsData::findLocationByCode($code); // (array|false) ищет локацию (страну, область, город) в базе по коду
McsData::findLocationByDomain($domain); // (array|false) ищет локацию (страну, область, город) по домену

McsData::get('isCitySelected'); // (true|false) делал ли пользователь выбор города?
McsData::get('moduleId'); // идентификатор модуля расширения в базе Joomla
McsData::get('basedomain'); // домен указанный в настройках расширения
McsData::get('cityId'); // ID текущего города в базе
McsData::get('city'); // код текущего города в базе (omsk)
McsData::get('cityName'); // название текущего города в базе (Омск)

McsData::get('provinceName'); // название текущей области в базе (Омская область)
McsData::get('countryName'); // название текущей страны
McsData::get('locationName'); // название текущей локации (если выбран город, то название города; если выбрана область, то название области)
McsData::getCurrentLocation(); // возвращет массив с текущими страной, областью, городом
McsData::get('default_city'); // город по умолчанию (указанный в настройках расширения)

// Склонения (город, регион, страна)
McsData::get('cityGenitive');
McsData::get('cityDative');
McsData::get('provinceGenitive');
McsData::get('countryPrepositional');

// Новое!
// Получение текста для текущего города прямо в коде
$content = McsContentHelper::getMcsTagValue($textID);
// пример:
$content = McsContentHelper::getMcsTagValue('[mcs-43]');
// или
$content = McsContentHelper::getMcsTagValue('mcs-43');
// или
$content = McsContentHelper::getMcsTagValue(43);

```
 
 Названия аналогичны спец маркерам склонений.

Формат вызова get метода: ```McsData::get($paramName, $defaultValue = null);```

## Замена маркеров в собственных скриптах

Иногда бывает необходимость в создании отдельных скриптов, которые не относятся к фронтенду. Например генерация файлов xml или других задач. В этом случае Вам потребуется вызвать плагин напрямую и передать ему текст содержащий метки для замены.
Вот пример кода:

```
<?php
// инициализация ядра Joomla
define('_JEXEC', 1);
define('JPATH_BASE', dirname(__FILE__));
// ReMark: предполагается, что этот скрипт в корне сайта
require_once(JPATH_BASE . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'defines.php');
require_once(JPATH_BASE . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'framework.php');
$app = JFactory::getApplication('site');
$app->initialise();

// TODO тут ваш код в котором Вы делаете что-то очень важное

$content = '<div>[mcs-1 Phone]</div>'; // ваш контент с метками который Вы сгенерировали выше

// запуск плагина для замены меток
JPluginHelper::importPlugin('system');
if (method_exists($app, 'setBody')) {
	$app->setBody($content); // Joomla 3.x
} else {
	JResponse::setBody($content); // joomla 2.5
}
$dispatcher = JDispatcher::getInstance();
$dispatcher->trigger('onAfterRender');
$content = (method_exists($app, 'getBody')) ? $app->getBody() : JResponse::getBody();

echo $content;
```


## Кастомизация внешнего вида

В текущей 3-й версии код модуля был написан как vueJs компонент (нам казалось, это удачная идея, да).
Таким образом, если Вам очень нужно поменять верстку компонента - то придется пересобрать его на сервере.
Сам компонент находится в директории
```/modules/mod_mycityselector/tmpl/webpack/mcs-modal/src```
На сервере должен быть установлен nodejs, чтобы вы могли сделать новый build.

```
$ cd modules/mod_mycityselector/tmpl/webpack/mcs-modal
$ npm i
$ npm run build
```

Не забудьте сбросить кеш в браузере.

Но мы рекомендуем, просто переопределять стили компонента в своих css файлах. Это гораздо проще.


*Если вам кажется, что некоторые моменты можно бы было описать лучше или где-то закралась неточность, то можете написать мне об этом на почту или сделать pull request.*

## Robots.txt и Sitemap.xml

Если используется вариант с поддоменами, то необходимо подменять имя хоста в файлах robots.txt и sitemap.xml.
Чтобы это настроить, нужно добавить несколько строк в конфигурацию web сервера.

Для Apache в файле ".htaccess" добавьте строку

```
RewriteEngine On # эту строку только если такой директивы в htaccess еще нет
RewriteRule ^robots.txt$ /components/com_mycityselector/robots.txt.php [QSA,L]
RewriteRule ^sitemap(.*).xml$ /components/com_mycityselector/sitemap.xml.php [QSA,L]
```

Для Nginx:

```
server {

     ... other instructions ...

     location = /robots.txt {
         rewrite ^(.*)$ /components/com_mycityselector/robots.txt.php last;
     }
     location ~ ^(.*)sitemap(.*)\.xml$ {
         rewrite ^(.*)$ /components/com_mycityselector/sitemap.xml.php last;
     }
}
```

## Между прочим

Cпасибо всем, кто помогал в тестировании и/или делал пожертвования.
