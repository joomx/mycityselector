<?php
/**
 * @author Konstantin Kutsevalov
 * @email <mail@art-prog.ru>
 */

namespace adamasantares\jxforms;

/**
 * Class CheckboxInput
 * @package adamasantares\jxforms
 */
class CheckboxInput {

    /**
     * @var array
     */
    private $_config = [];


    /**
     * Creates checkbox field object
     * @param string $name
     * @param string $label
     * @param boolean $status Checked / unchecked (default FALSE)
     * @param array $config Optional parameters.
     *  Example:
     *  [
     *      'id' => "some_id",
     *      'description' => "",
     *      'class' => "class_name_for_html_element",
     *      'labelClass' => "class_name_for_html_label",
     *      'readOnly' => false,
     *      'atLeft' => false,
     *  ]
     */
    public function __construct($name, $label, $status = false, $config = [])
    {
        $config['name'] = $name;
        $config['label'] = $label;
        $config['checked'] = $status;
        $this->_config = array_merge($this->getDefaultConfig(), $config);
    }


    /**
     * Returns default config
     * @return array
     */
    private function getDefaultConfig()
    {
        return [
            'id' => '',
            'name' => '',
            'label' => '',
            'description' => '',
            'class' => 'form-group checkbox',
            'labelClass' => '',
            'checked' => false,
            'readOnly' => false,
            'atLeft' => false
        ];
    }


    /**
     * Renders and returns HTML code of field
     * @return string HTML
     */
    public function render()
    {
        $id = empty($this->_config['id']) ? '' : 'id="' . $this->_config['id'] . '"';
        $checked = $this->_config['checked'] ? 'checked="checked"' : '';
        $leftLabel = $this->_config['atLeft'] ? $this->_config['label'] : '';
        $rightLabel = $this->_config['atLeft'] ? '' : $this->_config['label'];
        $this->_config['labelClass'] .= empty($this->_config['description']) ? '' : ' hasTooltip';

        return '<div class="' . $this->_config['class'] . '">'
            . '<label class="' . $this->_config['labelClass'] . '" data-original-title="' . $this->_config['description'] . '">'
            . $leftLabel
            . '<input type="checkbox" name="' . $this->_config['name'] . '" value="1" ' . $id . ' ' . $checked . ' />'
            . $rightLabel
            . '</label></div>';
    }

}