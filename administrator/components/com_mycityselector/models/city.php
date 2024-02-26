<?php

defined('_JEXEC') or die;

use joomx\mcs\plugin\helpers\McsData;
use TrueBV\Punycode;
use morphos\Russian\Cases;


class MycityselectorModelCity extends Joomla\CMS\MVC\Model\AdminModel
{

	public function getForm($data = [], $loadData = true)
	{
		$form = $this->loadForm('com_mycityselector.city', 'city', ['control' => 'jform', 'load_data' => $loadData]);
		return $form;
	}


	public function getTable($name = 'City', $prefix = 'Table', $options = [])
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
		if (!empty($data->subdomain)) {
            $data->subdomain = (new Punycode())->decode($data->subdomain);
        }
		return $data;
	}


	public function save($data)
	{
		$db    = Joomla\CMS\Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('country_id')->from('#__mycityselector_provinces')
			->where('id = ' . $db->escape($data['province_id']));
		$data['country_id'] = $db->setQuery($query)->loadResult();

		if (!$data['ordering'])
		{
			$query = $db->getQuery(true);
			$query->select('max(ordering)+1')->from('#__mycityselector_cities');
			$data['ordering'] = $db->setQuery($query)->loadResult();
		}

		if (McsData::MCS_FREE && $this->checkLimits() >= McsData::MCS_LIMIT_5)
		{
            Joomla\CMS\Factory::getApplication()->enqueueMessage(JText::sprintf('COM_MYCITYSELECTOR_LIMITS_REACHED'), 'error');
			$data['published'] = 0;
		}

		// на случай русскоязычных доменов
        $data['subdomain'] = empty($data['subdomain']) ? '' : (new Punycode())->encode($data['subdomain']);

		if (!parent::save($data))
		{
			return false;
		}

		$id = $data['id'] ? $data['id'] : $db->insertid();

		//сохраняем названия городов на разных языках
		if(!empty($data['name'])) {
		    foreach ($data['name'] as $langId => $name) {

                $query = $db->getQuery(true);
                $query->select('count(*)')->from('#__mycityselector_city_names')
                    ->where('city_id=' . $db->q($id))
                    ->where('lang_id=' . $db->q($langId));

                $result = $db->setQuery($query)->loadResult();

                if ($result == 0)
                {
                    if(!empty($name)) {
                        //Добавляем новое название
                        $query = $db->getQuery(true);
                        $query->insert('#__mycityselector_city_names')->columns(['city_id', 'lang_id', 'name'])
                            ->values(implode(',', [$db->q($id), $db->q($langId), $db->q($name)]));
                        $db->setQuery($query)->execute();
                    }
                }
                else
                {
                    //если название не пустое, то обновляем
//                    if(!empty($name)) {
                        //Обновляем название
                        $query = $db->getQuery(true);
                        $query->update('#__mycityselector_city_names')->set('name=' . $db->q($name))
                            ->where('city_id=' . $id)->where('lang_id=' . $langId);
                        $db->setQuery($query)->execute();
//                    } else {
//                        //если название пустое, то удаляем
//                        $query = $db->getQuery(true);
//                        $query->delete('#__mycityselector_city_names')
//                            ->where('city_id=' . $id)->where('lang_id=' . $langId);
//                        $db->setQuery($query)->execute();
//                    }

                }

            }
        }

        $langs = $this->getLangs();
        $cases = [
            2 => Cases::GENETIVE,
            3 => Cases::DATIVE,
            4 => Cases::ACCUSATIVE,
            5 => Cases::ABLATIVE,
            6 => Cases::PREPOSITIONAL,
        ];
        foreach($cases as $i => $case) {
            foreach ($langs as $lang) {
                $query = $db->getQuery(true);
                $query->select('count(*)')->from('#__mycityselector_city_cases')
                    ->where('city_id=' . $db->q($id))
                    ->where('case_id=' . $db->q($i))
                    ->where('lang_id=' . $db->q($lang['id']));

                $result = $db->setQuery($query)->loadResult();

                if ($result == 0) {
                    if (!empty($data['city_case_' . $i][$lang['id']]))
                    {
                        $value = $data['city_case_' . $i][$lang['id']];
                    } else {
                        $value = \morphos\Russian\GeographicalNamesInflection::getCase($data['name'][$lang['id']], $case);
                    }
                    //Добавляем новую запись падежа
                    $query = $db->getQuery(true);
                    $query->insert('#__mycityselector_city_cases')->columns(['city_id', 'case_id', 'lang_id', 'value'])
                        ->values(implode(',', [$db->q($id), $db->q($i), $db->q($lang['id']), $db->q($value)]));
                    $db->setQuery($query)->execute();
                } else {
                    //Обновляем запись падежа
                    $query = $db->getQuery(true);
                    $query->update('#__mycityselector_city_cases')->set('value=' . $db->q($data['city_case_' . $i][$langId]))
                        ->where('city_id=' . $id)->where('case_id=' . $i)->where('lang_id=' . $db->q($langId));
                    $db->setQuery($query)->execute();
                }
            }
		}

		return true;
	}


	private function checkLimits()
	{
		if (McsData::MCS_FREE)
		{
			$isExists = $this->_db->setQuery("SELECT count(`id`) FROM #__mycityselector_cities WHERE published=1")->loadResult();
			return $isExists;
		}
	}


	public function publish(&$pks, $value = 1)
	{
		if ($value == 1)
		{
			if (McsData::MCS_FREE)
			{
				$published  = $this->checkLimits();
				$canPublish = McsData::MCS_LIMIT_5 - $published;
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
		else
		{
			parent::publish($pks, $value);
		}
	}

    protected function getLangs()
    {
        $db    = Joomla\CMS\Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, locale')
            ->from('#__mycityselector_langs')
            ->order('`default` DESC, locale ASC');

        $result = $db->setQuery($query)->loadAssocList();

        return $result;
    }

}