<?php
/**
 * @author Konstantin Kutsevalov
 * @email <mail@art-prog.ru>
 */

namespace adamasantares\jxmvc;

jimport('joomla.application.component.controller');

/**
 * Wrapper for Joomla Controller of Component
 */
class JxController extends \JControllerLegacy {

    /**
     * @var string Sidebar menu html
     */
    protected $sidebarMenu = null;


    /**
     * Sidebar menu items
     * You can redeclare it in own controller
     */
    public function sidebarMenuItems()
    {
        return [
            // '{task_name}' => '{title}'
            // 'default' => 'Items'
        ];
    }

    /**
     * Assets URLs
     * You can redeclare it in own controller
     */
    public function assets()
    {
        return [
            // example
            // ['css', 'url' => 'components/com_hello/style.css', 'type' => 'text/css', 'media' => 'screen'],
            // ['js', 'url' => 'components/com_hello/script.js', 'type' => 'text/javascript', 'defer' => true, 'async' => false],
        ];
    }


    public function __construct()
    {
        parent::__construct();
        // registering actions
        $this->registerTask('default', 'actionIndex');
        foreach(get_class_methods($this) as $method) {
            if (substr($method, 0, 6) == 'action') {
                $task = strtolower(str_replace('action', '', $method));
                $this->registerTask($task, $method);
            }
        }
        // assets
        $document = \JFactory::getDocument();
        $css = ['type' => 'text/css', 'media' => null];
        $js = ['type' => 'text/javascript', 'defer' => true, 'async' => false];
        foreach ($this->assets() as $data) {
            if (!empty($data[0]) && !empty($data['url'])) {
                // add base url
                if (substr($data['url'], 0, 4) != 'http' && substr($data['url'], 0, 4) != '/') {
                    $data['url'] = \JURI::base() . $data['url'];
                }
                if ($data[0] == 'css') {
                    $data = array_merge($css, $data);
                    $document->addStyleSheet($data['url'], $data['type'], $data['media']);
                } else if ($data[0] == 'js') {
                    $data = array_merge($js, $data);
                    $document->addScript($data['url'], $data['type'], $data['defer'], $data['async']);
                }
            }
        }
        // menu items for left sidebar
        foreach ($this->sidebarMenuItems() as $task => $name) {
            \JHtmlSidebar::addEntry($name, 'index.php?option=com_mycityselector&task=' . $task, ($this->task==$task));
        }
        $this->sidebarMenu = \JHtmlSidebar::render();
    }


}