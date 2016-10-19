<?php
/**
 * Скрипт установки
 *
 */
defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');

// @devnote Этот инсталлер используется не из компонента, а из установочного файла пакета, поэтому и префикс "pkg_"
class Pkg_mycityselectorInstallerScript {


    /**
     * @param   JAdapterInstance  $adapter  The object responsible for running this script
     */
    function __construct(JAdapterInstance $adapter){
    }


    /**
     * Called on installation
     * @param   JAdapterInstance  $adapter  The object responsible for running this script
     * @return  boolean  True on success
     */
    function install(JAdapterInstance $adapter)
    {
        return true;
    }


    /**
     * Called on uninstall
     * @param   JAdapterInstance  $adapter  The object responsible for running this script
     * @return  boolean  True on success
     */
    function uninstall(JAdapterInstance $adapter)
    {
        return true;
    }


    /**
     * Called on update
     * @param   JAdapterInstance  $adapter  The object responsible for running this script
     * @return  boolean  True on success
     */
    function update(JAdapterInstance $adapter)
    {
        //echo '<p>The module has been updated to version' . $parent->get('manifest')->version) . '</p>';
        return true;
    }


    /**
     * Called before any type of action
     * @param   string  $route  Which action is happening (install|uninstall|discover_install|update)
     * @param   JAdapterInstance  $adapter  The object responsible for running this script
     * @return  boolean  True on success
     */
    function preflight($route, JAdapterInstance $adapter)
    {
        return true;
    }


    /**
     * Called after any type of action
     * @param   string  $route  Which action is happening (install|uninstall|discover_install|update)
     * @param   JAdapterInstance  $adapter  The object responsible for running this script
     * @return  boolean  True on success
     */
    function postflight($route, JAdapterInstance $adapter)
    {
        $version = $adapter->getManifest()->version->__toString();
        $green = 'style="color: #3c763d;"';
        $red = 'style="color: #ff2b2b;"';
        $icon = 'style="color: #e68300"';
        // TODO i18n
        echo '<h3>My City Selector ' . $version . '</h3>';
        if ($route == 'install' || $route == 'update') {
            $success = true;
            $github = 'https://github.com/art-programming-team/mycityselector/issues';
            // Set the big value for plugin ordering and activate it
            // general plugin
            $qu = "UPDATE `#__extensions` SET `ordering`=9990, `enabled`=1 WHERE `element`='plgmycityselector' AND `type`='plugin'";
            JFactory::getDBO()->setQuery($qu)->execute();
            // editor button plugin
            $qu = "UPDATE `#__extensions` SET `ordering`=9991, `enabled`=1 WHERE `element`='mcsinsert' AND `type`='plugin'";
            JFactory::getDBO()->setQuery($qu)->execute();

            // здесь мы проверяем факт установки всех элементов пакеты
            echo '<strong>Компонент</strong>';
            $result = $green . '><span class="icon icon-checkmark-2"></span>';
            if (!is_file(JPATH_BASE.'/components/com_mycityselector/mycityselector.php') || !is_file(JPATH_BASE.'/../components/com_mycityselector/mycityselector.php')) {
                $result = $red . '><span class="icon icon-not-ok"></span>';
                $success = false;
            }
            echo '<p><span class="icon icon-cube" ' . $icon . '></span> com_mycityselector: <em ' . $result . '</em></p>';
            echo '<strong>Таблицы</strong>';
            $tables = JFactory::getDBO()->setQuery("SHOW TABLES;")->loadColumn();
            $myTables = ['mycityselector_country', 'mycityselector_province', 'mycityselector_city', 'mycityselector_field',
                'mycityselector_field_value', 'mycityselector_value_city'];
            $prefix = JFactory::getConfig()->get('dbprefix');
            foreach ($myTables as $table) {
                $result = $green . '><span class="icon icon-checkmark-2"></span>';
                if (!in_array($prefix.$table, $tables)) {
                    $success = false;
                    $result = $red . '><span class="icon icon-not-ok"></span>';
                }
                echo '<p><span class="icon icon-database" ' . $icon . '></span> ' . $prefix.$table . ': <em ' . $result . '</em></p>';
            }
            echo '<strong>Плагины</strong>';
            $result = $green . '><span class="icon icon-checkmark-2"></span>';
            if (!is_file(JPATH_BASE.'/../plugins/system/plgmycityselector/plgmycityselector.php')) {
                $result = $red . '><span class="icon icon-not-ok"></span>';
            }
            echo '<p><span class="icon icon-puzzle" ' . $icon . '></span> plgmycityselector: <em ' . $result . '</em></p>';
            $result = $green . '><span class="icon icon-checkmark-2"></span>';
            if (!is_file(JPATH_BASE.'/../plugins/editors-xtd/mcsinsert/mcsinsert.php')) {
                $result = $red . '><span class="icon icon-not-ok"></span>';
            }
            echo '<p><span class="icon icon-puzzle" ' . $icon . '></span> mcsinsert: <em ' . $result . '</em></p>';
            echo '<strong>Модуль</strong>';
            $result = $green . '><span class="icon icon-checkmark-2"></span>';
            if (!is_file(JPATH_BASE.'/../modules/mod_mycityselector/mod_mycityselector.php')) {
                $result = $red . '><span class="icon icon-not-ok"></span>';
            }
            echo '<p><span class="icon icon-grid-view-2" ' . $icon . '></span> mod_mycityselector: <em ' . $result . '</em></p>';

            if ($success) {
                echo '<p ' . $green . '>Установка успешно завершена.</p>';
            } else {
                echo '<p>В процессе установки возникли <em ' . $red . '>ошибки</em> :(<br>'
                    . ' Придётся <a href="' . $github . '" target="_blank" style="text-decoration:underline">жаловаться</a> разработчикам...</p>';
            }
        }
        echo '<div style="height:10px"></div>';
        return true;
    }

}
