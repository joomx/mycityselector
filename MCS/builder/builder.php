<?php
/**
 * MCS package builder
 */

// +++ functions +++
require 'helper.php';


// +++ options +++
if (count($argv) < 2) {
    out("Need options!\n", 'blue');
    out("php -f ./builder {version}\n", 'blue');
    out("For example: 'php -f ./builder 1.2.7'\n", 'blue');
    exit;
}

$cdate = date('M Y');
$version = $argv[1];
$myDir = dirname(__FILE__);
$modName = 'mod_mycityselector';
$plgName = 'plg_mycityselector';
$modDir = $myDir . '/../../modules/' . $modName;
$plgDir = $myDir . '/../../plugins/system/' . $plgName;
$copyDir = $myDir . '/src_copy/' . date('dmY_His');
$copyModDir = $copyDir . '/mod';
$copyPlgDir = $copyDir . '/plg';

// +++ COPY SRC +++

if (!createDir($copyDir) || !copyDir($modDir, $copyModDir) || !copyDir($plgDir, $copyPlgDir)){
    exit;
}

// +++ PACKING MODULE +++
out("Packing module...\n", 'green');

// listing files
$files = glob($copyModDir.'/*') + glob($copyModDir.'/*.*');
$scriptFile = '';

// prepare files list
foreach ($files as $k => &$file) {
    $name = basename($file);
    if (is_dir($file)) {
        $file = '<folder>'.$name.'</folder>';
    } else {
        if ($name == $modName . '.php') { // if it's main script of mod
            $file = '<filename module="'.$modName.'">' . $name . '</filename>';
        } elseif ($name == $modName . '.scriptfile.php') { // if it's main script of mod
            $scriptFile = '<scriptfile>'.$name.'</scriptfile>';
            unset($files[$k]);
        } else {
            $file = '<filename>'.$name.'</filename>';
        }
    }
}
$files = $scriptFile . "\n\t<files>\n\t\t" . implode( "\n\t\t", $files) . "\n\t</files>\n";

$xml = file_get_contents($myDir.'/src_copy');


