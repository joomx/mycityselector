<?php
namespace joomx\mcs\plugin\helpers;

defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');

use Joomla\CMS\Factory;
use Joomla\Event\Event;


/**
 * Абстракция от нативных методов джумлы
 * Потому что нативные, инициализируются сильно позже, чем создается объект приложения.
 *
 * Class McsEventDispatcher
 *  @package joomx\mcs\plugin\helpers
 */
class McsEventDispatcher
{

    /**
     * @var McsEventDispatcher|null
     */
    protected static $instance = null;

    private $listeners = [];


    /**
     * McsEventDispatcher constructor.
     */
    protected function __construct()
    {

    }


    /**
     * @return McsEventDispatcher|null
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    /**
     * @param string $eventName
     * @param Closure $closure
     */
    public function listenEvent(string $eventName, \Closure $closure)
    {
        if (!isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = [];
        }
        $this->listeners[$eventName][] = $closure;
    }


    /**
     * @param string $eventName
     * @param array $data
     */
    public function triggerEvent(string $eventName, array $data)
    {
        if (isset($this->listeners[$eventName])) {
            foreach ($this->listeners[$eventName] as $listener) {
                if ($listener instanceof \Closure) {
                    $data = \call_user_func($listener, $data);
                } else {
                    $data = \call_user_func([$listener, $eventName], $data);
                }
            }
        }
        return $data;
    }

}