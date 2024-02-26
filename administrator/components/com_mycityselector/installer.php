<?php
/**
 * Скрипт установки/обновления пакета
 */
defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Installer\Adapter\PackageAdapter;

// @devnote Этот инсталлер используется не из компонента, а из установочного файла пакета, поэтому и префикс "pkg_"
class Pkg_mycityselectorInstallerScript {

    private $errors = [];

    private $logs = [];

    /**
     */
    function __construct($adapter)
    {
        // TODO Внимание!
        // Выполнение данного скрипта поисходит из папки tmp,
        // причем в этот момент там есть только текущий файл, файл манифеста и
        // архивы всех расширений пакета.
        // То есть никаких скриптов миграций там попросту нет:
        //
        // tmp/install_5b004fbb81f28/installer.php
        // tmp/install_5b004fbb81f28/license.txt
        // tmp/install_5b004fbb81f28/com_mycityselector_v300.zip
        // tmp/install_5b004fbb81f28/mcsinsert_v300.zip
        // tmp/install_5b004fbb81f28/mod_mycityselector_v300.zip
        // tmp/install_5b004fbb81f28/pkg_mycityselector.xml
        // tmp/install_5b004fbb81f28/plgmycityselector_v300.zip
    }


    /**
     * Called on installation
     * @return  boolean  True on success
     */
    function install($adapter)
    {
        include_once JPATH_ADMINISTRATOR . '/../plugins/system/plgmycityselector/compatibilities/include.php';
        // TODO Внимание
        // В момент вызова этого метода, все нужные файлы уже скопированы в Joomla
        // поэтому искать файлы миграций нужно в админке, в директории компонента
        $migrationsPath = realpath(JPATH_ADMINISTRATOR . '/components/com_mycityselector/migrations/');
        if (!class_exists('MigraPhp')) {
            require_once $migrationsPath . '/lib/migra.php';
        }
        // выполняем миграцию install_only
        $migra = new MigraPhp($migrationsPath);
        $migra->runMigration('install_only', 'apply');
        $migra->markAsAppliedMigration('3_0_0_1525116043_init'); // при чистой установке мы сразу помечаем миграцию для перехода с v2 на v3 как выполненную
        $errors = $migra->getErrors();
        if (!empty($errors)) {
            $this->errors = $migra->getErrors();
            $this->logs = $migra->getLogs();
        }

        // экспериментальные функции
        // - хуки для базы данных
// TODO эти хуки нужно переделывать для 4й джумлы
//        copy(JPATH_BASE . '/components/com_mycityselector/experimental/hooks/mysqlih.php', JPATH_BASE . '/../libraries/joomla/database/driver/mysqlih.php');
//        copy(JPATH_BASE . '/components/com_mycityselector/experimental/hooks/pdoh.php', JPATH_BASE . '/../libraries/joomla/database/driver/pdoh.php');

        return true;
    }


    /**
     * Called on uninstall
     * @return  boolean  True on success
     */
    function uninstall($adapter)
    {
        include_once JPATH_ADMINISTRATOR . '/../plugins/system/plgmycityselector/compatibilities/include.php';
        // TODO Внимание!
        // при удалении расширения, данный файл находится не в папке компонента (как в dev версии),
        // а в спец. директории /administrator/manifests/packages/mycityselector
        // Потому что этот скрипт инсталлера относится не к компоненту, а ко всему пакету
        $migrationsPath = realpath(JPATH_ADMINISTRATOR . '/components/com_mycityselector/migrations/');
        if (!class_exists('MigraPhp')) {
            require_once $migrationsPath . '/lib/migra.php';
        }
        // отменяем миграцию install_only
        $migra = new MigraPhp($migrationsPath);
        $migra->runMigration('install_only', 'revert');
        $errors = $migra->getErrors();
        if (!empty($errors)) {
            $this->errors = $migra->getErrors();
            $this->logs = $migra->getLogs();

            // TODO save to log file

        }
        return true;
    }


