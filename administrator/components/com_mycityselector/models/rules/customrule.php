<?php
\defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Form\FormRule;

class JFormRuleCustomRule extends FormRule {

    public function test(\SimpleXMLElement $element, $value, $group = null, $input = null, $form = null)
    {
        return true;
    }

}