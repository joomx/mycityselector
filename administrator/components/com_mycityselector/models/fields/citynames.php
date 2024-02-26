<?php

defined('_JEXEC') or die;

Joomla\CMS\Form\FormHelper::loadFieldClass('list');

class JFormFieldCitynames extends JFormField
{

    protected $type = "Citynames";
    protected $tableNames = '#__mycityselector_city_names';
    protected $tableLangs = '#__mycityselector_langs';
    protected $db;

    public function __construct(Form $form = null)
    {
        $this->db = Joomla\CMS\Factory::getDbo();

        parent::__construct($form);
    }

    public function getInput()
    {
        $langs = $this->getLangs();

        if(empty($langs)) {
            return false;
        }

        if(isset($_GET['id']) && !empty($_GET['id'])) {
            $html = $this->getEditForm($langs);
        } else {
            $html = $this->getCreateForm($langs);
        }

        return $html;

    }

    protected function getLangs()
    {
        $query = $this->db->getQuery(true);
        $query->select('id, locale')
            ->from($this->tableLangs)
            ->order('`default` DESC, locale ASC');

        $result = $this->db->setQuery($query)->loadAssocList();

        return $result;
    }

    protected function getEditForm($langs)
    {
        $html = '';

        foreach ($langs as $lang) {

            $city = $this->getCityNameByCityIdAndByLangId($_GET['id'], $lang['id']);

            if(empty($city)) {
                $html .= "<label for=\"name{$lang['id']}\">{$lang['locale']}: </label><input id=\"name{$lang['id']}\" type='text' name=\"jform[name][{$lang['id']}]\" class=\"form-control\" placeholder='Name'><br><br>";
            } else {
                $html .= "<label for=\"name{$lang['id']}\">{$lang['locale']}: </label><input id=\"name{$lang['id']}\" type='text' name=\"jform[name][{$lang['id']}]\" class=\"form-control\" placeholder='Name' value=\"{$city[0]['name']}\"><br><br>";
            }
        }

        return $html;
    }

    protected function getCreateForm($langs)
    {
        $html = '';

        foreach ($langs as $lang) {
            $html .= "<label for=\"name{$lang['id']}\">{$lang['locale']}: </label><input id=\"name{$lang['id']}\" type='text' name=\"jform[name][{$lang['id']}]\" class=\"form-control\" placeholder='Name'><br><br>";
        }

        return $html;
    }

    protected function getCityNameByCityIdAndByLangId($cityId, $langId)
    {
        $query = $this->db->getQuery(true);
        $query->select('name')
            ->from($this->tableNames)
            ->where("`city_id` = {$cityId} AND `lang_id` = {$langId}")
            ->order('name ASC');

        $result = $this->db->setQuery($query)->loadAssocList();

        return $result;
    }

}