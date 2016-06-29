<?php
/**
 * Скрипт установки
 *
 */
defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');

// @devnote Этот инсталлер используется не из компонента, а из установочного файла пакета.
class pkg_mycityselectorInstallerScript {


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

        // здесь мы проверяем факт установки всех элементов пакеты
        // компонент, модуль, плагин

        ?>
        <h3>My City Selector</h3>
        <p>Установка вроде как завершена.</p>
        <?php

        // TODO настройки будут в отдельной таблице
        // присваиваем параметру "базовый домен" значение по умолчанию
//        $host = $_SERVER['SERVER_NAME'];
//        $db = JFactory::getDbo();
//        $db->setQuery("SELECT `params` FROM `#__modules` WHERE `module`='mod_mycityselector'")->execute();
//        $rows = $db->loadAssocList();
//        if (count($rows) > 0) {
//            $row = $rows[0];
//            $params = new JRegistry();
//            $params->loadString($row['params']);
//            if (method_exists($params, 'setValue')) {
//                $params->setValue('main_domain', $host);
//            } else {
//                $params->set('main_domain', $host);
//            }
//            $params = $db->quote($params->toString());
//            $db->setQuery("UPDATE `#__modules` SET `params`={$params} WHERE `module`='mod_mycityselector'")->execute();
//        }
        return true;
    }

}
