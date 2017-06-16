Для использования закодированных и защищенных с помощью ionCube PHP-файлов  требуется чтобы на вашем веб-сервере был установлен загрузчик ionCube.

Возможна автоматическая установка с помощью мастера установки.
Вы можете скачать его на странице (Loader Installer: ZIP):
[https://www.ioncube.com/loaders.php](https://www.ioncube.com/loaders.php)

Или по прямой ссылке (для Windows):
[https://www.ioncube.com/loader-wizard/loader-installer.zip](https://www.ioncube.com/loader-wizard/loader-installer.zip)

Возможна автоматическая установка с помощью мастера установки. Вы можете скачать его на странице (Loader Installer: ZIP):
[https://www.ioncube.com/loaders.php](https://www.ioncube.com/loaders.php)
Или по прямой ссылке (для Windows):
[https://www.ioncube.com/loader-wizard/loader-installer.zip](https://www.ioncube.com/loader-wizard/loader-installer.zip)

Распакуйте и запустите файл из архива, следуйте дальнейшим указаниями мастера.

Если вы разбираетесь в настройке вашего сервера, вы можете сделать это самостоятельно:
1. Скачайте пакет с модулем для вашей операционной системы на странице [https://www.ioncube.com/loaders.php](https://www.ioncube.com/loaders.php) (раздел Loader Downloads) и разархивируйте его.
2. Затем вам нужно узнать версию PHP установленную на вашем сервере, для этого зайдите в административную панель Joomla, нажмите `Система -> Информация о системе`. На этой странице найдите запись (пример):

    Версия PHP 7.0.20-2~ubuntu16.04.1+deb.sury.org+1
Обратите внимание на первые две цифры после PHP - это номер вашей версии, в данном примере 7.0
На этой же странице перейдите во вкладку `"Информация о PHP"` и найдите строку Thread Safety disabled (или Thread Safety enabled), запомните это значение.
3. Перейдите в папку куда вы распаковали модуль, вам нужно выбрать файл согласно вашей версии PHP и режиму Thread Safety. Если параметр Thread Safety включен (enabled), то вам нужен файл, оканчивающийся на _ts.so
Скопируйте этот файл на ваш сервер в папку, например `/usr/lib/php/`
4. Найдите где располагается ваш конфигурационный файл на сервере, для этого на странице "Информация о PHP" найдите строку Loaded Configuration File, рядом будет указано расположение файла настроек, например `/etc/php/7.0/fpm/php.ini`, откройте его любым редактором, например 

    nano /etc/php/7.0/fpm/php.ini
и добавьте в самом конце строку 

    zend_extension = /usr/lib/php/имя файла загрузчика
например:

    zend_extension = /usr/lib/php/ioncube_loader_lin_7.0.so
*в указанном примере библиотека для версии PHP 7.0 ОС Linux*

Сохраните файл настроек, выйдите из редактора и перезапустите ваш веб сервер.

5. Теперь нужно проверить что модуль загружен и функционирует, для этого снова зайдите в административную панель Joomla, `Система -> Информация о системе -> Информация о PHP`, на странице вы должны найти информацию вида

    with the ionCube PHP Loader (enabled) + Intrusion Protection from ioncube24.com (unconfigured) v6.0.9, Copyright (c) 2002-2016, by ionCube Ltd.
*(версии могут отличаться)*


Вы всегда можете попросить нас о помощи в установке, если вы приобрели MyCitySelector на нашем сайте, оставьте заявку на установку через специальную форму запроса [https://act-prog.ru/product/mcs-extension](https://act-prog.ru/product/mcs-extension)

