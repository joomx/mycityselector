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
use function \adamasantares\html\tg;


/**
 * Class JxView
 * @package adamasantares\jxmvc
 */
class JxView
{

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
        $option = isset($_REQUEST['option']) ? $_REQUEST['option'] : '';
        return tg('input!hidden$option', $option);
    }

    /**
     * returns tag with option parameter from request
     * @return string
     */
    public function formControllerName()
    {
        $option = isset($_REQUEST['controller']) ? $_REQUEST['controller'] : 'default';
        return tg('input!hidden$controller', $option);
    }

    public function formToken()
    {
        return \JHtml::_('form.token');
    }

}