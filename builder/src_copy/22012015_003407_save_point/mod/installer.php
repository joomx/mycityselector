<?php
/**
 * Скрипт установки
 *
 * @see https://docs.joomla.org/J3.x:Creating_a_simple_module/Adding_an_install-uninstall-update_script_file
 */
defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');


class mod_mycityselectorInstallerScript {


    /**
     * @param JInstallerModule $parent /libraries/cms/installer/adapter/module.php
     */
    function __construct($parent){ }


    /**
     * Method to install the extension
     * @param JInstallerModule $parent
     * @return boolean
     */
    function install($parent)
    {
        return true;
    }


    /**
     * Method to uninstall the extension
     * @param JInstallerModule $parent
     * @return boolean
     */
    function uninstall($parent)
    {
        return true;
    }


    /**
     * Method to update the extension
     * @param JInstallerModule $parent
     * @return boolean
     */
    function update($parent)
    {
        //echo '<p>The module has been updated to version' . $parent->get('manifest')->version) . '</p>';
        return true;
	}


    /**
     * Method to run before an install/update/uninstall method
     * @param JInstallerModule $parent
     * @param String $type is the type of change (install, update or discover_install)
     * @return boolean
     */
    function preflight($type, $parent)
    {
        return true;
    }


    /**
     * Method to run after an install/update/uninstall method
     * @param JInstallerModule $parent
     * @param String $type is the type of change (install, update or discover_install)
     * @return boolean
     */
    function postflight($type, $parent)
    {
        // присваиваем параметру "базовый домен" значение по умолчанию
        $host = $_SERVER['SERVER_NAME'];
        $db = JFactory::getDbo();
        $db->setQuery("SELECT `params` FROM `#__modules` WHERE `module`='mod_mycityselector'")->execute();
        $rows = $db->loadAssocList();
        if (count($rows) > 0) {
            $row = $rows[0];
            $params = new JRegistry();
            $params->loadString($row['params']);
            if (method_exists($params, 'setValue')) {
                $params->setValue('main_domain', $host);
            } else {
                $params->set('main_domain', $host);
            }
            $params = $db->quote($params->toString());
            $db->setQuery("UPDATE `#__modules` SET `params`={$params} WHERE `module`='mod_mycityselector'")->execute();
        }
        return true;
    }

}