<?php
/**
 * @author Konstantin Kutsevalov (Adamas Antares)
 * @email <mail@art-prog.ru>
 */

namespace adamasantares\JxForms;

// extended classes
require_once __DIR__ . '/TextInput.php';
require_once __DIR__ . '/EditorInput.php';
require_once __DIR__ . '/CheckboxInput.php';
require_once __DIR__ . '/ChosenInput.php';
require_once __DIR__ . '/RadioInput.php';


/**
 * Class Field
 * @package adamasantares\JxForms
 */
class JxField {


    /**
     * @see \adamasantares\JxForms\CheckboxInput::__construct()
     * @param $name
     * @param $label
     * @param string $value
     * @param array $config
     * @return string
     */
    public static function text($name, $label, $value = '', $config = [])
    {
        $field = new TextInput($name, $label, $value, $config);
        return $field->render();
    }


    /**
     * @see \adamasantares\JxForms\EditorInput::__construct()
     * @param string $name
     * @param string $label
     * @param string $value
     * @param array $config
     * @return string
     */
    public static function editor($name, $label, $value = '', $config = [])
    {
        $field = new EditorInput($name, $label, $value, $config);
        return $field->render();
    }


    /**
     * @see \adamasantares\JxForms\CheckboxInput::__construct()
     * @param string $name
     * @param string $label
     * @param boolean $status Checked/unchecked
     * @param array $config
     * @return string
     */
    public static function checkbox($name, $label, $status = false, $config = [])
    {
        $field = new CheckboxInput($name, $label, $status, $config);
        return $field->render();
    }


    /**
     * @see \adamasantares\JxForms\RadioInput::__construct()
     * @param string $name
     * @param string $label
     * @param string $value
     * @param array $config
     * @return string
     */
    public static function radio($name, $label, $value, $config = [])
    {
        $field = new RadioInput($name, $label, $value, $config);
        return $field->render();
    }


    /**
     * @see \adamasantares\JxForms\ChosenInput::__construct()
     * @param $name
     * @param $label
     * @param string $value
     * @param array $config
     * @return string
     */
    public static function chosen($name, $label, $value, $config = [])
    {
        $field = new ChosenInput($name, $label, $value, $config);
        return $field->render();
    }




}