<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Editors-xtd.pagebreak
 */

defined('_JEXEC') or die;

/**
 * Editor MCS Insert button
 */
class PlgButtonMcsinsert extends Joomla\CMS\Plugin\CMSPlugin
{

	/**
	 * Display the button
	 * @param   string  $name  The name of the button to add
	 * @return array A two element array of (imageName, textToInsert)
	 */
	public function onDisplay($name)
	{
		$user = Joomla\CMS\Factory::getUser();
		if (!$user->guest) {
			$script = file_get_contents(dirname(__FILE__) . '/script.js');
			$script = str_replace('{editor_name}', $name, $script);
            Joomla\CMS\Factory::getDocument()->addScriptDeclaration($script);
			$button = new Joomla\CMS\Object\CMSObject;
			$button->modal = true;
			$button->class = 'btn';
			$button->link = 'index.php?option=com_mycityselector&amp;tmpl=component&amp;view=fields&amp;mode=popup';
			$button->text = JText::_('MCS');
			$button->name = 'tags-2';
			$button->options = "{handler: 'iframe', size: {x: 600, y: 400}}";
			return $button;
		}
		return false;
	}

}
