<?php
/**
 * @author Konstantin Kutsevalov (Adamas Antares)
 * @email <mail@art-prog.ru>
 */

// Joomla'a classes
require_once JPATH_BASE . '/../libraries/joomla/form/field.php';
require_once JPATH_BASE . '/../libraries/joomla/form/fields/text.php';
require_once JPATH_BASE . '/../libraries/joomla/form/fields/checkbox.php';
require_once JPATH_BASE . '/../libraries/joomla/form/fields/radio.php';

// form generator
if (!class_exists('\adamasantares\jxforms\JxForm')) {
    require_once __DIR__ . '/JxForm.php';
}

// form fields helper
if (!class_exists('\adamasantares\jxforms\JxField')) {
    require_once __DIR__ . '/JxField.php';
}