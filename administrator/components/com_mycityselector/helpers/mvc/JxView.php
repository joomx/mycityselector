<?php
/**
 * @author Konstantin Kutsevalov
 * @email <mail@art-prog.ru>
 */

namespace adamasantares\jxmvc;

if (class_exists('\adamasantares\jxmvc\JxView')) return;

if (!class_exists('\adamasantares\html\Tag')) {
    require_once __DIR__ . '/helpers/html/Tag.php';
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

    public function __construct($viewPath, $variables = [])
    {

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

    public function formToken()
    {
        return \JHtml::_('form.token');
    }

}