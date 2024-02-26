<?php

defined('_JEXEC') or die;

class MycityselectorModelFieldvalue extends Joomla\CMS\MVC\Model\AdminModel
{

    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm('com_mycityselector.fieldvalue', 'fieldvalue', ['control' => 'jform', 'load_data' => $loadData]);
        return $form;
    }


    public function getTable($name = 'Fieldvalue', $prefix = 'Table', $options = [])
    {
        return parent::getTable($name, $prefix, $options);
    }


    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = Joomla\CMS\Factory::getApplication()->getUserState('com_mycityselector.edit.field.data', []);
        if (empty($data))
        {
            $data = $this->getItem();
        }
        return $data;
    }


    public function save($data)
    {
        if (parent::save($data))
        {
            $db = Joomla\CMS\Factory::getDbo();
            // Удаляем записи с таким же field_value_id
            $field_value_id = $this->getState('fieldvalue.id');

            $query = $db->getQuery(true);
            $query->delete('#__mycityselector_value_city')->where('field_value_id = ' . $db->quote($field_value_id));
            $result = $db->setQuery($query)->execute();

            if (isset($data['cities']) && !empty($data['cities']) && !empty($result))
            {
                if ($data['cities'] != 0)
                {
                    $query = $db->getQuery(true);
                    $query->insert('#__mycityselector_value_city (field_value_id, city_id)');
                    foreach ($data['cities'] as $cityId)
                    {
                        $query->values($db->q($field_value_id) . ',' . $db->q($cityId));
                    }

                    $db->setQuery($query)->execute();
                }
            }


            $query = $db->getQuery(true);
            $query->delete('#__mycityselector_value_province')->where('field_value_id = ' . $db->quote($field_value_id));
            $result = $db->setQuery($query)->execute();
            if (isset($data['provinces']) && !empty($data['provinces'])  && !empty($result))
            {
                if ($data['provinces'] != 0)
                {
                    $query = $db->getQuery(true);
                    $query->insert('#__mycityselector_value_province (field_value_id, province_id)');
                    foreach ($data['provinces'] as $cityId)
                    {
                        $query->values($db->q($field_value_id) . ',' . $db->q($cityId));
                    }

                    $db->setQuery($query)->execute();
                }
            }

            $query = $db->getQuery(true);
            $query->delete('#__mycityselector_value_country')->where('field_value_id = ' . $db->quote($field_value_id));
            $result = $db->setQuery($query)->execute();
            if (isset($data['countries']) && !empty($data['countries']) && !empty($result))
            {
                if ($data['countries'] != 0)
                {
                    $query = $db->getQuery(true);
                    $query->insert('#__mycityselector_value_country (field_value_id, country_id)');
                    foreach ($data['countries'] as $cityId)
                    {
                        $query->values($db->q($field_value_id) . ',' . $db->q($cityId));
                    }

                    $db->setQuery($query)->execute();
                }
            }
            return true;
        }
        return false;
    }


    public function delete(&$pks)
    {
        if (parent::delete($pks))
        {
            if (!empty($pks))
            {
                $db = Joomla\CMS\Factory::getDbo();
                $query = $db->getQuery(true);
                $query->delete('#__mycityselector_value_city')
                    ->where('field_value_id IN (' . implode(',', $pks) . ')');
                $db->setQuery($query)->execute();

                $query = $db->getQuery(true);
                $query->delete('#__mycityselector_value_province')
                    ->where('field_value_id IN (' . implode(',', $pks) . ')');
                $db->setQuery($query)->execute();

                $query = $db->getQuery(true);
                $query->delete('#__mycityselector_value_country')
                    ->where('field_value_id IN (' . implode(',', $pks) . ')');
                $db->setQuery($query)->execute();

                return true;
            }
            else
            {
                return true;
            }
        }
    }

}