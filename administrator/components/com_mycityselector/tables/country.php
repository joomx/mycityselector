<?php

defined('_JEXEC') or die();

class TableCountry extends Joomla\CMS\Table\Table
{
	public function __construct($db)
	{
		parent::__construct( '#__mycityselector_countries', 'id', $db );
	}
}