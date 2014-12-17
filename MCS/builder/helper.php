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


function clearDir($dir){
    $dir = str_replace('//', '/', $dir);
    foreach (glob($dir . '/*') as $file) {
        if(is_dir($file)) {
            clearDir($file);
            @rmdir($file);
        } else {
            if (strpos($file,'.gitignore') === false) {
                unlink($file);
            }
        }
    }
}


/**
 * Zipping directory
 *
 * @param string $sourceDir The file for zipping
 * @param string $destinationFile The Archive file name
 * @param bool $overwrite By default is true
 * @param string $arcRootPath path in archive as root for files
 * @return bool true on success
 */
function zipping($sourceDir = '', $destinationFile = '', $overwrite = true, $arcRootPath = '/')
{
    if (file_exists($destinationFile) && !$overwrite) {
        return false;
    }
    $sourceDir = rtrim($sourceDir, '/\\') . '/';
    $files = glob($sourceDir . '*') + glob($sourceDir . '*.*'); // read files list BEFORE create arc file
    $zip = new ZipArchive();
    $ret = $zip->open($destinationFile, ($overwrite ? ZipArchive::OVERWRITE : ZipArchive::CREATE));
    if ($ret !== TRUE) {
        out('Error! Can\'t create zip file' . "\n", 'red');
        exit;
    } else {
        $count = count($files);
        for ($i=0; $i<$count; $i++) { // foreach work incorrect with array_merge
            $file = $files[$i];
            if (strpos($file,'.gitignore') !== false) {
                continue;
            }
            if (is_dir($file)) {
                $dir = str_replace($sourceDir, $arcRootPath, $file);
                out(" add folder " . $dir . "...", 'light_blue');
                if ($zip->addEmptyDir($dir)){
                    out("ok\n", 'light_blue');
                } else {
                    out("error\n", 'red');
                }
                $subFiles = glob($file . '/*') + glob($file . '/*.*');
                $files = array_merge($files, $subFiles);
                $count = count($files);
            } else {
                $relPath = str_replace($sourceDir, $arcRootPath, $file);
                out(" add file " . $relPath . "...", 'light_blue');
                if ($zip->addFile($file, $relPath)){
                    out("ok\n", 'light_blue');
                } else {
                    out("error\n", 'red');
                }
            }
        }
        $zip->close();
    }
}