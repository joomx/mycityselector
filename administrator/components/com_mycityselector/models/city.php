<?php
/**
 * MyCitySelector
 * @author Konstantin Kutsevalov
 * @version 2.0.0
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');


jimport('joomla.application.component.modellist');


class CityModel extends JModelList {

    /**
     * Table name
     * @var string
     */
    private $table = '#__mycityselector_city';

    /**
     * Prefix for fields names PREFIX[field_name]
     * @var string
     */
    private $fieldPrefix = 'City';

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
     * @var int
     */
    private $provinceId = 0;

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
            $config['filter_fields'] = ['ordering', 'id', 'name', 'status'];
        }
        if (!empty($config['province_id'])) {
            $this->provinceId = intval($config['province_id']);
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
     * @param int $provinceId
     * @param bool $limit
     * @return array
     */
    public function getItems($provinceId = null, $limit = true)
    {
        if (empty($provinceId)) {
            $provinceId = $this->provinceId;
        } else {
            $provinceId = intval($provinceId);
        }
		$page = intval($this->input->getCmd('page', '0'));
		$start = intval($this->pageLimit * $page);
        $query = $this->getListQuery();
        if ($provinceId != 0) {
            $query->where("province_id={$provinceId}");
        }
        $query->order($this->ordering . ' ' . $this->direction);
        if ($limit) {
            return $this->_db->setQuery($query, $start, $this->pageLimit)->loadAssocList();
        }
        return $this->_db->setQuery($query)->loadAssocList();
	}


    /**
     * Search items (records)
     * @param string $term
     * @return array
     */
    public function searchItemsByName($term)
    {
        if (empty($term) || mb_strlen($term, 'utf8') < 2) {
            return [];
        }
        $term = $this->_db->quote('%' . $term . '%');
        $query = "SELECT `city`.*, `prv`.`name` AS `province_name`, `cnt`.`name` AS `country_name` FROM `{$this->table}` `city` "
            . "LEFT JOIN `#__mycityselector_province` `prv` ON `prv`.`id` = `city`.`province_id` "
            . "LEFT JOIN `#__mycityselector_country` `cnt` ON `cnt`.`id` = `city`.`country_id` "
            . "WHERE `city`.`name` LIKE {$term} AND `city`.`status` = 1";
        return $this->_db->setQuery($query)->loadAssocList();
	}


    /**
     * Returns item by ID
     * @param int $id
     * @return array
     */
    public function getItem($id)
    {
        $id = $this->_db->escape($id);
        $query = $this->getListQuery();
        $query->where("`id`={$id}");
		return $this->_db->setQuery($query)->loadAssoc();
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
        // get all keys with "City[value]"
        $prefix = $this->fieldPrefix;
        $id = 0;
        if (!empty($data['City'])) {
            $pairFieldValue = $fields = $values = [];
            foreach ($data['City'] as $param => $value) {
                if ($param == 'id') {
                    $id = intval($value);
                    continue;
                }
                $pairFieldValue[] = $this->_db->quoteName($param) . '=' . $this->_db->quote($value);
                $fields[] = $this->_db->quoteName($param);
                $values[] = $this->_db->quoteName($value);
            }
            // check item
            $isExists = $this->_db->setQuery("SELECT count(`id`) FROM `{$this->table}` WHERE `id`={$id}")->execute();
            if ($isExists->num_rows == 0) {
                // create
                $maxOrder = $this->_db->setQuery("SELECT max(`ordering`) FROM `{$this->table}`")->loadRow();
                $fields[] = 'ordering';
                $values[] = empty($maxOrder[0]) ? 1 : $maxOrder[0] + 1;
                $query = $this->_db->getQuery(true)->insert($this->table)->columns($fields)->values($values);
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
     * Drop (remove) selected cities
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
     * Drop cities by province id
     * @param $keys
     */
    public function dropByProvinces($keys)
    {
        if (!is_array($keys)) {
            $keys = [$keys];
        }
        foreach ($keys as $i => $key) {
            $keys[$i] = intval($key);
        }
        $keys = implode(',', $keys);
        $this->_db->setQuery("DELETE FROM `{$this->table}` WHERE `province_id` IN ({$keys})")->execute();
    }


    /**
     * @param int $provinceId
     * @return string (JPagination)
     */
    public function getPagination($provinceId = null)
    {
        if (empty($provinceId)) {
            $provinceId = $this->provinceId;
        } else {
            $provinceId = intval($provinceId);
        }
        $html = '';
        $page = intval($this->input->getCmd('page', 0));
        $this->_db->setQuery("SELECT COUNT(*) AS `val` FROM `{$this->table}`".($provinceId == 0 ? '' : " WHERE `province_id` = {$provinceId}"));
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