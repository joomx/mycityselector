<?php

defined('_JEXEC') or die;

use joomx\mcs\plugin\helpers\McsData;

Joomla\CMS\Form\FormHelper::loadFieldClass('list');

class JFormFieldCitiesList extends JFormFieldList
{

	protected $type = "CitiesList";
    protected $table = '#__mycityselector_cities';
    protected $tableNames = '#__mycityselector_city_names';

	public function getOptions()
	{
	    $app = Joomla\CMS\Factory::getApplication();
		$country_id =  $app->getUserState('com_mycityselector.fields.filter.country');
		$province_id =  $app->getUserState('com_mycityselector.fields.filter.province');
        $langId = McsData::getLangId();
		$db =  Joomla\CMS\Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.id as id, n.name as name')
            ->from($this->table . ' as a')
            ->innerJoin("`{$this->tableNames}` AS `n` ON `a`.`id` = `n`.`city_id`")
            ->where("`n`.`lang_id` = {$langId}");


		$query->order('name');
		if (!empty($country_id))
		{
			$query->where('country_id=' . $db->escape($country_id));
		}
		if (!empty($province_id))
		{
			$query->where('province_id=' . $db->escape($province_id));
		}
		$result = $db->setQuery($query)->loadAssocList();
		$options = [];
		foreach ($result as $city)
		{
			$options[] = Joomla\CMS\HTML\HTMLHelper::_('select.option', $city['id'], $city['name']);
		}

		return array_merge(parent::getOptions(), $options);
	}
}