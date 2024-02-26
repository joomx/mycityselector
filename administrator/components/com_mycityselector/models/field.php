<?php

defined('_JEXEC') or die;

use joomx\mcs\plugin\helpers\McsData;

class MycityselectorModelField extends Joomla\CMS\MVC\Model\AdminModel
{

	public function getForm($data = [], $loadData = true)
	{
		$form = $this->loadForm('com_mycityselector.field', 'field', ['control' => 'jform', 'load_data' => $loadData]);
		return $form;
	}


	public function getTable($name = 'Field', $prefix = 'Table', $options = [])
	{
		return parent::getTable($name, $prefix, $options);
	}


	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Joomla\CMS\Factory::getApplication()->getUserState('com_mycityselector.edit.user.data', []);
		if (empty($data))
		{
			$data = $this->getItem();
		}
		return $data;
	}


	public function getFieldValues()
	{
		$field_id = Joomla\CMS\Factory::getApplication()->input->get('id', 0, 'int');
        $langId = McsData::getLangId();
		$db       = Joomla\CMS\Factory::getDbo();

		$query    = $db->getQuery(true);
		$query->select('GROUP_CONCAT(DISTINCT c_n.name ORDER BY c_n.name) AS cities, 
		                        GROUP_CONCAT(DISTINCT p_n.name ORDER BY p_n.name) AS provinces, 
		                        GROUP_CONCAT(DISTINCT ct_n.name ORDER BY ct_n.name) AS countries, 
		                        a.value AS value, a.`default` as `default`, a.id AS id')
			->from('#__mycityselector_field_value AS a')
			->leftJoin('#__mycityselector_value_city AS v_c ON a.id = v_c.field_value_id')
            ->leftJoin('#__mycityselector_city_names AS c_n ON v_c.city_id = c_n.city_id AND c_n.lang_id = '. $db->q($langId))

            ->leftJoin('#__mycityselector_value_province AS v_p ON a.id = v_p.field_value_id')
            ->leftJoin('#__mycityselector_province_names AS p_n ON v_p.province_id = p_n.province_id AND p_n.lang_id = '. $db->q($langId))

            ->leftJoin('#__mycityselector_value_country AS v_ct ON a.id = v_ct.field_value_id')
            ->leftJoin('#__mycityselector_country_names AS ct_n ON v_ct.country_id = ct_n.country_id AND ct_n.lang_id = '. $db->q($langId))

			->where('a.field_id = ' . $db->q($field_id))
			->group('a.id');

        return $db->setQuery($query)->loadAssocList();
	}


	public function delete(&$pks)
	{
		if (parent::delete($pks))
		{
			$db = Joomla\CMS\Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id')->from('#__mycityselector_field_value')
				->where('field_id IN (' . implode(',', $pks) . ')');
			$result          = $db->setQuery($query)->loadColumn();
			$fieldValueModel = JModelLegacy::getInstance('fieldvalue', 'MycityselectorModel');

			return $fieldValueModel->delete($result);
		}
	}


	public function save($data)
	{
		if (McsData::MCS_FREE && $this->checkLimits() >= McsData::MCS_LIMIT_15)
		{
            Joomla\CMS\Factory::getApplication()->enqueueMessage(JText::sprintf('COM_MYCITYSELECTOR_LIMITS_REACHED'), 'error');
			$data['published'] = 0;
		}
		if (parent::save($data))
		{
			if (!$data['id'])
			{
				$field_id = $this->getState($this->getName() . '.id');
				$query    = $this->_db->getQuery(true);
				$query->insert('#__mycityselector_field_value')
					->columns(['field_id', '`default`'])->values($field_id . ',1');

				return $this->_db->setQuery($query)->execute();
			}
			return true;
		}
		else
		{
			return false;
		}
	}


	private function checkLimits()
	{
		if (McsData::MCS_FREE)
		{
			$isExists = $this->_db->setQuery("SELECT count(`id`) FROM #__mycityselector_field WHERE published=1")->loadResult();
			return $isExists;
		}
	}


	public function publish(&$pks, $value = 1)
	{
		if ($value == 1)
		{
			$published  = $this->checkLimits();
			$canPublish = McsData::MCS_LIMIT_15 - $published;
			if (sizeof($pks) <= $canPublish)
			{
				parent::publish($pks, $value);
			}
			else
			{
                Joomla\CMS\Factory::getApplication()->enqueueMessage(JText::sprintf('COM_MYCITYSELECTOR_LIMITS_REACHED'), 'error');
				return false;
			}
		}
		else
		{
			parent::publish($pks, $value);
		}
	}

}