<?php

defined('_JEXEC') or die;

use TrueBV\Punycode;

class MycityselectorModelProvince extends Joomla\CMS\MVC\Model\AdminModel
{

	public function getForm($data = [], $loadData = true)
	{
		$form = $this->loadForm('com_mycityselector.province', 'province', ['control' => 'jform', 'load_data' => $loadData]);
		return $form;
	}


	public function getTable($name = 'Province', $prefix = 'Table', $options = [])
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
        $db = Joomla\CMS\Factory::getDbo();

        if ( !$data['ordering'] ) {
            $query = $db->getQuery(true);
            $query->select('max(ordering)+1')->from('#__mycityselector_provinces');
            $data['ordering'] = $db->setQuery($query)->loadResult();
        }

        // на случай русскоязычных доменов
        $data['subdomain'] = empty($data['subdomain']) ? '' : (new Punycode())->encode($data['subdomain']);

        if ( !parent::save($data) ) {
            return false;
        }
        $id = !empty($data['id']) ?  // иногда метод $db->insertid() не возвращает ID созданной записи и это баг, но есть запасной вариант его получить
            $data['id'] : ( $db->insertid() ? $db->insertid() : $this->getState('province.id') );

        //сохраняем названия городов на разных языках
        if ( !empty($data['name']) ) {
            foreach ($data['name'] as $langId => $name) {
                $query = $db->getQuery(true);
                $query->select('count(*)')->from('#__mycityselector_province_names')
                    ->where('province_id=' . $db->q($id))
                    ->where('lang_id=' . $db->q($langId));
                $result = $db->setQuery($query)->loadResult();

                if ($result == 0) {
                    if ( !empty($name) ) {
                        //Добавляем новое название
                        $query = $db->getQuery(true);
                        $query->insert('#__mycityselector_province_names')->columns(['province_id', 'lang_id', 'name'])
                            ->values(implode(',', [$db->q($id), $db->q($langId), $db->q($name)]));
                        $db->setQuery($query)->execute();
                    }
                } else {
                    // Обновляем название
                    $query = $db->getQuery(true);
                    $query->update('#__mycityselector_province_names')->set('name=' . $db->q($name))
                        ->where('province_id=' . $id)->where('lang_id=' . $langId);
                    $db->setQuery($query)->execute();
                }
            }
        }

        return true;
    }

}