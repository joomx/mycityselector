<?php

defined('_JEXEC') or die;

JLoader::import('citycase', dirname(__FILE__));
JFormHelper::loadFieldClass('text');

class JFormFieldCityCase2 extends JFormFieldCityCase
{
	protected $type = "CityCase2";
	protected $caseId = 2;

	// Родительный падеж
	protected $case = \morphos\Cases::GENETIVE;

}