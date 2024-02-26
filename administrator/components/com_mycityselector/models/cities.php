<?php
/**
 * MyCitySelector
 * @author  Konstantin Kutsevalov
 * @version 2.0.0
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

use joomx\mcs\plugin\helpers\McsData;

class MycityselectorModelCities extends Joomla\CMS\MVC\Model\ListModel
{

	/**
	 * Table name
	 * @var string
	 */
	private $table = '#__mycityselector_cities';

	private $tableNames = '#__mycityselector_city_names';


	/**
	 * For Input object link
	 * @var null
	 */
	private $input = null;

	public $_forms;

	private $lang_id = 0;

	/**
	 * Init
	 */
	function __construct($config = [])
	{
	    // TODO put lang_id to $lang_id
//        $lang = Joomla\CMS\Factory::getLanguage();
//        $lang->getTag();

		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = ['ordering', 'id', 'name', 'code', 'published', 'country', 'province'];
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

        $db    = $this->getDbo();
		$query = $this->_db->getQuery(true);
		$query->select("`t`.*, `n`.`name` AS `name`, `c`.`name` AS `country`, `p`.`name` AS `province`")
			->from($this->table. ' as t')
            ->innerJoin("`{$this->tableNames}` AS `n` ON `n`.`city_id` = `t`.`id`")
            ->innerJoin("`#__mycityselector_country_names` AS `c` ON `c`.`country_id` = `t`.`country_id`")
            ->innerJoin("`#__mycityselector_province_names` AS `p` ON `p`.`province_id` = `t`.`province_id`")
            ->where("`n`.`lang_id` = {$langId} AND `c`.`lang_id` = {$langId} AND `p`.`lang_id` = {$langId}");

		// Filter by search in title or note or id:.
		$search = $this->getState('filter.search');

		if ( !empty($search) ) {
			if (stripos($search, 'id:') === 0) {
				$query->where($db->quoteName('id') . ' = ' . (int) substr($search, 3));
			} else {
				$search = $db->quote('%' . strtolower($search) . '%');
				$query->where("(LOWER(`n`.name) LIKE {$search})");
			}
		}

		// Filter by country
		$country = $this->getState('filter.country');
		if ( !empty($country) ) {
			$query->where('t.country_id = ' . $db->escape($country));
		}

		// Filter by province
		$province = $this->getState('filter.province');
		if ( !empty($province) ) {
			$query->where('t.province_id = ' . $db->escape($province));
		}

		// Filter by publish
		// Filter by province
		$published = $this->getState('filter.published');
		if ( !empty($published) ) {
			$query->where('t.published = ' . $db->escape($published));
		}
		$query->order($db->qn($db->escape($this->getState('list.ordering', 'name'))) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

		return $query;
	}


	protected function populateState($ordering = 'ordering', $direction = 'ASC')
	{
		parent::populateState($ordering, $direction);
	}

}