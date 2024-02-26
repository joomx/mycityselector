<?php

defined('_JEXEC') or die;

JLoader::import('citycase', dirname(__FILE__));
JFormHelper::loadFieldClass('text');

class JFormFieldCityCase3 extends JFormFieldCityCase
{
	protected $type = "CityCase3";
	protected $caseId = 3;

	// Дательный падеж
	protected $case = \morphos\Cases::DATIVE;

}