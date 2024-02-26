<?php

defined('_JEXEC') or die();

class TableField extends Joomla\CMS\Table\Table
{
	public function __construct($db)
	{
		parent::__construct( '#__mycityselector_field', 'id', $db );
	}
}