<?php

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');
if (!class_exists('McsData')) {
    JLoader::register('plgSystemPlgMycityselector', JPATH_ROOT . '/plugins/system/plgmycityselector/plgmycityselector.php', false);
    JLoader::load('plgSystemPlgMycityselector');
    JLoader::import('plugins.system.plgmycityselector.helpers.McsData', JPATH_ROOT);
}

use joomx\mcs\plugin\helpers\McsData;

class JFormFieldCityselect extends JFormFieldList
{

    protected $type = "Cityselect";
    protected $citiesTable = '#__mycityselector_cities';
    protected $citiesTableNames = '#__mycityselector_city_names';
    protected $provincesTable = '#__mycityselector_provinces';
    protected $provincesTableNames = '#__mycityselector_province_names';
    protected $countriesTable = '#__mycityselector_countries';
    protected $countriesTableNames = '#__mycityselector_country_names';
    protected $langId;
    protected $db;

    public function __construct(Form $form = null)
    {
        $this->db = JFactory::getDbo();
        $this->langId = McsData::getLangId();

        parent::__construct($form);
    }

    public function getOptions()
    {
        $citiesOptions = $this->getCities();
        $provincesOptions = $this->getProvinces();
        $countriesOptions = $this->getCountries();

        $this->setValue($this->getSelected());

        return array_merge(parent::getOptions(), $citiesOptions, $provincesOptions, $countriesOptions);
    }

    protected function getCities()
    {
        $query = $this->db->getQuery(true);
        $query->select('a.subdomain as subdomain, n.name as name')
            ->from($this->citiesTable . ' as a')
            ->where('published = 1')
            ->innerJoin("`{$this->citiesTableNames}` AS `n` ON `a`.`id` = `n`.`city_id`")
            ->where("`n`.`lang_id` = {$this->langId}")
            ->order('n.name ASC');

        $result = $this->db->setQuery($query)->loadAssocList();

        $options = [];
        foreach ($result as $city) {
            $options[] = Joomla\CMS\HTML\HTMLHelper::_('select.option', $city['subdomain'], $city['name']);
        }

        return $options;
    }

    protected function getProvinces()
    {
        $query = $this->db->getQuery(true);
        $query->select('a.subdomain as subdomain, n.name as name')
            ->from($this->provincesTable . ' as a')
            ->where('published = 1')
            ->innerJoin("`{$this->provincesTableNames}` AS `n` ON `a`.`id` = `n`.`province_id`")
            ->where("`n`.`lang_id` = {$this->langId}")
            ->order('n.name ASC');

        $result = $this->db->setQuery($query)->loadAssocList();

        $options = [];
        foreach ($result as $province) {
            $options[] = Joomla\CMS\HTML\HTMLHelper::_('select.option', $province['subdomain'], $province['name']);
        }

        return $options;
    }

    protected function getCountries()
    {
        $query = $this->db->getQuery(true);
        $query->select('a.subdomain as subdomain, n.name as name')
            ->from($this->countriesTable . ' as a')
            ->where('published = 1')
            ->innerJoin("`{$this->countriesTableNames}` AS `n` ON `a`.`id` = `n`.`country_id`")
            ->where("`n`.`lang_id` = {$this->langId}")
            ->order('n.name ASC');

        $result = $this->db->setQuery($query)->loadAssocList();

        $options = [];
        foreach ($result as $country) {
            $options[] = Joomla\CMS\HTML\HTMLHelper::_('select.option', $country['subdomain'], $country['name']);

        }

        return $options;
    }

    protected function getSelected()
    {
        $selected = '';
        $params = JComponentHelper::getParams('com_mycityselector');

        if (!empty($params['default_city'])) {
            $selected = $params['default_city'];
        }

        return $selected;
    }

}