    /**
     * Called on update
     * @return  boolean  True on success
     */
    function update($adapter)
    {
        include_once JPATH_ADMINISTRATOR . '/../plugins/system/plgmycityselector/compatibilities/include.php';
        // TODO Внимание
        // В момент вызова этого метода, все нужные файлы уже скопированы в джумлу
        // поэтому искать файлы миграций нужно в админке, в директории компонента
        $migrationsPath = realpath(JPATH_ADMINISTRATOR . '/components/com_mycityselector/migrations/');
        // выполняем миграцию install_only
        if (!class_exists('MigraPhp')) {
            require_once $migrationsPath . '/lib/migra.php';
        }
        $migra = new MigraPhp($migrationsPath);
        $migra->createBackup(); // TODO
        $migra->applyMigrations();
        $errors = $migra->getErrors();
        if (!empty($errors)) {
            $this->errors = $migra->getErrors();
            $this->logs = $migra->getLogs();
        }

        // проверить наличие старого параметра subdomain_cities, записать его значение в seo_mode параметр и удалить
        $comp = ComponentHelper::getComponent('com_mycityselector');
        $params = $comp->params->toArray();
        if (!empty($params['subdomain_cities'])) {
            $params['seo_mode'] = $params['subdomain_cities'];
            unset($params['subdomain_cities']);
            $comp->setParams($params);
            $table = \JTable::getInstance('extension');
            $table->load($comp->id);
            $table->bind(['params' => $comp->params->toString()]);
            if ($table->check()) {
                $table->store();
            }
        }

        // экспериментальные функции
        // - хуки для базы данных
// TODO эти хуки нужно переделывать для 4й джумлы
//        copy(JPATH_BASE . '/components/com_mycityselector/experimental/hooks/mysqlih.php', JPATH_BASE . '/../libraries/joomla/database/driver/mysqlih.php');
//        copy(JPATH_BASE . '/components/com_mycityselector/experimental/hooks/pdoh.php', JPATH_BASE . '/../libraries/joomla/database/driver/pdoh.php');

        return true;
    }


    /**
     * Called before any type of action
     * @param   string  $route  Which action is happening (install|uninstall|discover_install|update)
     * @param   mixed  $adapter  The object responsible for running this script
     * @return  boolean  True on success
     */
    function preflight($route, $adapter)
    {
        return true;
    }


