<?php

defined('_JEXEC') or die;

Joomla\CMS\Form\FormHelper::loadFieldClass('list');

class JFormFieldProvincenames extends JFormField
{

    protected $type = "Provincenames";
    protected $tableNames = '#__mycityselector_province_names';
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

            $province = $this->getProvinceNameByProvinceIdAndByLangId($_GET['id'], $lang['id']);

            if(empty($province)) {
                $html .= "<label for=\"name{$lang['id']}\">{$lang['locale']}: </label><input id=\"name{$lang['id']}\" type='text' name=\"jform[name][{$lang['id']}]\" class=\"form-control\" placeholder='Name'><br><br>";
            } else {
                $html .= "<label for=\"name{$lang['id']}\">{$lang['locale']}: </label><input id=\"name{$lang['id']}\" type='text' name=\"jform[name][{$lang['id']}]\" class=\"form-control\" placeholder='Name' value=\"{$province[0]['name']}\"><br><br>";
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

    protected function getProvinceNameByProvinceIdAndByLangId($provinceId, $langId)
    {
        $query = $this->db->getQuery(true);
        $query->select('name')
            ->from($this->tableNames)
            ->where("`province_id` = {$provinceId} AND `lang_id` = {$langId}")
            ->order('name ASC');

        $result = $this->db->setQuery($query)->loadAssocList();

        return $result;
    }

}