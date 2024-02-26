<?php
/**
 * @author Konstantin Kutsevalov
 * @email <mail@art-prog.ru>
 */

namespace adamasantares\jxforms;

/**
 * Class ChosenInput
 * @package adamasantares\jxforms
 */
class ChosenInput {

    /**
     * @var array
     */
    private $_config = [];


    /**
     * Creates Chosen selector object
     * @param string $name
     * @param string $label
     * @param boolean $status Checked / unchecked (default FALSE)
     * @param array $config Optional parameters.
     *  Example:
     *  [
     *      'id' => "",
     *      'description' => "",
     *      'class' => "class_name_for_html_element",
     *      'labelClass' => "class_name_for_html_label",
     *      'disabled' => false,
     *      'readOnly' => false,
     *      'default' => 0,
     *      'options' => ['0' => 'Usual', '1' => 'Super', '2' => 'The best'],
     *      'search' => false, // search field in list
     *  ]
     *  Also for options you can define <optgroup> like this
     * 'options' => [
     *      'group1_caption' => [
     *          'value1' => 'option1_caption',
     *          'value2' => 'option2_caption',
     *          ...
     *      ],
     *      'group2_caption' => [ ... ]
     * ]
     */
    public function __construct($name, $label, $value = '0', $config = [])
    {

        // $selector = '.advancedSelect', $debug = null, $options = array()
        $this->_config['name'] = $name;
        $this->_config['value'] = $value;
        $this->_config['label'] = $label;
        $this->_config = array_merge($this->getDefaultConfig(), $config);
        // check id
        if (empty($this->_config['id'])) {
            $this->_config['id'] = str_replace([']', '['], ['', '-'], $name);
        }
    }


    /**
     * Returns default config
     * @return array
     */
    private function getDefaultConfig()
    {
        return [
            'id' => '',
            'class' => '',
            'options' => ['1' => 'Yes', '0' => 'No'],
            'default' => 0,
            'search' => false
        ];
    }


    /**
     * Renders and returns HTML code of field
     * @return string HTML
     */
    public function render()
    {
        \JHtml::_('jquery.framework');
        // Chosen options
        $options = [];
        if ($this->_config['search'] === false) {
            $options['disable_search_threshold'] = 10;
        }
        $options['allow_single_deselect'] = true;
        $options['no_results_text'] = \JText::_('JGLOBAL_SELECT_NO_RESULTS_MATCH');
        $json = json_encode($options, false);
        \JHtml::_('script', 'jui/chosen.jquery.min.js', false, true, false, false, false);
        \JHtml::_('stylesheet', 'jui/chosen.css', false, true);
        // chosen js init
        \JFactory::getDocument()->addScriptDeclaration('jQuery(document).ready(function (){
            jQuery("#' . $this->_config['id'] . '").chosen(' . $json . ');
        });');
        // html markup
        $this->_config['labelClass'] .= empty($this->_config['description']) ? '' : ' hasTooltip';
        $select = '<div class="control-group">'
            . '<div class="control-label">'
                . '<label class="' . $this->_config['labelClass']
                    . '" title="" data-original-title="' . $this->_config['description'] . '">'
	            . $this->_config['label'] . '</label>'
            . '</div>'
            . '<div class="controls">'
                . '<select id="' . $this->_config['id'] . '" name="' . $this->_config['name']
                    . '" class="' . $this->_config['class'] . '">';
        // - selector's options
        if (empty($this->_config['options']) || !is_array($this->_config['options'])) {
            $this->_config['options'] = $this->getDefaultConfig()['options'];
        }
        foreach ($this->_config['options'] as $key => $data) {
            if (is_array($data)) {
                // optgroup
                $select .= '<optgroup label="' . $key . '">';
                foreach ($data as $value => $caption) {
                    $select .= '<option value="' . $value . '">' . $caption . '</option>';
                }
                $select .= '</optgroup>';
            } else {
                // simple options list
                $select .= '<option value="' . $key . '">' . $data . '</option>';
            }
        }
        $select .= '</select>'
            . '</div>'
        . '</div>';

        return $select;
    }

}