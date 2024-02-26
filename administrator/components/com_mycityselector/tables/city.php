<?php

defined('_JEXEC') or die();

class TableCity extends Joomla\CMS\Table\Table
{
	public function __construct($db)
	{
		parent::__construct( '#__mycityselector_cities', 'id', $db );
	}
}