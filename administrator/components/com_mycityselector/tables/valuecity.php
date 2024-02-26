<?php

defined('_JEXEC') or die();

class TableValuecity extends Joomla\CMS\Table\Table
{
	public function __construct($db)
	{
		parent::__construct( '#__mycityselector_value_city', 'id', $db );
	}
}