<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Editors-xtd.pagebreak
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Editor MCS Insert button
 */
class PlgButtonMcsinsert extends JPlugin
{

	/**
	 * Display the button
	 * @param   string  $name  The name of the button to add
	 * @return array A two element array of (imageName, textToInsert)
	 */
	public function onDisplay($name)
	{
		$user = JFactory::getUser();
		if (!$user->guest) {
			$script = file_get_contents(dirname(__FILE__) . '/script.js');
			$script = str_replace('{editor_name}', $name, $script);
			JFactory::getDocument()->addScriptDeclaration($script);
			$button = new JObject;
			$button->modal = true;
			$button->class = 'btn';
			$button->link = 'index.php?option=com_mycityselector&amp;tmpl=component&amp;task=popup&amp;controller=fields';
			$button->text = JText::_('MCS');
			$button->name = 'tags-2';
			$button->options = "{handler: 'iframe', size: {x: 600, y: 400}}";
			return $button;
		}
		return false;
	}

}
