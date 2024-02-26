<?php

defined('_JEXEC') or die;

JLoader::import('citycase', dirname(__FILE__));
JFormHelper::loadFieldClass('text');

class JFormFieldCityCase6 extends JFormFieldCityCase
{
	protected $type = "CityCase6";
	protected $caseId = 6;

	// Предложный падеж
	protected $case = \morphos\Cases::PREPOSITIONAL;

}