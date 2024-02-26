<?php
defined('_JEXEC') or die;

use joomx\mcs\plugin\helpers\McsData;

Joomla\CMS\Form\FormHelper::loadFieldClass('list');

class JFormFieldCitiesofcountrylist extends JFormFieldList
{
    protected $type = "Citiesofcountrylist";

    protected $citiesTable = '#__mycityselector_cities';
    protected $citiesTableNames = '#__mycityselector_city_names';
    protected $countriesTable = '#__mycityselector_countries';
    protected $countryId;
    protected $db;
    protected $langId;

    protected $validate = '';
    protected $validationtext = 'Что-тос этим полем не так :(';


    public function __construct()
    {
        parent::__construct();
        $this->countryId = @$_GET['id'];
        $this->db = Joomla\CMS\Factory::getDbo();
        $this->langId = McsData::getLangId();
    }


    public function getOptions()
    {
        $query = $this->db->getQuery(true);
        $countryIdCondition = empty($this->countryId) ? "" : "AND country_id = {$this->countryId}";
        $query->select('a.id as id, n.name as name')
            ->from($this->citiesTable . ' as a')
            ->where("published = 1 {$countryIdCondition}")
            ->innerJoin("`{$this->citiesTableNames}` AS `n` ON `a`.`id` = `n`.`city_id`")
            ->where("`n`.`lang_id` = {$this->langId}")
            ->order('n.name ASC');
        $result = $this->db->setQuery($query)->loadAssocList();
        $options  = [];
        foreach ($result as $city) {
            $options[] = Joomla\CMS\HTML\HTMLHelper::_('select.option', $city['id'], $city['name']);
        }
        array_unshift($options, Joomla\CMS\HTML\HTMLHelper::_('select.option', '', ''));
        $this->setValue($this->getSelected());
        return array_merge(parent::getOptions(), $options);
    }


    protected function getSelected()
    {
        $query = $this->db->getQuery(true);
        if (empty($this->countryId)) {
            $query->select('default_city_id')
                ->from($this->countriesTable);
        } else {
            $query->select('default_city_id')
                ->from($this->countriesTable)
                ->where("id = {$this->countryId}");
        }
        $result = $this->db->setQuery($query)->loadAssocList();
        return $result[0]['default_city_id'] ?? '';
    }

}