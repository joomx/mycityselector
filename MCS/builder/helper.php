<?php

// colored output
function out($str, $color = '', $background = '')
{
    $colors = [
        'black' => '0;30',
        'dark_gray' => '1;30',
        'blue' => '0;34',
        'light_blue' => '1;34',
        'green' => '0;32',
        'light_green' => '1;32',
        'cyan' => '0;36',
        'light_cyan' => '1;36',
        'red' => '0;31',
        'light_red' => '1;31',
        'purple' => '0;35',
        'light_purple' => '1;35',
        'brown' => '0;33',
        'yellow' => '1;33',
        'light_gray' => '0;37',
        'white' => '1;37',
    ];
    $backgrounds = [
        'black' => '40',
        'red' => '41',
        'green' => '42',
        'yellow' => '43',
        'blue' => '44',
        'magenta' => '45',
        'cyan' => '46',
        'light_gray' => '47',
    ];
    if (!empty($color) && isset($colors[$color])) {
        $str = "\033[" . $colors[$color] . "m" . $str;
    }
    if (!empty($color) && isset($colors[$color])) {
        $str = "\033[" . $colors[$color] . "m" . $str;
    }
    echo $str . "\033[0m";
}


function createDir($dir)
{
    if (!file_exists($dir)) {
        if (!mkdir($dir, 0755)) {
            out("Error: ", 'red');
            out("can't create directory " . $dir . "\n", 'blue');
            return false;
        }
    }
    return true;
}


function copyDir($source, $dest)
{
    if (!createDir($dest)){
        return false;
    }

    foreach (
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST) as $item
    ) {
        if ($item->isDir()) {
            mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
        } else {
            copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
        }
    }
    return true;
}


// http://davidwalsh.name/create-zip-php
function create_zip($files = array(), $destination = '', $overwrite = false)
{
    if (file_exists($destination) && !$overwrite) {
        return false;
    }
    $valid_files = array();
    if (is_array($files)) {
        foreach ($files as $file) {
            if (file_exists($file)) {
                $valid_files[] = $file;
            }
        }
    }
    if (count($valid_files)) {
        $zip = new ZipArchive();
        if ($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
            return false;
        }
        foreach ($valid_files as $file) {
            $zip->addFile($file, $file);
        }
        $zip->close();
        return file_exists($destination);
    } else {
        return false;
    }
}