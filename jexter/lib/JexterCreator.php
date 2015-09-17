<?php
/*
 * JEXTER
 * Joomla console package builder
 * @author Konstantin Kutsevalov (AdamasAntares) <mail@art-prog.ru>
 * @version 1.0.0 alpha
 * @license GPL v3 (license.txt)
 */

/**
 * Class JexterCreator
 */
class JexterCreator {

    public static function run($args)
    {
        switch ($args['type']) {
            case 'component': case 'comp':
                self::createComponent($args);
                break;
            case 'plugin': case 'plg':
                self::createPlugin($args);
                break;
            case 'module': case 'mod':
                self::createModule($args);
                break;
        }
    }


    /**
     * Create component
     * @param $args
     */
    public static function createComponent($args)
    {

    }

    public static function createModule($args)
    {

    }

    public static function createPlugin($args)
    {

    }
} 