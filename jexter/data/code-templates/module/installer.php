<?php
/**
 * Pre-install script
 *
 * @see https://docs.joomla.org/J3.x:Creating_a_simple_module/Adding_an_install-uninstall-update_script_file
 */
defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');


class _NEWMOD_InstallerScript {


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
        return true;
    }

}