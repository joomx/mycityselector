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
require_once 'DefaultController.php';

use adamasantares\jxmvc\JxController;
use adamasantares\jxmvc\JxView;

class CityController extends JxController
{


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
            'fields' => JText::_('COM_MYCITYSELECTOR_FIELDS')
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
        JToolbarHelper::title(JText::_('COM_MYCITYSELECTOR_NAME'), 'big-ico');
        JToolbarHelper::addNew();
        JToolbarHelper::publishList();
        JToolbarHelper::unpublishList();
        JToolbarHelper::custom('drop', 'delete', 'delete', JText::_('COM_MYCITYSELECTOR_ITEM_DELETE'));
        $provinceId = intval($this->input->getCmd('province_id'));
        $model = $this->getModel('city', ['province_id' => $provinceId]);
        /* @var $model CityModel */    // (./models/[$modelName].php)
        // sorting
        $this->setStateFromRequest('order_by', $model->filter_fields);
        $listOrder = $this->getState('order_by', 'name');
        $this->setStateFromRequest('order_direction', ['asc', 'desc']);
        $listDirection = $this->getState('order_direction', 'asc');
        $model->setOrder($listOrder, $listDirection);
        // country & province names
        $province = $this->getModel('province')->getItem($provinceId);
        if (empty($province)) {
            return $this->render('not_found');
        }
        $provinceName = $province['name'];
        $country = $this->getModel('country')->getItem($province['country_id']);
        if (empty($country)) {
            return $this->render('not_found');
        }
        $countryName = $country['name'];

        $this->render('list', [
            'items' => $model->getItems(),
            'pagination' => $model->getPagination(),
            'listOrder' => $listOrder,
            'listDirection' => $listDirection,
            'countryName' => $countryName,
            'provinceName' => $provinceName,
        ]);
    }


    /**
     * Add new item
     */
    public function actionAdd()
    {
        JToolbarHelper::title(JText::_('COM_MYCITYSELECTOR_NAME') . ' - ' . JText::_('COM_MYCITYSELECTOR_ITEM_ADDING'), 'big-ico');
        JToolbarHelper::apply('save');
        JToolbarHelper::save('saveandclose');
        JToolbarHelper::save2new('saveandnew');
        JToolbarHelper::cancel('default');

        $model = $this->getModel('city');
        /* @var $model CityModel */
        $data = [];
        $data['countries'] = $model->getCountries();
        $data['provinces'] = $model->getProvinces();
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
            'model' => $model,
            'data' => $data
        ]);
    }


    /**
     * Edit form for item
     */
    public function actionUpdate()
    {
        $model = $this->getModel('city');
        /* @var $model CityModel */
        $id = intval($this->input->getCmd('id'));
        if (!empty($_POST['cid'])) {
            $id = intval($_POST['cid'][0]);
        }
        $data = $model->getItem($id);
        $data['countries'] = $model->getCountries();
        $data['provinces'] = $model->getProvinces();
        JToolbarHelper::title(JText::_('COM_MYCITYSELECTOR_NAME') . ' - ' . JText::_('COM_MYCITYSELECTOR_ITEM_EDITING') . ': ' . $data['name'], 'big-ico');
        if (!empty($data)) {
            JToolbarHelper::apply('save');
            JToolbarHelper::save('saveandclose');
            JToolbarHelper::save2new('saveandnew');
            JToolbarHelper::cancel('default');
            $this->render('edit', [
                'model' => $model,
                'data' => $data,
            ]);
        } else {
            JToolbarHelper::addNew();
            //$view->setLayout('not_found');
            $this->render('not_found', []);
        }
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
        /* @var $model CityModel */
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
        $model = $this->getModel('city');
        /* @var $model CityModel */
        if (!empty($_POST['cid'])) {
            $model->dropItems($_POST['cid']);
        }
        $this->redirect('index.php?option=' . $this->_component . '&controller=' . $this->_id . '&page=' . $page);
    }


    /**
     * Publish items
     */
    public function actionPublish()
    {
        $page = $this->input->getCmd('page', 0);
        $model = $this->getModel('city');
        /* @var $model CityModel */
        if (!empty($_POST['cid'])) {
            $model->publishItems($_POST['cid'], 1);
        }
        $this->redirect('index.php?option=' . $this->_component . '&controller=' . $this->_id . '&page=' . $page);
    }


    /**
     * UnPublish items
     */
    public function actionUnPublish()
    {
        $page = $this->input->getCmd('page', 0);
        $model = $this->getModel('city');
        /* @var $model CityModel */
        if (!empty($_POST['cid'])) {
            $model->publishItems($_POST['cid'], 0);
        }
        $this->redirect('index.php?option=' . $this->_component . '&controller=' . $this->_id . '&page=' . $page);
    }


    /**
     * Save new order of items
     */
    public function actionSaveOrdering()
    {
        $responce = ['status' => '200', 'debug_get' => $_GET];
        $order = empty($_GET['order']) ? [] : $_GET['order'];
        if (!empty($order)) {
            /* @var $model CityModel */
            $model = $this->getModel('city');
            $listOrder = $this->getState('order_by', 'name');
            $listDirection = $this->getState('order_direction', 'asc');
            $model->setOrder($listOrder, $listDirection);
            $model->saveOrdering($order);
        }
        exit(json_encode($responce));
    }

}