<?php
/**
 * MyCitySelector
 * @author Konstantin Kutsevalov
 * @version 2.0.0
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

require_once __DIR__ . '/../helpers/form/formHelper.php';
require_once __DIR__ . '/../helpers/mvc/JxController.php';
require_once __DIR__ . '/../helpers/mvc/JxView.php';

use adamasantares\jxmvc\JxController;
use adamasantares\jxmvc\JxView;

class DevController extends JxController {


    /**
     * @inheritdoc
     */
    public function assets()
    {
        return [
            ['css', 'url' => 'components/com_mycityselector/admin-style.css'],
            ['js', 'url' => 'components/com_mycityselector/admin-scripts.js', 'defer' => true],
        ];
    }


    /**
     * @inheritdoc
     */
    public function sidebarMenuItems()
    {
        $sidebar = [
            'default' => JText::_('COM_MYCITYSELECTOR_COUNTRIES'), //'country'
            'province' => JText::_('COM_MYCITYSELECTOR_PROVINCES'),
            'city' => JText::_('COM_MYCITYSELECTOR_CITIES'),
        ];
        if (JFactory::getConfig()->get('debug') == 1) {
            $sidebar['dev'] = 'DEV TOOLS';
        }
        return $sidebar;
    }


    private function getSubmenuItems()
    {
        return [
            'Isis Template Icons' => ['task' => 'isisicons'],
        ];
    }


    /**
     * Shows Debug submenu only
     */
    public function actionIndex()
    {
        $this->actionIsisIcons(); // by default shows icons
	}

    /**
     *
     */
    public function actionIsisIcons()
    {
        JToolBarHelper::title(JText::_('COM_MYCITYSELECTOR_NAME'), 'big-ico');
        $path = COM_MCS_PATH . '/../../templates/isis/css/template.css';
        $icons = [];
        if (is_file($path)) {
            $cssLines = file($path);
            foreach ($cssLines as $line) {
                $line = trim($line);
                if (substr($line, 0, 6) == '.icon-' && substr($line, -1, 1) == '{') {
                    $line = explode(':', $line)[0];
                    $line = str_replace([',', '{'], ['', ''], $line);
                    $icons[] = substr($line, 1);
                }
            }
        } else {
            $this->setMessage('Шаблон Isis не найден', 'error');
        }
        $this->render('isis_icons', [
            'subMenu' => '',
            'icons' => $icons,
        ]);
    }

	
}