<?php
/**
 * @author Konstantin Kutsevalov
 * @email <mail@art-prog.ru>
 */

namespace adamasantares\jxmvc;

if (class_exists('\adamasantares\jxmvc\JxRouter')) return;

/**
 * Controllers Router for components admin side
 *
 * Class JxRouter
 * @package adamasantares\jxmvc
 */
class JxRouter
{

    /**
     * Load and execute controller by request
     * @param string $root Root folder of component
     * @param string $componentName ComponentName (prefix of all Controllers names)
     * @param string $defaultTask
     * @param string $defaultController
     * @throws \Exception
     */
    public static function executeController($root, $componentName, $defaultTask = 'default', $defaultController = 'default')
    {
        //$controller = JControllerLegacy::getInstance('mycityselector');
        $input = \JFactory::getApplication()->input;
        $task = $input->getCmd('task', $defaultTask);

        $controllerName = ucfirst($task) . 'Controller';
        $path = rtrim($root, '/') . '/controllers/' . $controllerName . '.php';
        if (!is_file($path)) {
            $path = rtrim($root, '/') . '/controllers/' . ucfirst($defaultController) . 'Controller.php';
        }
        if (is_file($path)) {
            require_once $path;
        } else {
            throw new \Exception("Controller not found: {$controllerName}");
        }
        if (!class_exists($controllerName)) {
            throw new \Exception("Controller not found: {$controllerName}");
        }
        $controller = new $controllerName($root); /* @var $controller JxController */
        $controller->execute();
    }

}