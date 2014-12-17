<?php
/**
 * MCS package builder
 */

// +++ functions +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
require 'helper.php';


// +++ options +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
if (count($argv) < 2) {
    out("Need options!\n", 'blue');
    out("php -f ./builder {version}\n", 'blue');
    out("For example: 'php -f ./builder 1.2.7'\n", 'blue');
    exit;
}

// check
if (!extension_loaded('zip')) {
    out("The Zip php extension not installed!\n", 'red');
    exit;
}

// +++ defines +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$version = $argv[1];
$ver = str_replace('.', '', $version);
$myDir = dirname(__FILE__);
$modName = 'mod_mycityselector';
$plgName = 'plg_mycityselector';
$modDir = $myDir . '/../../modules/' . $modName;
$plgDir = $myDir . '/../../plugins/system/' . $plgName;
$copyDir = $myDir . '/src_copy/' . date('dmY_His');
$copyModDir = $copyDir . '/mod';
$copyPlgDir = $copyDir . '/plg';
$destDir = $myDir . '/dest/';
$packagesDir = $myDir . '/packages/';
$zipModFile = 'mycityselector-mod-j2532-v' . $ver . '.zip';
$zipPlgFile = 'mycityselector-plg-j2532-v' . $ver . '.zip';
$zipPackageFile = 'mycityselector-j2533-v' . $ver . '.zip';



// +++ COPY SRC +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

if (!createDir($copyDir) || !copyDir($modDir, $copyModDir) || !copyDir($plgDir, $copyPlgDir)){
    exit;
}
clearDir($destDir);


// +++ PACKING MODULE +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
out("Packing module...\n", 'green');

// listing files
$files = glob($copyModDir.'/*') + glob($copyModDir.'/*.*');
$scriptFile = '';

// prepare files list
foreach ($files as $k => &$file) {
    $name = basename($file);
    if (is_dir($file)) {
        // if directory
        $file = '<folder>' . $name . '</folder>';
    } else {
        // if file
        if ($name == $modName . '.php') { // if it's main script of mod
            $file = '<filename module="' . $modName . '">' . $name . '</filename>';
        } elseif ($name == $modName . '.scriptfile.php') { // if it's main script of mod
            $scriptFile = '<scriptfile>' . $name . '</scriptfile>';
            unset($files[$k]); // remove script from files list
        } else {
            $file = '<filename>' . $name . '</filename>';
        }
    }
}
$files = $scriptFile . "\n\t<files>\n\t\t" . implode( "\n\t\t", $files) . "\n\t</files>\n";

// get xml template of manifest file
$xml = file_get_contents($myDir.'/src_copy/manifest/mod_mycityselector.xml');
// replace marks
$xml = str_replace(
    ['{cdate}', '{version}', '{files}'],
    [date('M Y'), $version, $files],
    $xml
);
// save xml
file_put_contents($copyModDir.'/mod_mycityselector.xml', $xml);
// zip
$zipFile = $destDir . $zipModFile;
zipping($copyModDir, $zipFile);
out("Done\n", 'green');


// +++ PACKING PLUGIN +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
out("Packing plugin...\n", 'green');

// listing files
$files = glob($copyPlgDir.'/*') + glob($copyPlgDir.'/*.*');
$scriptFile = '';

// prepare files list
foreach ($files as $k => &$file) {
    $name = basename($file);
    if (is_dir($file)) {
        // if directory
        $file = '<folder>' . $name . '</folder>';
    } else {
        // if file
        if ($name == $plgName . '.php') { // if it's main script of mod
            $file = '<filename plugin="' . $plgName . '">' . $name . '</filename>';
        } elseif ($name == $plgName . '.scriptfile.php') { // if it's main script of mod
            $scriptFile = '<scriptfile>' . $name . '</scriptfile>';
            unset($files[$k]); // remove script from files list
        } else {
            $file = '<filename>' . $name . '</filename>';
        }
    }
}
$files = $scriptFile . "\n\t<files>\n\t\t" . implode( "\n\t\t", $files) . "\n\t</files>\n";

// get xml template of manifest file
$xml = file_get_contents($myDir.'/src_copy/manifest/plg_mycityselector.xml');
// replace marks
$xml = str_replace(
    ['{cdate}', '{version}', '{files}'],
    [date('M Y'), $version, $files],
    $xml
);
// save xml
file_put_contents($copyPlgDir.'/plg_mycityselector.xml', $xml);
// zip
$zipFile = $destDir . $zipPlgFile;
zipping($copyPlgDir, $zipFile);
out("Done\n", 'green');


// +++ PACKING TO PACKAGE +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
out("Packing package...\n", 'green');
// get xml template of manifest file
$xml = file_get_contents($myDir.'/src_copy/manifest/pkg_mycityselector.xml');
// replace marks
$xml = str_replace(
    ['{cdate}', '{version}', '{mod_name}', '{mod_zip}', '{plg_name}', '{plg_zip}'],
    [date('M Y'), $version, $modName, $zipModFile, $plgName, $zipPlgFile],
    $xml
);
// save xml
file_put_contents($destDir . 'pkg_mycityselector.xml', $xml);

$zipFile = $destDir . $zipPackageFile;
zipping($destDir, $zipFile);
unlink($destDir . $zipModFile);
unlink($destDir . $zipPlgFile);
unlink($destDir.'/pkg_mycityselector.xml');
out("Done\n", 'green');