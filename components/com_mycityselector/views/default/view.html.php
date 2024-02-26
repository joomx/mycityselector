<?php
/**
 * MyCitySelector
 * @author  Konstantin Kutsevalov
 * @version 2.0.0
 */

defined('_JEXEC') or die;


class MycityselectorViewDefault extends JViewLegacy
{
	public function display($tpl = null)
	{
		$countryId = \JFactory::getApplication()->input->getCmd('country_id', 0);
		$country   = $this->getModel('country');
		// sorting
		$country->setOrder('ordering', 'ASC');
		$this->countries = [];
		if ($countryId)
		{
			$countryOne = $country->getItem($countryId);
			if (!empty($countryOne))
			{
				$this->countries = [$countryOne];
			}
		}
		else
		{
			$this->countries = $country->getItems(false, true);
		}
		$province = $this->getModel('province');
		$province->setOrder('ordering', 'ASC');

		$city = $this->getModel('city');
		$city->setOrder('ordering', 'ASC');
		$rows      = $province->getItems(null, false, true);
		$this->provinces = [];
		foreach ($rows as $row)
		{
			$this->provinces[$row['id']] = [
				'id'         => $row['id'],
				'country_id' => $row['country_id'],
				'name'       => $row['name'],
			];
		}

		// menu
		$menu     = \JFactory::getApplication()->getMenu();
		$this->menuItem = $menu->getActive()->params;

		$this->cities = $city->getItems(null, false, true);

		parent::display($tpl);
	}
}
