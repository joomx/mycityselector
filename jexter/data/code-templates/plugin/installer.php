<?php
/**
 * {jex_name}
 * @author {jex_author}
 * @version 1.0.0
 */

defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');


class Plg_JEX_GROUP__JEX_SYSNAME_InstallerScript {
	
	public function __construct($adapter)
    {
        //echo 'construct<br>';
    }

    /**
     * @see https://docs.joomla.org/J2.5:Managing_Component_Updates_(Script.php)
     * @param $route
     * @param $adapter
     * @return bool
     */
    public function preflight($route, $adapter)
    {
        //echo 'preflight<br>';
		return true;
	}


	public function postflight($route, $adapter)
    {
        //echo 'postflight<br>';
		return true;
	}


	public function install($adapter)
    {
        $email = '{jex_author_email}';
        // todo here you can create you unique "Hello page" for install page
        ?><div style="">
            <h3>{jex_name}</h3>
            <img src="http://art-prog.ru/share/icons/joomla/plugin-icon.png" alt="plugin-ico" style="float:left;margin-right:10px;"/>
            <div><b>Author:</b> {jex_author}<br></div>
            <?php if (!empty($email)) { ?>
                <div><b>E-mail:</b> <?= $email ?><br></div>
            <?php } ?>
        </div><?php
		return true;
	}


	public function update($adapter)
    {
        //echo 'update<br>';
		return true;
	}


	public function uninstall($adapter)
    {
        //echo 'uninstall<br>';
		return true;
	}

}