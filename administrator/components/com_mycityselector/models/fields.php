<?php
/**
 * MyCitySelector
 * @author Konstantin Kutsevalov
 * @version 2.0.0
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');


jimport('joomla.application.component.modellist');


/**
 * Class FieldsModel
 */
class FieldsModel extends JModelList
{

    /**
     * Table name
     * @var string
     */
    private $table = '#__mycityselector_field';

    private $table_fieldvalues = '#__mycityselector_field_value';

    private $table_valuecities = '#__mycityselector_value_city';

    /**
     * Prefix for fields names PREFIX[field_name]
     * @var string
     */
    private $fieldPrefix = 'Field';

    /**
     * Primary key of table
     * @var string
     */
    private $primaryKey = 'id';

    /**
     * Table's fields
     * @var array
     */
    private $fields = [];

    /**
     * For Input object link
     * @var null
     */
    private $input = null;

    /**
     * Limit of items on page
     * @var int
     */
    private $pageLimit = 20;

    /**
     * @var string
     */
    private $ordering = 'name';

    /**
     * @var string
     */
    private $direction = 'asc';

    /**
     * @var string
     */
    private $lastError = '';


    /**
     * Init
     */
    function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = ['ordering', 'id', 'name', 'subdomain', 'status'];
        }
        parent::__construct($config);
        $fields = $this->_db->getTableColumns($this->table, false);
        foreach ($fields as $field => $details) {
            $type = explode('(', $details->Type);
            $max = isset($type[1]) ? intval(trim($type[1], ')')) : 0;
            $this->fields[$field] = [
                'name' => $field,
                'primary' => ($details->Key == 'PRI' ? true : false),
                'type' => $type[0],
                'maxLength' => $max,
                'required' => ($details->Null == 'NO' ? true : false),
                'default' => $details->Default,
                'comment' => $details->Comment
            ];
        }
        $this->input = JFactory::getApplication()->input;
    }


    /**
     * Properties getter
     * @param $name
     * @return null
     */
    function __get($name)
    {
        if (!empty($this->$name)) {
            return $this->$name;
        }
        return null;
    }


    /**
     * @inheritdoc
     * @return null
     */
    public function getTable($name = '', $prefix = '', $options = [])
    {
        return null;
    }


    /**
     * Returns table name
     * @return string
     */
    public function getTableName()
    {
        return $this->table;
    }


    /**
     * Returns primary key
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }


    /**
     * Returns table's fields
     * @return string
     */
    public function getFields()
    {
        return $this->fields;
    }


    /**
     * Returns table's fields
     * @return string
     */
    public function getLastError()
    {
        return $this->lastError;
    }


    /**
     * Returns table's fields
     * @return string
     */
    public function getDefaultRecordValues()
    {
        $data = [];
        foreach ($this->fields as $key => $params) {
            if (isset($params['default'])) {
                $data[$params['name']] = $params['default'];
            } else {
                $data[$params['name']] = '';
            }
        }
        return $data;
    }


    /**
     * Returns field's name for input element
     * @param string $name
     * @param bool $nc no check field?
     * @return string
     */
    public function getFieldName($name, $nc=false)
    {
        if (isset($this->fields[$name]) || $nc) {
            return $this->fieldPrefix . '[' . $name . ']';
        }
        return $name;
    }


    /**
     * Returns validation rules
     * @return array
     */
    public function getValidateRules()
    {
        return [];
    }

    /**
     * Sets order and direction for sorting (before read items)
     * @param $field
     * @param $direction
     */
    public function setOrder($field, $direction)
    {
        $this->ordering = $field;
        $this->direction = $direction;
    }


    /**
     * Returns items (records)
     * @param bool $limit
     * @return array
     */
    public function getItems($limit = true)
    {
        $page = intval($this->input->getCmd('page', '0'));
        $start = intval($this->pageLimit * $page);
        $query = $this->getListQuery();
        $query->order($this->ordering . ' ' . $this->direction);
        if ($limit) {
            return $this->_db->setQuery($query, $start, $this->pageLimit)->loadAssocList();
        }
        return $this->_db->setQuery($query)->loadAssocList();
    }


    /**
     * Search
     * @param string $queryString
     * @param bool $total By ref
     * @return array
     */
    public function searchItems($queryString, &$total = 0)
    {
        $page = intval($this->input->getCmd('page', '0'));
        $start = intval($this->pageLimit * $page);
        $query = $this->getListQuery();
        $query->order($this->ordering . ' ' . $this->direction);
        $queryString = $this->_db->quote('%'.$queryString.'%');
        $query->where("`name` LIKE {$queryString}");
        $result = $this->_db->setQuery($query, $start, $this->pageLimit)->loadAssocList();
        // total count for pagination
        $total = $this->_db->setQuery("SELECT COUNT(*) AS `val` FROM `{$this->table}` WHERE `name` LIKE {$queryString}")
            ->loadResult();
        return $result;
    }


    /**
     * Returns item by ID
     * @param int $id
     * @return array
     */
    public function getItem($id = 0)
    {
        $query = $this->getListQuery();
        if ($id > 0) {
            $id = $this->_db->quote($id);
            $query->where("`id` = {$id}");
        } else {
            $query->order("`id` ASC");
        }
        // load item
        $data = $this->_db->setQuery($query, 0, 1)->loadAssoc();
        // load values
        $query = $this->_db->getQuery(true)->select("`id`,`value`,`default`")->from($this->table_fieldvalues)
            ->where("`field_id` = {$id}")->order("`default` DESC");
        $data['fieldValues'] = $this->_db->setQuery($query)->loadAssocList();
        foreach ($data['fieldValues'] as $k => $fieldValue) {
            $fid = $this->_db->quote($fieldValue['id']);
            $query = "SELECT `city`.`id`, `city`.`name` FROM `{$this->table_valuecities}` `t` "
                . "INNER JOIN `#__mycityselector_city` `city` ON `t`.`city_id` = `city`.`id` WHERE `t`.`fieldvalue_id` = {$fid}";
            $cities = $this->_db->setQuery($query)->loadAssocList();
            if (empty($cities)) {
                $cities = [];
            }
            $data['fieldValues'][$k]['cities'] = $cities;
        }
        return $data;
    }


    /**
     * @return JDatabaseQuery
     */
    protected function getListQuery()
    {
        $query = $this->_db->getQuery(true);
        $query->select('*')->from($this->table);
        return $query;
    }


    /**
     * Saves item's data
     * @param $data Array of fields and values pairs
     * @return int Id of record (return 0 on error)
     */
    public function saveItem($data)
    {
        // get all keys with "Prefix[value]"
        $prefix = $this->fieldPrefix;
        if (!empty($data[$prefix])) {
            $data = $data[$prefix];
            $id = intval($data['id']);
            if (empty($data['name'])) {
                $this->lastError = 'Название не может быть пустым'; // todo JText::_('COM_MYCITYSELECTOR_HELLO_SAVE_ERROR')
                return $id;
            }
            $name = $this->_db->quote($data['name']);
            $published = intval($data['published']);
            // check item
            $isExists = ($id > 0) ? $this->_db->setQuery("SELECT `id` FROM `{$this->table}` WHERE `id`={$id}")->execute() : false;
            if ($isExists === false || $isExists->num_rows == 0) {
                // create
                $result = $this->_db
                    ->setQuery("INSERT INTO `{$this->table}` (`name`, `published`) VALUES ({$name}, {$published})")
                    ->execute();
                if ($result) {
                    $id = $this->_db->insertid();
                    $this->saveFieldValues($id, $data);
                    return $id;
                }
            } else {
                // update
                $result = $this->_db
                    ->setQuery("UPDATE `{$this->table}` SET `name` = {$name}, `published` = {$published} WHERE `id` = {$id}")
                    ->execute();
                if ($result) {
                    $this->saveFieldValues($id, $data);
                    return $id;
                }
            }
        }
        return 0;
    }


    /**
     * Сохраняет контент
     * @param int $fieldId
     * @param array $data
     */
    private function saveFieldValues($fieldId, $data)
    {
        foreach ($data['value'] as $key => $value) {
            $cities = isset($data['cities'][$key]) ? $data['cities'][$key] : [];
            if (substr($key, 0, 1) == '_') {
                // create
                if (empty($value) && empty($cities)) continue;
                $default = (substr($key, -3, 3) == 'DEF') ? '1' : '0';
                // - value
                $value = $this->_db->quote($value);
                $result = $this->_db
                    ->setQuery("INSERT INTO `{$this->table_fieldvalues}` (`field_id`,`value`,`default`) "
                        . "VALUES ({$fieldId}, {$value}, {$default})")->execute();
                // - cities
                if ($result) {
                    $id = $this->_db->insertid();
                    if (!empty($cities)) {
                        foreach ($cities as $cityId) {
                            $cityId = $this->_db->quote($cityId);
                            $this->_db->setQuery("INSERT INTO `{$this->table_valuecities}` (`fieldvalue_id`,`city_id`) "
                                . "VALUES ({$id}, {$cityId})")->execute();
                        }
                    }
                }
            } else {
                // update
                $id = $this->_db->quote($key);
                // - value
                $value = $this->_db->quote($value);
                $this->_db->setQuery("UPDATE `{$this->table_fieldvalues}` SET `value` = {$value} WHERE `id` = {$id}")
                    ->execute();
                // - cities
                $this->_db->setQuery("DELETE FROM `{$this->table_valuecities}` WHERE `fieldvalue_id` = {$id}")->execute(); // remove all links
                if (!empty($cities)) {
                    foreach ($cities as $cityId) {
                        $cityId = $this->_db->quote($cityId);
                        $this->_db->setQuery("INSERT INTO `{$this->table_valuecities}` (`fieldvalue_id`,`city_id`) "
                            . "VALUES ({$id}, {$cityId})")->execute();
                    }
                }
            }
        }
    }


    /**
     * Publish/unPublish selected items
     * @param array $keys
     * @param string|int $status
     */
    public function publishItems($keys, $status = '1')
    {
        $status = $this->_db->escape($status);
        foreach ($keys as $i => $key) {
            $keys[$i] = intval($key);
        }
        $keys = implode(',', $keys);
        $this->_db->setQuery("UPDATE `{$this->table}` SET `status`='{$status}' WHERE `id` IN ({$keys})")->execute();
    }


    /**
     * Drop (remove) selected items
     * @param $keys
     */
    public function dropItems($keys)
    {
        if (!is_array($keys)) {
            $keys = [$keys];
        }
        foreach ($keys as $i => $key) {
            $keys[$i] = intval($key);
            $this->deleteFieldValue(intval($key));
        }
        $keys = implode(',', $keys);
        $this->_db->setQuery("DELETE FROM `{$this->table}` WHERE `id` IN ({$keys})")->execute();
    }


    /**
     * @return string (JPagination)
     */
    public function getPagination($count = null, $showTotal = true)
    {
        $html = '';
        $page = intval($this->input->getCmd('page', 0));
        if (empty($count)) {
            $this->_db->setQuery("SELECT COUNT(*) AS `val` FROM `{$this->table}`");
            $count = $this->_db->loadResult();
        }
        if ($count > 0) {
            $url = $_SERVER['REQUEST_URI'];
            if (strpos($url, '?') === false) {
                $url .= '?';
            } else {
                $url = str_replace('&page=' . $page, '', $url) . '&';
            }
            $pages = intval($count / $this->pageLimit);
            if ($count % $this->pageLimit > 0) {
                $pages++;
            }
            for ($i = 0; $i < $pages; $i++) {
                if ($page == $i) {
                    $html .= '<b><a href="' . $url . 'page=' . $i . '">' . ($i + 1) . '</a></b> &nbsp;';
                } else {
                    $html .= '<a href="' . $url . 'page=' . $i . '">' . ($i + 1) . '</a> &nbsp;';
                }
            }
            if ($showTotal) {
                $html .= '<br/>Total ' . $count . ' items';
            }
            $html .= '<input type="hidden" name="page" value="' . $page . '"/>';
        }
        return $html;
    }


    /**
     * Saves new ordering values
     * @param array $keys [id => order, id => order, ...]
     */
    public function saveOrdering($keys)
    {
        // @devnote все записи должны иметь не нулевое значение в поле ordering (при создании записи устанавливается автоматически)
        // @devnote Ключи приходят в порядке их расположения в списке (измененный порядок)
        $ordering = array_values($keys);
        sort($ordering);
        if ($this->direction == 'desc') {
            $ordering = array_reverse($ordering);
        }
        $i = 0;
        foreach ($keys as $id => $v) {
            $id = intval($id);
            $orderNum = intval($ordering[$i]);
            if ($id > 0) {
                $this->_db->setQuery("UPDATE `{$this->table}` SET `ordering` = {$orderNum} WHERE `id` = {$id}")->execute();
            }
            $i++;
        }
    }
    public function deleteFieldValue($id) {
        $query = $this->_db->getQuery(true)->select('id')->from($this->table_fieldvalues)->where('id='.$this->_db->quote($id));
        $isExist = $this->_db->setQuery($query)->execute();
        if ($isExist === false || $isExist->num_rows == 0) {
            return true;
        } else {
            $query = $this->_db->getQuery(true)->delete($this->table_fieldvalues)->where('id='.$this->_db->quote($id));
            $this->_db->setQuery($query)->execute();
            $query = $this->_db->getQuery(true)->delete($this->table_valuecities)->where('fieldvalue_id='.$this->_db->quote($id));
            $this->_db->setQuery($query)->execute();
            return true;
        }
    }

}