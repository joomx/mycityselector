<?php
/**
 * @author Konstantin Kutsevalov
 * @email <mail@art-prog.ru>
 */

namespace adamasantares\jxforms;

/**
 * Wrap for JFormFieldRadio
 * Class RadioInput
 * @package adamasantares\jxforms
 */
class RadioInput extends \JFormFieldRadio {

    /**
     * @var array
     */
    private $_config = [];


    /**
     * Creates radio button object
     * @param string $name
     * @param string $label
     * @param string $value
     * @param array $config Optional parameters.
     *  Example:
     *  [
     *      'id' => "",
     *      'description' => "",
     *      'class' => "class_name_for_html_element",
     *      'labelClass' => "class_name_for_html_label",
     *      'disabled' => false,
     *      'readOnly' => false,
     *      'options' => ['1' => 'Yes', '0' => 'No']
     *  ]
     */
    public function __construct($name, $label, $value = '0', $config = [])
    {
        $this->value = $value;
        $this->name = $name;
        $this->_config = array_merge($this->getDefaultConfig(), $config);
        // configure xml
        $xml = '<'.'?xml version="1.0"?'.'>'."\n".'<doc>'
            . '<field name="' . $name . '" '
            . 'type="text" '
            . 'label="' . $label . '" ';
        if (!empty($this->_config['options']) && is_array($this->_config['options'])) {
            $xml .= ">\n";
            foreach ($this->_config['options'] as $val => $title) {
                $xml .= '<option value="' . htmlspecialchars($val) . '">' . htmlspecialchars($title) . "</option>\n";
            }
            $xml .= "</field>";
        } else {
            $xml .= "/>";
        }
        $xml .= '</doc>';
        $xml = simplexml_load_string($xml);
        $this->element = $xml->field;
        unset($this->_config['options']);
        foreach ($this->_config as $param => $val) {
            //$param = strtolower($param);
            if ($param == 'placeholder') {
                $this->hint = $this->_config[$param];
            } else {
                $this->__set($param, $val);
            }
        }
        // check id
        if (empty($this->id)) {
            $this->id = str_replace([']', '['], ['', '-'], $this->name);
        }
    }


    /**
     * Returns default config
     * @return array
     */
    private function getDefaultConfig()
    {
        return [
            'class' => 'radio btn-group btn-group-yesno',
            'options' => ['1' => 'Yes', '0' => 'No'],
            'default' => '0'
        ];
    }


    /**
     * Renders and returns HTML code of field
     * @return string HTML
     */
    public function render($id = null, $data=null)
    {
        return $this->renderField();
    }

}