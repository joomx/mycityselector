<?php
/**
 * MCS package builder
 */

// +++ functions +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
require 'helper.php';

// check
if (!extension_loaded('zip')) {
    out("The Zip php extension not installed!\n", 'red');
    exit;
}
if (!function_exists('simplexml_load_file')) {
    out("The SimpleXml php extension not installed!\n", 'red');
    exit;
}

// +++ defines +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$version = getVersion();
$ver = str_replace('.', '', $version);
$myDir = dirname(__FILE__);
$modName = 'mod_mycityselector';
$plgName = 'plg_mycityselector';
$modDir = $myDir . '/../modules/' . $modName;
$plgDir = $myDir . '/../plugins/system/' . $plgName;
$copyDir = $myDir . '/src_copy/' . date('dmY_His') . (isset($argv[1]) ? '_' . $argv[1] : '');
$copyModDir = $copyDir . '/mod';
$copyPlgDir = $copyDir . '/plg';
$pkgDir = $myDir . '/package/';
$zipModFile = 'mcs-mod-v' . $ver . '-j25j3x.zip';
$zipPlgFile = 'mcs-plg-v' . $ver . '-j25j3x.zip';
$zipPackageFile = 'mcs-v' . $ver . '-j25j3x-pm.zip';



// +++ COPY SRC +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

if (!createDir($copyDir) || !copyDir($modDir, $copyModDir) || !copyDir($plgDir, $copyPlgDir)){
    exit;
}
clearDir($pkgDir);


// +++ PACKING MODULE +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
out("Packing module ...\n", 'green');

// listing files
$files = glob($copyModDir.'/*') + glob($copyModDir.'/*.*');
$scriptFile = '';
// prepare files list
foreach ($files as $k => &$file) {
    $name = basename($file);
    if (is_dir($file)) {
        $file = ['tag' => 'folder', 'attr' => [], 'value' => $name]; // if directory
    } else {
        // if file
        if ($name == $modName . '.php') { // if it's main script of mod
            $file = ['tag' => 'filename', 'attr' => ['module' => $modName], 'value' => $name];
        } elseif ($name == 'installer.php') { // if it's main script of mod
            $scriptFile = $name;
            unset($files[$k]); // remove script from files list
        } else {
            $file = ['tag' => 'filename', 'attr' => [], 'value' => $name];
        }
    }
}

// update the data in manifest file
$upd = updateManifest($copyModDir . '/mod_mycityselector.xml', [
    'creationDate' => date('M Y'),
    'version' => $version,
    'scriptfile' => $scriptFile,
    'files' => $files
]);

// zip
zipping($copyModDir, $pkgDir . $zipModFile);
out("Done ", 'green');
out("({$zipModFile})\n", 'gray');


// +++ PACKING PLUGIN +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
out("Packing plugin ...\n", 'green');

// listing files
$files = glob($copyPlgDir.'/*') + glob($copyPlgDir.'/*.*');
$scriptFile = '';
// prepare files list
foreach ($files as $k => &$file) {
    $name = basename($file);
    if (is_dir($file)) {
        $file = ['tag' => 'folder', 'attr' => [], 'value' => $name]; // if directory
    } else {
        // if file
        if ($name == $plgName . '.php') { // if it's main script of plugin
            $file = ['tag' => 'filename', 'attr' => ['plugin' => $plgName], 'value' => $name];
        } elseif ($name == 'installer.php') { // if it's main script of mod
            $scriptFile = $name;
            unset($files[$k]); // remove script from files list
        } else {
            $file = ['tag' => 'filename', 'attr' => [], 'value' => $name];
        }
    }
}

// update the data in manifest file
$upd = updateManifest($copyPlgDir.'/plg_mycityselector.xml', [
    'creationDate' => date('M Y'),
    'version' => $version,
    'scriptfile' => $scriptFile,
    'files' => $files
]);

// zip
zipping($copyPlgDir, $pkgDir . $zipPlgFile);
out("Done ", 'green');
out("({$zipPlgFile})\n", 'gray');


// +++ PACKING TO PACKAGE +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
out("Packing package ...\n", 'green');

// update the data in manifest file of package
$upd = updateManifest(
    $myDir.'/pkg_mycityselector.xml',
    [
        'creationDate' => date('M Y'),
        'version' => $version,
        'files' => [
            ['tag' => 'file', 'attr' => ['type' => 'module', 'id' => $modName, 'client' => 'site'], 'value' => $zipModFile],
            ['tag' => 'file', 'attr' => ['type' => 'plugin', 'id' => $plgName, 'group' => 'system'], 'value' => $zipPlgFile],
        ]
    ],
    $pkgDir . 'pkg_mycityselector.xml'
);


zipping($pkgDir, $pkgDir . $zipPackageFile);
unlink($pkgDir . $zipModFile);
unlink($pkgDir . $zipPlgFile);
unlink($pkgDir.'pkg_mycityselector.xml');
out("Done ", 'green');
out("({$zipPackageFile})\n", 'gray');
out("Building complete.\n", 'green');