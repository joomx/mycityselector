<?php
/**
 * MyCitySelector
 * @author Konstantin Kutsevalov
 * @version 1.0.0
 */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

require_once dirname(__FILE__) . '/helpers/mvc/JxRouter.php';


// todo only for tests
if (!class_exists('\adamasantares\html\Tag')) {
    require_once __DIR__ . '/helpers/html/Tag.php';
}

use \adamasantares\html\Tag;
use function \adamasantares\html\tg;


echo "===";
echo tg('input!text$option', '<div>Привет! Че каво?</div>');
echo "===";
echo tg('.super.puper.element', 'Дивный элемент');
echo "===";
echo tg(['a.button', 'href' => '/url/path'], tg(['img.my-img', 'alt' => 'just-a-link'], '/image.png'));
echo "===";



// todo uncomment
// \adamasantares\jxmvc\JxRouter::executeController(dirname(__FILE__), 'Mycityselector');