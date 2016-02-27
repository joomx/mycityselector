<?php
/**
 * MyCitySelector
 * @author Konstantin Kutsevalov
 * @version 1.0.0
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

require_once __DIR__ . '/helpers/form/formHelper.php';
require_once __DIR__ . '/helpers/mvc/JxController.php';

use adamasantares\jxmvc\JxController;


class DefaultController extends JxController {


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
        return [
            'default' => JText::_('COM_MYCITYSELECTOR_COUNTRIES'),
            //'country' => JText::_('COM_MYCITYSELECTOR_COUNTRIES'),
            'region' => JText::_('COM_MYCITYSELECTOR_REGIONS'),
            'city' => JText::_('COM_MYCITYSELECTOR_CITIES'),
        ];
    }


    /**
     * List of items
     * @param   boolean  $cache   If true, the view output will be cached
     * @param   array    $urlParams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     */
    public function actionIndex($cache = false, $urlParams = [])
    {
        JToolBarHelper::title(JText::_('COM_MYCITYSELECTOR_NAME'), 'big-ico');
        JToolBarHelper::addNew();
        JToolBarHelper::publishList();
        JToolBarHelper::unpublishList();
        JToolBarHelper::deleteList();
        JToolBarHelper::custom('drop', 'delete', 'delete', JText::_('COM_MYCITYSELECTOR_ITEM_DELETE'));

        $model	= $this->getModel('city');	// (./models/[$modelName].php)
        $this->render('list', [
            'items' => $model->getItems(),
            'pagination' => $model->getPagination(),
            'listOrder' => $this->escape($model->get('State')->get('list.ordering')),
            'listDirection' => $this->escape($model->get('State')->get('list.direction'))
        ]);
	}


    /**
     * Add new item
     * @param   boolean  $cache   If true, the view output will be cached
     * @param   array    $urlParams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     */
    public function actionAdd($cache = false, $urlParams = [])
    {
        JToolBarHelper::title(JText::_('COM_MYCITYSELECTOR_NAME') . ' - ' . JText::_('COM_MYCITYSELECTOR_ITEM_ADDING'), 'big-ico');
		$model = $this->getModel('city');
        $view = $this->getView('cities', 'html');
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
		$model = $this->getModel('city');
        $view = $this->getView('cities', 'html');
        $view->setModel($model, true);
        $view->sidebar = $this->sidebar;
        $id = intval($this->input->getCmd('id'));
        if (!empty($_POST['cid'])) {
            $id = intval($_POST['cid'][0]);
        }
        $data = $model->getItem($id);
        JToolBarHelper::title(JText::_('COM_MYCITYSELECTOR_NAME') . ' - ' . JText::_('COM_MYCITYSELECTOR_ITEM_EDITING') . ': ' . $data['name'], 'big-ico');
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
        $model = $this->getModel('city');
        $id = $model->saveItem($_POST);
        if (!$id) {
            // error
            JFactory::getApplication()->enqueueMessage(JText::_('COM_MYCITYSELECTOR_HELLO_SAVE_ERROR'), 'error');
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
        $this->setRedirect('index.php?option=com_mycityselector' . $url)->redirect();
    }


    /**
     * Drop items
     * @param   boolean  $cache   If true, the view output will be cached
     * @param   array    $urlParams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     */
    public function actionDrop($cache = false, $urlParams = [])
    {
        $page = $this->input->getCmd('page', 0);
		$model	= $this->getModel('city');
        if (!empty($_POST['cid'])) {
            $model->dropItems($_POST['cid']);
        }
        $this->setRedirect('index.php?option=com_mycityselector&page=' . $page)->redirect();
	}


    /**
     * Publish items
     * @param   boolean  $cache   If true, the view output will be cached
     * @param   array    $urlParams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     */
    public function actionPublish($cache = false, $urlParams = [])
    {
        $page = $this->input->getCmd('page', 0);
		$model	= $this->getModel('city');
        if (!empty($_POST['cid'])) {
            $model->publishItems($_POST['cid'], 1);
        }
        $this->setRedirect('index.php?option=com_mycityselector&page=' . $page)->redirect();
	}


    /**
     * UnPublish items
     * @param   boolean  $cache   If true, the view output will be cached
     * @param   array    $urlParams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     */
    public function actionUnPublish($cache = false, $urlParams = [])
    {
        $page = $this->input->getCmd('page', 0);
		$model	= $this->getModel('city');
        if (!empty($_POST['cid'])) {
            $model->publishItems($_POST['cid'], 0);
        }
        $this->setRedirect('index.php?option=com_mycityselector&page=' . $page)->redirect();
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