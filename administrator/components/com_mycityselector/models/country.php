<?php
/**
 * MyCitySelector
 * @author Konstantin Kutsevalov
 * @version 2.0.0
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');


jimport('joomla.application.component.modellist');


class CountryModel extends JModelList {

    /**
     * Table name
     * @var string
     */
    private $table = '#__mycityselector_country';

    /**
     * Prefix for fields names PREFIX[field_name]
     * @var string
     */
    private $fieldPrefix = 'Country';

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
     * @return string
     */
    public function getFieldName($name)
    {
        if (isset($this->fields[$name])) {
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
        // TODO rules
        return [
        ];
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
     * Returns item by ID
     * @param int $id
     * @return array
     */
    public function getItem($id = 0)
    {
        $query = $this->getListQuery();
        if ($id > 0) {
            $id = $this->_db->escape($id);
            $query->where("`id`={$id}");
        } else {
            $query->order('`id` ASC');
        }
		return $this->_db->setQuery($query, 0, 1)->loadAssoc();
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
        $id = 0;
        if (!empty($data[$prefix])) {
            $pairFieldValue = $fields = $values = [];
            foreach ($data[$prefix] as $param => $value) {
                if ($param == 'id') {
                    $id = intval($value);
                    continue;
                }
                $pairFieldValue[] = $this->_db->quoteName($param) . '=' . $this->_db->quote($value);
                $fields[] = $this->_db->quoteName($param);
                $values[] = $this->_db->quote($value);
            }
            // check item
            $isExists = $id > 0 ? $this->_db->setQuery("SELECT count(`id`) FROM `{$this->table}` WHERE `id`={$id}")->execute() : false;
            if ($isExists === false || $isExists->num_rows == 0) {
                // create
                $maxOrder = $this->_db->setQuery("SELECT max(`ordering`) FROM `{$this->table}`")->loadRow();
                $fields[] = 'ordering';
                $values[] = empty($maxOrder[0]) ? 1 : $maxOrder[0] + 1;
                $query = $this->_db->getQuery(true)->insert($this->table)->columns($fields)->values(implode(',', $values));
                $result = $this->_db->setQuery($query)->execute();
                if ($result) {
                    return $this->_db->insertid();
                }
            } else {
                // update
                $query = $this->_db->getQuery(true)->update($this->table)->set($pairFieldValue)->where(['id = ' . $id]);
                $result = $this->_db->setQuery($query)->execute();
                if ($result) {
                    return $id;
                }
            }
        }
        return 0;
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
        }
        $keys = implode(',', $keys);
        $this->_db->setQuery("DELETE FROM `{$this->table}` WHERE `id` IN ({$keys})")->execute();
    }


    /**
     * @return string (JPagination)
     */
    public function getPagination()
    {
        $html = '';
        $page = intval($this->input->getCmd('page', 0));
        $this->_db->setQuery("SELECT COUNT(*) AS `val` FROM `{$this->table}`");
        $count = $this->_db->loadResult();
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
            for ($i=0; $i<$pages; $i++) {
                if ($page == $i) {
                    $html .= '<b><a href="' . $url . 'page=' . $i . '">' . ($i + 1) . '</a></b> &nbsp;';
                } else {
                    $html .= '<a href="' . $url . 'page=' . $i . '">' . ($i + 1) . '</a> &nbsp;';
                }
            }
            $html .= '<br/>Total ' . $count . ' items';
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
	
}