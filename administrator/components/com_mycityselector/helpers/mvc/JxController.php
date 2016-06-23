<?php
/**
 * @author Konstantin Kutsevalov
 * @email <mail@art-prog.ru>
 */

namespace adamasantares\jxmvc;

if (class_exists('\adamasantares\jxmvc\JxController')) return;

/**
 * Controller of Component
 */
class JxController {

    /**
     * @var string Sidebar menu html
     */
    protected $sidebarMenu = null;

    /**
     * @var string Component's root path
     */
    protected $root = '';

    /**
     * @var string controller id
     */
    protected $_id = 'jx-controller';

    /**
     * @var string action
     */
    protected $_action = 'index';

    /**
     * @var string Component's name
     */
    protected $_component = 'com_name';

    /**
     * @var string JInput
     */
    protected $input;

    /**
     * @var string JInput
     */
    protected $db;

    /**
     * @var \JApplicationWeb
     */
    protected $app;


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


    public function __construct($root, $componentName)
    {
        $this->root = $root;
        $this->_id = empty($_REQUEST['controller']) ? 'default' : $_REQUEST['controller'];
        $this->_action = empty($_REQUEST['task']) ? 'index' : $_REQUEST['task'];
        $this->_component = $componentName;
        $this->input = \JFactory::getApplication()->input;
        $this->db = \JFactory::getDbo();
        $this->app = \JFactory::getApplication();
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
        foreach ($this->sidebarMenuItems() as $action => $name) {
            \JHtmlSidebar::addEntry($name, 'index.php?option=' . $componentName . '&task=default&controller=' . $action, ($this->_id==$action));
        }
        $this->sidebarMenu = \JHtmlSidebar::render();
    }

    /**
     * Returns state value by name
     * @param $name
     * @param $default
     * @return mixed
     */
    public function getState($name, $default = null)
    {
        $key = get_class($this) . '_';
        $session = \JFactory::getSession();
        return $session->get($key . $name, $default);
    }

    /**
     * Sets state value by name
     * @param $name
     * @param $value
     */
    public function setState($name, $value)
    {
        $key = get_class($this) . '_';
        $session = \JFactory::getSession();
        $session->set($key . $name, $value);
    }

    /**
     * Sets state value by name from request
     * @param $name
     * @param array $filter Enabled filters names
     * @return mixed
     */
    public function setStateFromRequest($name, $filter = [])
    {
        $key = get_class($this) . '_';
        if (!empty($_REQUEST[$name])) {
            if (!(!empty($filter) && !in_array($_REQUEST[$name], $filter))) {
                $session = \JFactory::getSession();
                $session->set($key . $name, $_REQUEST[$name]);
            }
        }
    }


    /**
     * Returns component name
     */
    public function getComponentName()
    {
        return $this->_component;
    }


    /**
     * Sets message to User's state
     * @param $message
     * @param string $status
     */
    public function setMessage($message, $status = '')
    {
        $app = \JFactory::getApplication();
        $app->setUserState($this->_component . '_message', $message);
        $app->setUserState($this->_component . '_status', $status);
    }

    /**
     * Returns message from User's state
     * @return string
     */
    public function getMessage()
    {
        $app = \JFactory::getApplication();
        $message = $app->getUserState($this->_component . '_message');
        $status = $app->getUserState($this->_component . '_status');
        $this->setMessage('');
        if (!empty($message)) {
            return \JFactory::getApplication()->enqueueMessage($message, $status);
        }
        return '';
    }

    /**
     * Returns model
     * @param string $name Model name
     * @param mixed $config Model's config (argument for constructor)
     * @throws \Exception
     */
    public function getModel($name, $config = null)
    {
        $path = $this->root . '/models/' . $name . '.php';
        if (is_file($path)) {
            require_once($path);
            $className = ucfirst(strtolower($name)) . 'Model';
            if (class_exists($className)) {
                $model = new $className($config);
                return $model;
            }
            throw new \Exception("Model '{$className}' not found.");
        }
        throw new \Exception("Model '{$name}' not found.");
    }

    /**
     * Execute action
     * @throws \Exception
     */
    public function execute()
    {
        if (empty($this->_action)) {
            $this->_action = 'index';
        }
        $method = 'action' . ucfirst(strtolower($this->_action));
        if (method_exists($this, $method)) {
            $this->$method();
        } else if (method_exists($this, 'actionIndex')) {
            $this->actionIndex();
        } else {
            throw new \Exception("Action '{$method}' not found");
        }
    }

    /**
     * Renders view
     * @param string $viewName
     * @param array $variables
     * @throws \Exception
     */
    public function render($viewName, $variables = [])
    {
        $viewFile = $this->root . '/views/' . $this->_id . '/' . $viewName . '.php';
        if (!is_file($viewFile)) {
            throw new \Exception("View '{$viewName}' not found");
        }
        $view = new JxView($this);
        $variables['sidebar'] = $this->sidebarMenu; // add sidebar
        $view->render($viewFile, $variables);
    }

    /**
     * Does redirect
     */
    public function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }

}