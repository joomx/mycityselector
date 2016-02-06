<?php
/**
 * {jex_name}
 * @author {jex_author}
 * @version 1.0.0
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

jimport('joomla.application.component.controller');
require_once __DIR__ . '/helpers/form/formHelper.php';


class _JEX_CLASSNAME_Controller extends JControllerLegacy {

    /**
     * @var string Sidebar html code
     */
    private $sidebar = '';


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
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::base().'components/{jex_sysname}/admin-style.css');
		$document->addScript(JURI::base().'components/{jex_sysname}/admin-scripts.js', 'text/javascript', true);
        // menu items for left sidebar
		$links = array(
            'default' => JText::_('T_JEX_ITEMS'),
            // 'other_item' => 'Title',
            // ...
        );
		foreach ($links as $task => $name) {
            JHtmlSidebar::addEntry($name, 'index.php?option={jex_sysname}&task=' . $task, ($this->task==$task));
		}
        $this->sidebar = JHtmlSidebar::render();
    }


    /**
     * List of items
     * @param   boolean  $cache   If true, the view output will be cached
     * @param   array    $urlParams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     */
    public function actionIndex($cache = false, $urlParams = [])
    {
        JToolBarHelper::title(JText::_('T_JEX_NAME'), 'big-ico');
        JToolBarHelper::addNew();
        JToolBarHelper::publishList();
        JToolBarHelper::unpublishList();
        JToolBarHelper::deleteList();
        JToolBarHelper::custom('drop', 'delete', 'delete', JText::_('T_JEX_ITEM_DELETE'));
		$model	= $this->getModel('{jex_item_model}');	// (./models/[$modelName].php)
        $view = $this->getView('{jex_items_view}', 'html');	// view logic (./views/[$viewName]/view.html.php)
        $view->setModel($model, true);
        $view->items = $model->getItems();
        $view->pagination = $model->getPagination();
		$view->setLayout('list');	// view template (./views/[$viewName]/tmpl/[$tmplName].php)
        $view->sidebar = $this->sidebar;
		$view->display();
	}


    /**
     * Add new item
     * @param   boolean  $cache   If true, the view output will be cached
     * @param   array    $urlParams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     */
    public function actionAdd($cache = false, $urlParams = [])
    {
        JToolBarHelper::title(JText::_('T_JEX_NAME') . ' - ' . JText::_('T_JEX_ITEM_ADDING'), 'big-ico');
		$model = $this->getModel('{jex_item_model}');
        $view = $this->getView('{jex_items_view}', 'html');
        $view->setModel($model, true);
        $view->sidebar = $this->sidebar;
        JToolBarHelper::apply('save');
        JToolBarHelper::save('saveandclose');
        JToolBarHelper::save2new('saveandnew');
        JToolBarHelper::cancel('default');
        $view->setLayout('edit');
        $view->data = [];
        foreach ($model->getFields() as $name => $coloumn) {
            $view->data[$name] = '';
            if (!empty($coloumn['default'])) {
                $view->data[$name] = $coloumn['default'];
            } else {
                if (in_array($coloumn['type'], ['int', 'bigint', 'tinyint', 'float', 'double'])) {
                    $view->data[$name] = '0';
                }
            }
        }
        $view->display();
	}


    /**
     * Edit form for item
     * @param   boolean  $cache   If true, the view output will be cached
     * @param   array    $urlParams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     */
    public function actionUpdate($cache = false, $urlParams = [])
    {
		$model = $this->getModel('{jex_item_model}');
        $view = $this->getView('{jex_items_view}', 'html');
        $view->setModel($model, true);
        $view->sidebar = $this->sidebar;
        $id = intval($this->input->getCmd('id'));
        if (!empty($_POST['cid'])) {
            $id = intval($_POST['cid'][0]);
        }
        $data = $model->getItem($id);
        JToolBarHelper::title(JText::_('T_JEX_NAME') . ' - ' . JText::_('T_JEX_ITEM_EDITING') . ': ' . $data['name'], 'big-ico');
        if (!empty($data)) {
            JToolBarHelper::apply('save');
            JToolBarHelper::save('saveandclose');
            JToolBarHelper::save2new('saveandnew');
            JToolBarHelper::cancel('default');
            $view->setLayout('edit');
            $view->data = $data;

            // todo create form

        } else {
            JToolBarHelper::addNew();
            $view->setLayout('not_found');
        }
        $view->display();
	}


    /**
     * Save an item and return to form
     * @param   boolean  $cache   If true, the view output will be cached
     * @param   array    $urlParams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     */
    public function actionSave($cache = false, $urlParams = [])
    {
        $this->save($urlParams, 'update');
    }


    /**
     * Save an item and return to list items
     * @param   boolean  $cache   If true, the view output will be cached
     * @param   array    $urlParams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     */
    public function actionSaveAndClose($cache = false, $urlParams = [])
    {
        $this->save($urlParams);
    }


    /**
     * Save an item and create new item (redirect)
     * @param   boolean  $cache   If true, the view output will be cached
     * @param   array    $urlParams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     */
    public function actionSaveAndNew($cache = false, $urlParams = [])
    {
        $this->save($urlParams, 'add');
    }


    /**
     * Save item
     */
    private function save($urlParams, $redirectTo='default')
    {
        $page = 0; // TODO store current page in session
        $url = '';
        $model = $this->getModel('{jex_item_model}');
        $id = $model->saveItem($_POST);
        if (!$id) {
            // error
            JFactory::getApplication()->enqueueMessage(JText::_('T_JEX_HELLO_SAVE_ERROR'), 'error');
        } else {
            switch ($redirectTo) {
                case 'add':
                    $url .= '&taks=' . $redirectTo;
                    break;
                case 'update':
                    $url .= '&taks=' . $redirectTo . '&id=' . $id;
                    break;
                default:
                    $url .= '&page=' . $page;
            }
        }
        $this->setRedirect('index.php?option={jex_sysname}' . $url)->redirect();
    }


    /**
     * Drop items
     * @param   boolean  $cache   If true, the view output will be cached
     * @param   array    $urlParams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     */
    public function actionDrop($cache = false, $urlParams = [])
    {
        $page = $this->input->getCmd('page', 0);
		$model	= $this->getModel('{jex_item_model}');
        if (!empty($_POST['cid'])) {
            $model->dropItems($_POST['cid']);
        }
        $this->setRedirect('index.php?option={jex_sysname}&page=' . $page)->redirect();
	}


    /**
     * Publish items
     * @param   boolean  $cache   If true, the view output will be cached
     * @param   array    $urlParams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     */
    public function actionPublish($cache = false, $urlParams = [])
    {
        $page = $this->input->getCmd('page', 0);
		$model	= $this->getModel('{jex_item_model}');
        if (!empty($_POST['cid'])) {
            $model->publishItems($_POST['cid'], 1);
        }
        $this->setRedirect('index.php?option={jex_sysname}&page=' . $page)->redirect();
	}


    /**
     * UnPublish items
     * @param   boolean  $cache   If true, the view output will be cached
     * @param   array    $urlParams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     */
    public function actionUnPublish($cache = false, $urlParams = [])
    {
        $page = $this->input->getCmd('page', 0);
		$model	= $this->getModel('{jex_item_model}');
        if (!empty($_POST['cid'])) {
            $model->publishItems($_POST['cid'], 0);
        }
        $this->setRedirect('index.php?option={jex_sysname}&page=' . $page)->redirect();
	}


    /**
     * Save new order of items
     * @param   boolean  $cache   If true, the view output will be cached
     * @param   array    $urlParams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     */
    public function actionSaveOrder($cache = false, $urlParams = [])
    {

        // todo

	}

	
}