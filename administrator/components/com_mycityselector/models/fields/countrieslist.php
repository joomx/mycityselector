<?php
defined('_JEXEC') or die;

use joomx\mcs\plugin\helpers\McsData;

Joomla\CMS\Form\FormHelper::loadFieldClass('list');

class JFormFieldCountriesList extends JFormFieldList
{

	protected $type = "CountriesList";
    protected $table = '#__mycityselector_countries';
    protected $tableNames = '#__mycityselector_country_names';

	public function getOptions()
	{
        $langId = McsData::getLangId();

		$db = Joomla\CMS\Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.id as id, n.name as name')
              ->from($this->table . ' as a')
              ->innerJoin("`{$this->tableNames}` AS `n` ON `a`.`id` = `n`.`country_id`")
              ->where("`n`.`lang_id` = {$langId}");
		$result = $db->setQuery($query)->loadAssocList();
		foreach ($result as $country)
		{
			$options[] = Joomla\CMS\HTML\HTMLHelper::_('select.option', $country['id'], $country['name']);
		}
		return array_merge(parent::getOptions(), $options);
	}
}