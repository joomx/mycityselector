<?php
defined('_JEXEC') or die;

use joomx\mcs\plugin\helpers\McsData;

Joomla\CMS\Form\FormHelper::loadFieldClass('list');

class JFormFieldProvinceList extends JFormFieldList
{

	protected $type = "ProvinceList";
    protected $table = '#__mycityselector_provinces';
    protected $tableNames = '#__mycityselector_province_names';

	public function getOptions()
	{
        $langId = McsData::getLangId();

		$viewName = Joomla\CMS\Factory::getApplication()->input->get('view', 'cities', 'string');
		$country_id = Joomla\CMS\Factory::getApplication()->getUserState('com_mycityselector.'.$viewName.'.filter.country');
		$db         = Joomla\CMS\Factory::getDbo();
		$query      = $db->getQuery(true);
        $query->select('a.id as id, n.name as name')
            ->from($this->table . ' as a')
            ->innerJoin("`{$this->tableNames}` AS `n` ON `a`.`id` = `n`.`province_id`")
            ->where("`n`.`lang_id` = {$langId}");

		if (!empty($country_id))
		{
			$query->where('country_id=' . $db->escape($country_id));
		}
		$result = $db->setQuery($query)->loadAssocList();

		foreach ($result as $province)
		{
			$options[] = Joomla\CMS\HTML\HTMLHelper::_('select.option', $province['id'], $province['name']);
		}

		return array_merge(parent::getOptions(), $options);
	}
}