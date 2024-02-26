<?php
/**
 * MyCitySelector
 * @author  Konstantin Kutsevalov
 * @version 2.0.0
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

use joomx\mcs\plugin\helpers\McsData;

class MycityselectorModelFields extends Joomla\CMS\MVC\Model\ListModel
{

	/**
	 * Table name
	 * @var string
	 */
	private $table = '#__mycityselector_field';


	/**
	 * For Input object link
	 * @var null
	 */
	private $input = null;

	public $_forms;

	/**
	 * Init
	 */
	function __construct($config = [])
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = ['id', 'name', 'published', 'country', 'province', 'city'];
		}
		parent::__construct($config);
		$this->input = Joomla\CMS\Factory::getApplication()->input;
	}


	/**
	 * @return JDatabaseQuery
	 */
	protected function getListQuery()
	{
        $langId = McsData::getLangId();
        $isDbRepl = false;

        if (isset($_GET['db_replacement'])) {
            if (McsData::get('experimental_mode', 0) == '1') {
                $isDbRepl = true;
            }
        }

		$query = $this->_db->getQuery(true);
        $query->select('a.id AS id, a.name AS name, a.published AS published, GROUP_CONCAT(DISTINCT dn.name) AS city, GROUP_CONCAT(DISTINCT en.name) AS province, GROUP_CONCAT(DISTINCT fn.name) AS country')
            ->from($this->table . ' as a');

//        if ($isDbRepl) {
//            $query->where('a.is_db_replacement = 1');
//        } else {
//            $query->where('(a.is_db_replacement <> 1 OR a.is_db_replacement IS NULL)');
//        }

		$query->leftJoin('#__mycityselector_field_value AS b ON b.field_id = a.id');

		$query->leftJoin('#__mycityselector_value_city AS c ON c.field_value_id = b.id');

		$query->leftJoin('#__mycityselector_cities AS d ON d.id = c.city_id')
              ->leftJoin("`#__mycityselector_city_names` AS `dn` ON `dn`.`city_id` = `d`.`id` AND `dn`.`lang_id` = {$langId}");

        $query->leftJoin('#__mycityselector_value_province AS value_province ON value_province.field_value_id = b.id');

        $query->leftJoin('#__mycityselector_provinces AS e ON e.id = value_province.province_id')
              ->leftJoin("`#__mycityselector_province_names` AS `en` ON `en`.`province_id` = `e`.`id` AND `en`.`lang_id` = {$langId}");

        $query->leftJoin('#__mycityselector_value_country AS value_country ON value_country.field_value_id = b.id');

		$query->leftJoin('#__mycityselector_countries AS f ON f.id = value_country.country_id')
              ->leftJoin("`#__mycityselector_country_names` AS `fn` ON `fn`.`country_id` = `f`.`id` AND `fn`.`lang_id` = {$langId}");

		// Filter by search in title or note or id:.
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where($this->_db->quoteName('id') . ' = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $this->_db->quote('%' . strtolower($search) . '%');
				$query->where('(LOWER(a.name) LIKE ' . $search . ')');
			}
		} 

//		 Filter by country
		$country = $this->getState('filter.country');
		if (!empty($country))
		{
			$query->where('d.country_id = ' . $this->_db->escape($country));
		}

//		 Filter by province
		$province = $this->getState('filter.province');
		if (!empty($province))
		{
			$query->where('d.province_id = ' . $this->_db->escape($province));
		}

//		 Filter by publish
		$published = $this->getState('filter.published');
		if (!empty($published))
		{
			$query->where('a.published = ' . $this->_db->escape($published));
		}

		$query->group('a.id');

		$query->order($this->_db->qn($this->_db->escape($this->getState('list.ordering', 'name'))) . ' ' . $this->_db->escape($this->getState('list.direction', 'ASC')));

		return $query;
	}


	protected function populateState($ordering = 'name', $direction = 'ASC')
	{
		parent::populateState($ordering, $direction);
	}

}