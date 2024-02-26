<?php

defined('_JEXEC') or die();

class TableProvince extends Joomla\CMS\Table\Table
{
	public function __construct($db)
	{
		parent::__construct( '#__mycityselector_provinces', 'id', $db );
	}
}