    /**
     * Здесь мы проверяем результат установки всех расширений пакета (в том числе таблицы)
     *
     * @param   string  $route  Which action is happening (install|uninstall|discover_install|update)
     * @param   mixed  $adapter  The object responsible for running this script
     * @return  boolean  True on success
     */
    function postflight($route, $adapter)
    {
        if ($route !== 'install' && $route !== 'update') {
            return true;
        }

        $dbo = Factory::getDBO();
        $version = $adapter->getManifest()->version->__toString();
        $adminComponent = JPATH_BASE.'/components/com_mycityselector/mycityselector.php';
        $siteComponent = JPATH_BASE.'/../components/com_mycityselector/mycityselector.php';
        $isInstallationOK = null;

        // данные для шаблона
        $data = [
            'github' => 'https://github.com/art-programming-team/mycityselector/issues',
            'css' => JUri::root() . '/administrator/components/com_mycityselector/installer.css',
            'components' > [],
            'modules' > [],
            'plugins' > [],
            'tables' > [],
        ];

        // получаем список всех необходимых таблиц и базовой миграции
        $migrationsPath = realpath(JPATH_ADMINISTRATOR . '/components/com_mycityselector/migrations/');
        if (!class_exists('MigraPhp')) {
            require_once $migrationsPath . '/lib/migra.php';
        }
        require_once $migrationsPath . '/install_only.php';
        $myTables = migration_install_only::tables();

        // TODO i18n

        $isInstallationOK = true;
        // Set the big value for plugin ordering and activate it
        // general plugin
        $qu = "UPDATE `#__extensions` SET `ordering`=9990, `enabled`=1 WHERE `element`='plgmycityselector' AND `type`='plugin'";
        $dbo->setQuery($qu)->execute();
        // editor button plugin
        $qu = "UPDATE `#__extensions` SET `ordering`=9991, `enabled`=1 WHERE `element`='mcsinsert' AND `type`='plugin'";
        $dbo->setQuery($qu)->execute();

        // добавим ссылку для обновления пакета
        $qu = "UPDATE `#__update_sites` SET `extra_query`='domain=' WHERE `name`='My City Selector Update Server'";
        $dbo->setQuery($qu)->execute();

        // здесь мы проверяем факт установки всех элементов пакеты
        // - компонент
        $result = true;
        if (!is_file($adminComponent) || !is_file($siteComponent)) {
            $result = false;
            $isInstallationOK = false;
        }
        $data['components'][] = ['name' => 'com_mycityselector', 'result' => $result];
        // - таблицы
        $tables = $dbo->setQuery("SHOW TABLES;")->loadColumn();
        foreach ($myTables as $tableName => $_columns) {
            $result = true;
            $table = $dbo->replacePrefix('#__' . $tableName);
            if (!in_array($table, $tables)) {
                $result = false;
                $isInstallationOK = false;
            }
            $data['tables'][] = ['name' => $table, 'result' => $result];
        }
        // - плагины
        $result = true;
        if (!is_file(JPATH_BASE.'/../plugins/system/plgmycityselector/plgmycityselector.php')) {
            $result = false;
            $isInstallationOK = false;
        }
        $data['plugins'][] = ['name' => 'plgmycityselector', 'result' => $result];
        $result = true;
        if (!is_file(JPATH_BASE.'/../plugins/editors-xtd/mcsinsert/mcsinsert.php')) {
            $result = false;
            $isInstallationOK = false;
        }
        $data['plugins'][] = ['name' => 'mcsinsert', 'result' => $result];
        // - модули
        $result = true;
        if (!is_file(JPATH_BASE.'/../modules/mod_mycityselector/mod_mycityselector.php')) {
            $result = false;
            $isInstallationOK = false;
        }
        $data['modules'][] = ['name' => 'mod_mycityselector', 'result' => $result];
        $result = true;
        if (!is_file(JPATH_BASE.'/modules/mod_mycityselector_admin_menu/mod_mycityselector_admin_menu.php')) {
            $result = false;
            $isInstallationOK = false;
        }
        $data['modules'][] = ['name' => 'mod_mycityselector_admin_menu', 'result' => $result];

        // если это процесс установки, то записываем в настройки компонента текущий домен как базовый. Потому что скорее всего
        // в админку зашли именно с базового домена
        $extensionId = null;
        $params = [];
        $host = @$_SERVER['HTTP_HOST'];

        $query = $dbo->getQuery(true);
        $query->select('extension_id, params')->from('#__extensions')
            ->where($dbo->quoteName('element') . ' = '. $dbo->quote('com_mycityselector'));

        $extensionResult = $dbo->setQuery($query)->loadAssocList();

        if(isset($extensionResult[0]) && !empty($extensionResult[0])) {
            $extensionId = $extensionResult[0]['extension_id'];
            $params = json_decode($extensionResult[0]['params'], true);
        }

        if (empty($params['basedomain']) && !empty($host) && !empty($extensionId)) {
            $params['basedomain'] = $host;
            $object = new stdClass();
            $object->extension_id = $extensionId;
            $object->params = json_encode($params);

            if ($dbo->updateObject('#__extensions', $object, 'extension_id')) {
                $data['base_domain'] = $host;
            }
        }

        $data['errors'] = $this->errors;
        $data['logs'] = $this->logs;

        // активируем меню в админке
        $query = "UPDATE `#__modules` SET `position` = 'menu', `ordering` = 1000, `published` = 1, `showtitle` = 1,"
            . " `params` = '{\"layout\":\"_:default\",\"module_tag\":\"div\",\"bootstrap_size\":\"0\",\"header_tag\":\"h3\",\"header_class\":\"\",\"style\":\"0\"}'"
            . " WHERE `module` LIKE 'mod_mycityselector_admin_menu'";
        $dbo->setQuery($query);
        $dbo->execute();
        $query = "UPDATE `#__extensions` SET `enabled` = '1' WHERE `element` = 'mod_mycityselector_admin_menu'";
        $dbo->setQuery($query);
        $dbo->execute();
        $dbo->setQuery("SELECT `id` FROM `#__modules` WHERE `module` = 'mod_mycityselector_admin_menu'");
        $id = (int) $dbo->loadResult();
        if ($id) {
            $dbo->setQuery("SELECT `moduleid` FROM `#__modules_menu` WHERE `moduleid` = {$id}");
            $moduleid = (int) $dbo->loadResult();
            if (!$moduleid) {
                try {
                    $obj = new stdClass();
                    $obj->moduleid = $id;
                    $obj->menuid = 0;
                    $dbo->insertObject('#__modules_menu', $obj);
                } catch(Exception $e) {
                    // ignore it
                }
            }
        }

        // template
        require JPATH_BASE . '/components/com_mycityselector/installer.tmpl.php';

        return true;
    }

}
