<?php
/**
 * @author Konstantin Kutsevalov
 * @email <mail@art-prog.ru>
 */

namespace adamasantares\jxmvc;

if (!class_exists('\adamasantares\html\Tag')) {
    require_once __DIR__ . '/../html/Tag.php';
}

use \JHtml;
use \JText;
use \adamasantares\html\Tag;
//if (PHP_VERSION_ID >= 506000) {
    //use function \adamasantares\html\tg; // php >= 5.6
//} else {
    function tg($properties, $content = null)
    {
        return \adamasantares\html\tg($properties, $content);
    }
//}


/**
 * Class JxView
 * @package adamasantares\jxmvc
 */
class JxView
{

    /**
     * @var JxController
     */
    public $controller;

    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    /**
     * Returns component name
     */
    public function getComponentName()
    {
        return $this->controller->getComponentName();
    }

    /**
     * Returns component name
     */
    public function getControllerName()
    {
        return isset($_REQUEST['controller']) ? $_REQUEST['controller'] : 'default';
    }

    /**
     * Returns message from User's state
     */
    public function getMessage()
    {
        return $this->controller->getMessage();
    }

    /**
     * Renders view
     * @param string $viewFile
     * @param array $variables
     */
    public function render($viewFile, $variables = [])
    {
        // define variables
        if (is_array($variables) && !empty($variables)) {
            foreach ($variables as $var => $value) {
                ${$var} = $value;
            }
        }
        // render
        include($viewFile);
    }


    /**
     * returns tag for task
     * @return string
     */
    public function formTask()
    {
        return tg('input!hidden$task', '');
    }

    /**
     * returns tag with option parameter from request
     * @return string
     */
    public function formOption()
    {
        $option = isset($_REQUEST['option']) ? $_REQUEST['option'] : $this->getComponentName();
        return tg('input!hidden$option', $option);
    }

    /**
     * returns tag with option parameter from request
     * @return string
     */
    public function formControllerName()
    {
        $controller = isset($_REQUEST['controller']) ? $_REQUEST['controller'] : 'default';
        return tg('input!hidden$controller', $controller);
    }

    public function formToken()
    {
        return \JHtml::_('form.token');
    }

}