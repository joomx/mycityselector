<?php
/**
 * MyCitySelector
 * @author Konstantin Kutsevalov
 * @version 1.0.0
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

require_once __DIR__ . '/../helpers/form/formHelper.php';
require_once __DIR__ . '/../helpers/mvc/JxController.php';
require_once __DIR__ . '/../helpers/mvc/JxView.php';

use adamasantares\jxmvc\JxController;
use adamasantares\jxmvc\JxView;

class CityController extends JxController {


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
            'region' => JText::_('COM_MYCITYSELECTOR_REGIONS'),
            'city' => JText::_('COM_MYCITYSELECTOR_CITIES'),
        ];
        if (JFactory::getConfig()->get('debug') == 1) {
            $sidebar['dev'] = 'DEV TOOLS';
        }
        return $sidebar;
    }


    /**
     * List of items
     */
    public function actionIndex()
    {
        JToolBarHelper::title(JText::_('COM_MYCITYSELECTOR_NAME'), 'big-ico');
        JToolBarHelper::addNew();
        JToolBarHelper::publishList();
        JToolBarHelper::unpublishList();
        JToolBarHelper::custom('drop', 'delete', 'delete', JText::_('COM_MYCITYSELECTOR_ITEM_DELETE'));
        $regionId = intval($this->input->getCmd('region_id'));
        $model = $this->getModel('city', ['region_id' => $regionId]);	// (./models/[$modelName].php)
        $this->render('list', [
            'items' => $model->getItems(),
            'pagination' => $model->getPagination(),
            'listOrder' => $this->input->getCmd('list.ordering', ''),
            'listDirection' => $this->input->getCmd('list.direction', '')
        ]);
	}


    /**
     * Add new item
     */
    public function actionAdd()
    {
        JToolBarHelper::title(JText::_('COM_MYCITYSELECTOR_NAME') . ' - ' . JText::_('COM_MYCITYSELECTOR_ITEM_ADDING'), 'big-ico');
        JToolBarHelper::apply('save');
        JToolBarHelper::save('saveandclose');
        JToolBarHelper::save2new('saveandnew');
        JToolBarHelper::cancel('default');

        $model = $this->getModel('city');	// (./models/[$modelName].php)
        $data = [];
        foreach ($model->getFields() as $name => $coloumn) {
            $data[$name] = '';
            if (!empty($coloumn['default'])) {
                $data[$name] = $coloumn['default'];
            } else {
                if (in_array($coloumn['type'], ['int', 'bigint', 'tinyint', 'float', 'double'])) {
                    $data[$name] = '0';
                }
            }
        }
        $this->render('edit', [
            'data' => $data
        ]);
	}


    /**
     * Edit form for item
     * @param   boolean  $cache   If true, the view output will be cached
     * @param   array    $urlParams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     */
    public function actionUpdate()
    {
		$model = $this->getModel('city');
        //$view = $this->getView('city', 'html');
        //$view->setModel($model, true);
        //$view->sidebar = $this->sidebar;
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
            //$view->setLayout('edit');
            //$data = $data;
            $this->render('edit', [
                'model' => $model,
                'data' => $data,
            ]);


        } else {
            JToolBarHelper::addNew();
            //$view->setLayout('not_found');
            $this->render('not_found', []);
        }
        //$view->display();
	}


    /**
     * Save an item and return to form
     */
    public function actionSave()
    {
        $this->save('update');
    }


    /**
     * Save an item and return to list items
     */
    public function actionSaveAndClose()
    {
        $this->save();
    }


    /**
     * Save an item and create new item (redirect)
     */
    public function actionSaveAndNew()
    {
        $this->save('add');
    }


    /**
     * Save item
     */
    private function save($redirectTo = 'index')
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
                    $url .= '&task=' . $redirectTo;
                    break;
                case 'update':
                    $url .= '&task=' . $redirectTo . '&id=' . $id;
                    break;
                default:
                    $url .= '&page=' . $page;
            }
        }
        $this->setMessage(JText::_('COM_MYCITYSELECTOR_FORM_SAVED'), 'message');
        $this->redirect('index.php?option=' . $this->_component . '&controller=' . $this->_id . $url);
    }


    /**
     * Drop items
     */
    public function actionDrop()
    {
        $page = $this->input->getCmd('page', 0);
		$model	= $this->getModel('city');
        if (!empty($_POST['cid'])) {
            $model->dropItems($_POST['cid']);
        }
        $this->redirect('index.php?option=' . $this->_component . '&controller='.$this->_id.'&page=' . $page);
	}


    /**
     * Publish items
     */
    public function actionPublish()
    {
        $page = $this->input->getCmd('page', 0);
		$model	= $this->getModel('city');
        if (!empty($_POST['cid'])) {
            $model->publishItems($_POST['cid'], 1);
        }
        $this->redirect('index.php?option=' . $this->_component . '&controller='.$this->_id.'&page=' . $page);
	}


    /**
     * UnPublish items
     */
    public function actionUnPublish()
    {
        $page = $this->input->getCmd('page', 0);
		$model	= $this->getModel('city');
        if (!empty($_POST['cid'])) {
            $model->publishItems($_POST['cid'], 0);
        }
        $this->redirect('index.php?option=' . $this->_component . '&controller='.$this->_id.'&page=' . $page);
	}


    /**
     * Save new order of items
     */
    public function actionSaveOrder()
    {

        // todo

	}

	
}