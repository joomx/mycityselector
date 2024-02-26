<?php
$xml = '<?xml version="1.0" encoding="utf-8"?>
<componentdata>
';

$db    = JFactory::getDbo();
$query = $db->getQuery(true);
$query->select('*')->from('#__mycityselector_country');
$result = $db->setQuery($query)->loadAssocList();
foreach ($result as $countryData)
{
	$xml .= '<country name="'.$countryData['name'].'" subdomain="'.$countryData['subdomain'].'">' . "\r\n";
	$query = $db->getQuery(true);
	$query->select('*')->from('#__mycityselector_province')->where('country_id = '.$countryData['id']);
	$provinceResult = $db->setQuery($query)->loadAssocList();
	foreach ($provinceResult as $provinceData) {
		$xml .= '<province name="'.$provinceData['name'].'" subdomain="'.$provinceData['subdomain'].'">' . "\r\n";
		$query = $db->getQuery(true);
		$query->select('*')->from('#__mycityselector_city')->where('province_id = '.$provinceData['id']);
		$cityResult = $db->setQuery($query)->loadAssocList();
		foreach ($cityResult as $cityData) {
			$xml .= '<city name="'.$cityData['name'].'" subdomain="'.$cityData['subdomain'].'" />' . "\r\n";
		}
		$xml .= '</province>'. "\r\n";
	}
	$xml .= '</country>'. "\r\n";
}
$xml .= '</componentdata>';
file_put_contents(JPATH_ROOT . 'cities.xml',$xml);