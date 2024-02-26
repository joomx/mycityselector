<?php
/**
 * MyCitySelector
 * @author  Konstantin Kutsevalov
 * @version 2.0.0
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

use TrueBV\Punycode;

class MycityselectorModelCountry extends Joomla\CMS\MVC\Model\AdminModel
{

	/**
	 * @inheritdoc
	 * @return null
	 */
	public function getTable($type = 'Country', $prefix = 'Table', $config = [])
	{
		$table = JTable::getInstance($type, $prefix, $config);

		return $table;
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


	public function getForm($data = [], $loadData = true)
	{
		$form = $this->loadForm('com_mycityselector.country', 'country', array('control' => 'jform', 'load_data' => $loadData));
		return $form;
	}


	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Joomla\CMS\Factory::getApplication()->getUserState('com_mycityselector.edit.user.data', array());
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

        if (!$data['ordering'])
        {
            $query = $db->getQuery(true);
            $query->select('max(ordering)+1')->from('#__mycityselector_countries');
            $data['ordering'] = $db->setQuery($query)->loadResult();
        }

		if (!empty($data['domain'])) {
	        if(!preg_match('/(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]/iu', $data['domain'])
	            &&
	           !preg_match('/^([\p{Cyrillic}\p{Latin}\d\.-]{1,64})?\.(?:\x{0440}\x{0444}|ru|su|arpa|info|aero|name|[a-z]{3})$/iu', $data['domain'])
	        ) {
	            $this->setError('Некорректное имя домена');
	            return false;
	        }
		}

        if (empty($data['domain']) && !empty($data['default_city_id'])) {
            $this->setError('Для домена необходимо указать город по умолчанию');
            return false;
        }

        if (empty($data['default_city_id'])) {
            $data['default_city_id'] = null;
            $query = $db->getQuery(true);
            $query->update($db->quoteName('#__mycityselector_countries'))
                ->set([$db->quoteName('default_city_id') . ' = NULL'])
                ->where([$db->quoteName('id') . ' = ' . $db->quote($data['id'])]);

            $db->setQuery($query)->execute();
        }

        // на случай русскоязычных доменов
        $data['subdomain'] = empty($data['subdomain']) ? '' : (new Punycode())->encode($data['subdomain']);
        if (!parent::save($data)) {
            return false;
        }
        $id = $data['id'] ? $data['id'] : $db->insertid();

        //сохраняем названия стран на разных языках
        if(!empty($data['name'])) {
            foreach ($data['name'] as $langId => $name) {
                $query = $db->getQuery(true);
                $query->select('count(*)')->from('#__mycityselector_country_names')
                    ->where('country_id=' . $db->q($id))
                    ->where('lang_id=' . $db->q($langId));

                $result = $db->setQuery($query)->loadResult();
                if ($result == 0) {
                    if(!empty($name)) {
                        //Добавляем новое название
                        $query = $db->getQuery(true);
                        $query->insert('#__mycityselector_country_names')->columns(['country_id', 'lang_id', 'name'])
                            ->values(implode(',', [$db->q($id), $db->q($langId), $db->q($name)]));

                        var_dump($query->__toString()); exit;

                        $db->setQuery($query)->execute();
                    }
                } else {
                    //Обновляем название
                    $query = $db->getQuery(true);
                    $query->update('#__mycityselector_country_names')->set('name=' . $db->q($name))
                        ->where('country_id=' . $id)->where('lang_id=' . $langId);
                    $db->setQuery($query)->execute();
                }
            }
        }
        return true;
    }

}
