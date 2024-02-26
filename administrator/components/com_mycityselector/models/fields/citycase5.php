<?php

defined('_JEXEC') or die;

JLoader::import('citycase', dirname(__FILE__));
JFormHelper::loadFieldClass('text');

class JFormFieldCityCase5 extends JFormFieldCityCase
{
	protected $type = "CityCase5";
	protected $caseId = 5;

	// Творительный падеж
	protected $case = \morphos\Cases::ABLATIVE;

}