<?php
require_once('administrator/components/com_mycityselector/migrations/lib/migra.php');
define('_JEXEC', 1);

if (file_exists(__DIR__ . '/defines.php'))
{
    include_once __DIR__ . '/defines.php';
}

if (!defined('_JDEFINES'))
{
    define('JPATH_BASE', __DIR__);
    require_once JPATH_BASE . '/includes/defines.php';
}

require_once JPATH_BASE . '/includes/framework.php';

//SCRIPT START
const PATH_TO_MIGRATIONS = 'administrator/components/com_mycityselector/migrations/';

$migrations = scandir(PATH_TO_MIGRATIONS);

$migrationForExecute = $argv[1];

if(!in_array($migrationForExecute, $migrations)) {
    echo "Migration \"{$migrationForExecute}\" not found" . PHP_EOL;
    exit;
}

$db = JFactory::getDbo();

require_once(PATH_TO_MIGRATIONS.$migrationForExecute);

$classes = get_declared_classes();
$classOfMigrationForExecute = end($classes);

$migrationObj = new $classOfMigrationForExecute($db);
$migrationObj->apply();