<?php

defined('_JEXEC') or die;

JLoader::import('citycase', dirname(__FILE__));
JFormHelper::loadFieldClass('text');

class JFormFieldCityCase4 extends JFormFieldCityCase
{
	protected $type = "CityCase4";
	protected $caseId = 4;

	// Винительный падеж
	protected $case = \morphos\Cases::ACCUSATIVE;

}