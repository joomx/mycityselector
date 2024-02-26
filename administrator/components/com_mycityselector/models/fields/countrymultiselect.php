<?php
defined('_JEXEC') or die;

use joomx\mcs\plugin\helpers\McsData;

Joomla\CMS\Form\FormHelper::loadFieldClass('list');

class JFormFieldCountrymultiselect extends JFormFieldList
{
	protected $type = "Countrymultiselect";
    protected $table = '#__mycityselector_countries';
    protected $tableNames = '#__mycityselector_country_names';

	public function getOptions()
	{
		$db = Joomla\CMS\Factory::getDbo();
        $langId = McsData::getLangId();
		$query = $db->getQuery(true);
        $query->select('a.id as id, n.name as name')
            ->from($this->table . ' as a')
            ->where('a.published = 1')
            ->innerJoin("`{$this->tableNames}` AS `n` ON `a`.`id` = `n`.`country_id`")
            ->where("`n`.`lang_id` = {$langId}")
            ->order('n.name ASC');

		$result   = $db->setQuery($query)->loadAssocList();
		$disabled = $this->getDisabled();
		$options  = [];
		foreach ($result as $country)
		{
			if (in_array($country['id'], $disabled))
			{
				$options[] = Joomla\CMS\HTML\HTMLHelper::_('select.option', $country['id'], $country['name'], 'value', 'text', true);
			}
			else
			{
				$options[] = Joomla\CMS\HTML\HTMLHelper::_('select.option', $country['id'], $country['name']);
			}

		}

		$this->setValue($this->getSelected());

		return array_merge(parent::getOptions(), $options);
	}

	protected function getDisabled()
	{
		$app           = Joomla\CMS\Factory::getApplication();
		$field_id      = $app->getUserStateFromRequest('com_mycityselector.edit.field.id', 'field_id');
		$field_value_id = $app->getUserStateFromRequest('com_mycityselector.edit.fieldvalue.id', 'id');
		$db            = Joomla\CMS\Factory::getDbo();
		$query         = $db->getQuery(true);
		$queryText     = 'SELECT DISTINCT country_id FROM #__mycityselector_field_value AS a
			LEFT JOIN #__mycityselector_value_country AS b ON a.id = b.field_value_id
			WHERE a.field_id = ' . $db->q($field_id);

		if ($field_value_id)
		{
			$queryText .= ' AND a.id != ' . $db->q($field_value_id);
		}
		$query->setQuery($queryText);
		$result = $db->setQuery($query)->loadColumn();

		return $result;
	}


	protected function getSelected()
	{
		$field_value_id = Joomla\CMS\Factory::getApplication()->getUserStateFromRequest('com_mycityselector.edit.fieldvalue.id', 'id');
		$db            = Joomla\CMS\Factory::getDbo();
		$query         = $db->getQuery(true);
		$query->select('country_id')->from('#__mycityselector_value_country')
			->where('field_value_id = ' . $db->q($field_value_id));
		$selected = $db->setQuery($query)->loadColumn();
		return $selected;
	}

}