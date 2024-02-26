<?php

defined('_JEXEC') or die();

class TableFieldvalue extends Joomla\CMS\Table\Table
{
	public function __construct($db)
	{
		parent::__construct( '#__mycityselector_field_value', 'id', $db );
	}
}