<?php
/**
 * @author Konstantin Kutsevalov
 * @email <mail@art-prog.ru>
 */

namespace adamasantares\jxforms;

/**
 * Wrap for JFormFieldEditor
 * Class EditorInput
 * @package adamasantares\jxforms
 */
class EditorInput extends \JFormFieldEditor {

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
     *      'description' => "",
     *      'width' => "600",
     *      'height' => "400"
     *  ]
     */
    public function __construct($name, $label, $value = '', $config = [])
    {
        $jform = new \JForm($name);
        parent::__construct($jform);
        $this->name = $name;
        $this->value = $value;
        $this->_config = array_merge($this->getDefaultConfig(), $config);
        // configure xml
        $xml = '<'.'?xml version="1.0"?'.'>'."\n".'<doc>'
            . '<field name="' . $name . '" '
			. 'type="editor" '
			. 'label="' . $label . '" buttons="true" /></doc>';
        $xml = simplexml_load_string($xml);
        $this->element = $xml->field;
        foreach ($this->_config as $param => $val) {
            $this->__set($param, $val);
        }
    }


    /**
     * Returns default config
     * @return array
     */
    private function getDefaultConfig()
    {
        return [
            'class' => '',
            'width' => '800',
            'height' => '500'
        ];
    }


    /**
     * Renders and returns HTML code of field
     * @return string HTML
     */
    public function render()
    {
        return $this->renderField();
    }
} 