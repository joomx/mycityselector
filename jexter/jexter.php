<?php
/**
 * JEXTER
 * Joomla console package builder
 * @author Konstantin Kutsevalov (AdamasAntares) <mail@art-prog.ru>
 * @copy Omsk 2015
 * @version 1.0.0 alpha
 * @license GPL v3 (license.txt)
 */

require 'lib/helper.php';
require 'lib/JexterBuilder.php';
require 'lib/JexterCreator.php';


// check extensions
if (!extension_loaded('zip')) {
    out("The Zip php extension not installed!\n", 'red');
    exit;
} elseif (!function_exists('simplexml_load_file')) {
    out("The SimpleXml php extension not installed!\n", 'red');
    exit;
}


// parsing console arguments
$args = parseCliArguments($argv);
$args['myDir'] = dirname(__FILE__); // add Jexter path

if (!empty($args['install'])) {

    // TODO install or reinstall(update) extension from exists files

} elseif ($args['build'] === true) {
    JexterBuilder::make($args);
} elseif (!empty($args['create'])) {

    // TODO create extension and install it to site
    //JexterCreator::createComponent();

}

