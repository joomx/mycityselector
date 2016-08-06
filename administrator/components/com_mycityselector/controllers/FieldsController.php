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

class FieldsController extends JxController
{

    /**
     * @inheritdoc
     */
    public function assets()
    {
        return [
            ['css', 'url' => 'components/com_mycityselector/assets/admin-style.css'],
            ['css', 'url' => 'components/com_mycityselector/assets/select2.min.css'],
            ['js', 'url' => 'components/com_mycityselector/assets/select2.min.js', 'defer' => true],
            ['js', 'url' => 'components/com_mycityselector/assets/admin-scripts.js', 'defer' => true],
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
        JToolBarHelper::title(JText::_('COM_MYCITYSELECTOR_NAME'), 'big-ico');
        JToolBarHelper::addNew();
        JToolBarHelper::publishList();
        JToolBarHelper::unpublishList();
        //TODO Добавить проверку прав доступа
//        if ($canDo->get('core.admin') || $canDo->get('core.options'))
//        {
        JToolbarHelper::preferences('com_mycityselector');
//            JToolbarHelper::divider();
//        }
        JToolBarHelper::custom('drop', 'delete', 'delete', JText::_('COM_MYCITYSELECTOR_ITEM_DELETE'));
        $model = $this->getModel('fields');
        // sorting
        $this->setStateFromRequest('order_by', $model->filter_fields);
        $listOrder = $this->getState('order_by', 'name');
        $this->setStateFromRequest('order_direction', ['asc', 'desc']);
        $listDirection = $this->getState('order_direction', 'asc');
        $model->setOrder($listOrder, $listDirection);
        $this->render('list', [
            'items' => $model->getItems(),
            'pagination' => $model->getPagination(),
            'listOrder' => $listOrder,
            'listDirection' => $listDirection
        ]);
    }


    /**
     * List of items for popup window from editor
     */
    public function actionPopup()
    {
        $total = 0;
        $isSearch = isset($_GET['query']);
        $model = $this->getModel('fields');
        $items = $model->searchItems(@$_GET['query'], $total);
        $html = $this->render('list_popup', [
            'isSearch' => $isSearch,
            'items' => $items,
            'pagination' => $model->getPagination($total, false),
        ], $isSearch);
        if ($isSearch) {
            exit(json_encode(['status' => '200', 'html' => $html]));
        }
    }


    /**
     * Add new item
     */
    public function actionAdd()
    {
        $model = $this->getModel('fields');
        /* @var $model fieldsModel */
        $data = $model->getDefaultRecordValues();
        $data['fieldValues'] = [
            [
                'id' => 0,
                'value' => '',
                'default' => 1,
                'cities' => []
            ]
        ];
        JToolBarHelper::title(JText::_('COM_MYCITYSELECTOR_NAME') . ' - ' . JText::_('COM_MYCITYSELECTOR_ITEM_ADDING'), 'big-ico');
        JToolBarHelper::apply('save');
        JToolBarHelper::save('saveandclose');
        JToolBarHelper::save2new('saveandnew');
        JToolBarHelper::cancel('index');
        $this->render('edit', [
            'model' => $model,
            'data' => $data,
        ]);
    }


    /**
     * Render form for additional cities
     */
    public function actionGetForm()
    {
        $model = $this->getModel('fields');
        $this->render('form', [
            'model' => $model,
            'data' => [
                'id' => 0,
                'value' => '',
                'default' => 0,
                'cities' => ''
            ],
        ]);
        $doc = JFactory::getDocument();
        echo '<script>' . $doc->_script['text/javascript'] . '</script>';
    }


    /**
     * Edit form for item
     */
    public function actionUpdate()
    {
        $model = $this->getModel('fields');
        /* @var $model fieldsModel */
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
            JToolBarHelper::cancel('index');
            $this->render('edit', [
                'model' => $model,
                'data' => $data,
            ]);
        } else {
            JToolBarHelper::addNew();
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
        $page = 0;
        $url = '';
        $model = $this->getModel('fields');
        $id = $model->saveItem($_POST);
        if (!empty($model->getLastError())) {
            // error
            $this->setMessage($model->getLastError(), 'error');
            // TODO need to keep data and return its to form anyway for "create" action
            if ($id > 0) {
                $url .= '&task=update&id=' . $id;
            } else {
                $url .= '&task=add';
            }
            $this->redirect('index.php?option=' . $this->_component . '&controller=' . $this->_id . $url);
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
        $this->setMessage(JText::_('COM_MYCITYSELECTOR_FORM_SAVED'), 'message'); // TODO change message!
        $this->redirect('index.php?option=' . $this->_component . '&controller=' . $this->_id . $url);
    }


    /**
     * Drop field
     */
    public function actionDrop()
    {
        $page = $this->input->getCmd('page', 0);
        $fields = $this->getModel('fields');
        // drop fields
        $fields->dropItems($_POST['cid']);
        $this->setMessage(JText::_('COM_MYCITYSELECTOR_MESSAGE_DELETED'));

        $this->redirect('index.php?option=' . $this->_component . '&controller=' . $this->_id . '&page=' . $page);
    }


    /**
     * Publish items
     */
    public function actionPublish()
    {
        $page = $this->input->getCmd('page', 0);
        $model = $this->getModel('fields');
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
        $model = $this->getModel('fields');
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
            /* @var $model fieldsModel */
            $model = $this->getModel('fields');
            $listOrder = $this->getState('order_by', 'name');
            $listDirection = $this->getState('order_direction', 'asc');
            $model->setOrder($listOrder, $listDirection);
            $model->saveOrdering($order);
        }
        exit(json_encode($responce));
    }


    /**
     *
     */
    public function actionAutocomplete()
    {
        $result = [];
        $model = $this->getModel('city');
        $cities = $model->searchItemsByName(@$_GET['q']);
        if (!empty($cities)) {
            foreach ($cities as $city) {
                $result[] = [
                    'id' => $city['id'],
                    'name' => $city['name'],
                    'province_name' => $city['province_name'],
                    'country_name' => $city['country_name'],
                ];
            }
        }
        exit(json_encode(['results' => $result]));
    }


    /**
     * @throws Exception
     */
    public function actionDeleteFieldValue()
    {
        $model = $this->getModel('fields');
        $id = JFactory::getApplication()->input->get('id');
        $model->deleteFieldValue($id);
        exit(json_encode(['status' => '200']));
    }

}