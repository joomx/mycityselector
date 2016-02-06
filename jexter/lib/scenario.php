<?php
/**
 * Scenario for getting parameters for extension
 * @author Konstantin Kutsevalov (AdamasAntares) <konstantin@kutsevalov.name>
 * @version 1.0.0 alpha
 * @license GPL v3 (license.txt)
 */

namespace adamasantares\jexter;

return [
    // common parameters
    'common_start' => [
        [
            'name' => '{jex_name}',
            'prompt' => 'Enter a real name of {ext} ["My super {ext}"]',
            'default' => 'My Super Plugin',
            'filter' => '/^[a-zA-Z0-9 ]{4,30}$/i'
        ],
        [
            'name' => '{jex_sysname}',
            'prompt' => 'Enter a system name of {ext} ["{pref}my_super_{ext}"]',
            'default' => 'plg_my_super_plugin',
            'filter' => '/^[_a-z0-9]+$/i'
        ],
        [
            'name' => '{jex_description}',
            'prompt' => 'Enter a description of {ext} (min:6; max:200) ["My {ext} the best of the best"]',
            'default' => 'My {ext} the best of the best',
            'filter' => '/^.{6,200}$/i'
        ],
        [
            'name' => '{jex_author}',
            'prompt' => 'Enter your name ["MyName"]',
            'default' => gethostname(),
            'filter' => '/^.{2,50}$/i'
        ],
        [
            'name' => '{jex_author_email}',
            'prompt' => 'Enter your email (myname@ema.il) [""]',
            'default' => '',
            'filter' => '/^.{5,50}$/i' // no need validation.
        ],
        [
            'name' => '{jex_author_url}',
            'prompt' => 'Enter your website URL (http://www.super-carl.name) [""]',
            'default' => '',
            'filter' => '/^.{0,50}$/i'
        ],
    ],

    // parameters for component
    'component' => [
        [
            'name' => '{jex_one_item}',
            'prompt' => 'Enter the entity name ["Item"]',
            'default' => 'Item',
            'filter' => '/^[^,.!@#$%&*()]{0,20}$/i'
        ],
        [
            'name' => '{jex_items}',
            'prompt' => 'Enter the entity plural name ["Items"]',
            'default' => 'Items',
            'filter' => '/^[^,.!@#$%&*()]{0,20}$/i'
        ],
        [
            'name' => '{menu-icon}',
            'prompt' => 'Select component\'s icon [0]',
            'default' => '0',
            'filter' => '/^[0-9]{1,3}$/',
            'options_title' => '== select ==',
            'options' => function() {
                    return getIconsList('default');
                },
            'value_as_option' => true
        ],
    ],

    // parameters for module
    'module' => [],

    // parameters for plugin
    'plugin' => [
        [
            'name' => '{jex_group}',
            'prompt' => 'Select the group (type) of plugin [0]',
            'default' => '0',
            'filter' => '/^[0-8]{1}$/',
            'options_title' => '== select ==',
            'options' => ['content', 'system', 'search', 'authentication', 'user', 'captcha', 'editors', 'extensions', 'finder'],
            'value_as_option' => true
        ],
    ],

    // parameters for library
    'library' => [],

    // common parameters
    'common_finish' => [
//        [
//            'name' => 'do_config',
//            'prompt' => 'Do you want to create configuration file for this extension? [0]',
//            'default' => '0',
//            'filter' => '/^[0-2]+$/i',
//            'options_title' => '== select ==',
//            'options' => ['no', 'yes, create new config', 'yes, add to exists project config'],
//            'value_as_option' => false
//        ],
//        [
//            'condition' => ['do_config' => '/1/'],
//            'name' => 'config',
//            'prompt' => 'Enter a name for config file ["my_project"]',
//            'default' => 'my_project',
//            'filter' => '/^[a-z0-9_]+$/i',
//        ],
//        [
//            'condition' => ['do_config' => '/2/'],
//            'name' => 'config',
//            'prompt' => 'Select the project\'s config file [0]',
//            'default' => '0',
//            'filter' => '/^[0-9]{1,3}$/',
//            'options_title' => '== select ==',
//            'options' => function() {
//                    return getProjectsConfig();
//                },
//            'value_as_option' => true
//        ],
    ]
];
