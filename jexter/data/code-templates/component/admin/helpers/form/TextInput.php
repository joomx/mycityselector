<?php
/**
 * @author Konstantin Kutsevalov
 * @email <mail@art-prog.ru>
 */

namespace adamasantares\jxforms;

/**
 * Wrap for JFormFieldText
 * Class TextInput
 * @package adamasantares\jxforms
 */
class TextInput extends \JFormFieldText {

    /**
     * @var array
     */
    private $_config = [];


    /**
     * Creates text field object
     * @param string $name
     * @param string $label
     * @param mixed $value
     * @param array $config Optional parameters.
     *  Example:
     *  [
     *      'required' => true,
     *      'placeholder' => "Name",
     *      'description' => "",
     *      'inline' => true, // label at left of input
     *      'pattern' => "",
     *      'maxLength' => "100",
     *      'class' => "class_name_for_html_element",
     *      'labelClass' => "class_name_for_html_label",
     *      'size' => "45",
     *      'readOnly' => false,
     *      'autoFocus' => true,
     *      'format' => "",
     *      'suggestions' => [
     *          "value" => "title",
     *          "value" => "title",
     *          ...
     *      ],
     *  ]
     */
    public function __construct($name, $label, $value = '', $config = [])
    {
        $this->name = $name;
        $this->value = $value;
        $this->_config = array_merge($this->getDefaultConfig(), $config);
        // configure xml
        $xml = '<'.'?xml version="1.0"?'.'>'."\n".'<doc>'
            . '<field name="' . $name . '" '
            . 'type="text" '
            . 'label="' . $label . '" ';
        if (!empty($this->_config['suggestions']) && is_array($this->_config['suggestions'])) {
            $xml .= ">\n";
            foreach ($this->_config['suggestions'] as $val => $title) {
                $xml .= '<option value="' . htmlspecialchars($val) . '">' . htmlspecialchars($title) . "</option>\n";
            }
            $xml .= "</field>";
        } else {
            $xml .= "/>";
        }
        $xml .= '</doc>';
        $xml = simplexml_load_string($xml);
        $this->element = $xml->field;
        foreach ($this->_config as $param => $val) {
            //$param = strtolower($param);
            if ($param == 'placeholder') {
                $this->hint = $this->_config[$param];
            } else {
                $this->__set($param, $val);
            }
        }
    }


    /**
     * Returns default config
     * @return array
     */
    private function getDefaultConfig()
    {
        return [
            'class' => 'input-xxlarge input-large-text',
            'autocomplete' => 'off',
            'inline' => true
        ];
    }


    /**
     * Renders and returns HTML code of field
     * @return string HTML
     */
    public function render()
    {
        $html = $this->renderField();
        if (!empty($this->_config['inline']) && $this->_config['inline'] === true) { // inline style
            $html = '<div class="form-inline form-inline-header">' . $html . '</div>';
        }
        return $html;
    }
} 