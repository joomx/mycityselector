<?php
/**
 * Вспомогательные функции для builder
 */


/**
 * Вывод в консоль с поддержкой цвета
 * @param String $str Строка
 * @param String $color Название цвета для текста
 * @param String $background Название цвета для фона
 */
function out($str, $color = '', $background = '')
{
    if (php_sapi_name() == "cli") {
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
        if (!empty($background) && isset($backgrounds[$background])) {
            $str = "\033[" . $backgrounds[$background] . "m" . $str;
        }
        echo $str . "\033[0m";
    } else {
        $GLOBALS['jexter_web_global_output'][] = $str;
    }
}


/**
 * Returns output of out
 * @return array
 */
function getOutput()
{
    if (isset($GLOBALS['jexter_web_global_output'])) {
        return $GLOBALS['jexter_web_global_output'];
    }
    return [];
}


/**
 * Parsing CLI arguments
 * @param array $argv $ARGV
 * @param array $default Default values with rules of conversion. For example "command arg1 arg2 arg3=val3"
 *      will be returned as $args[ 0 => arg1, 1 => arg2, arg3 => val3 ]. But with this argument value
 *      [ 0 => [name => file, default => val1], 1 => [name => line] ]
 *      we can convert it to $args[ file => arg1, line => arg2, arg3 => val3 ].
 *      If there is no any arguments in $argv, then will be returned values from "default" keys.
 * @return array
 */
function prepareArguments($argv, $default = [])
{
    $arguments = [];
    foreach ($default as $key => $data) {
        if (!empty($data)) {
            $value = empty($default[$key]['default']) ? '' : $default[$key]['default'];
            $arguments[$data['name']] = $value;
        }
    }
    if (!empty($argv)) {
        foreach ($argv as $key => $arg) {
            list($param, $value) = explode('=', $arg . '=', 2);
            $value = trim($value, '"');
            if (empty($value)) { // if passed simple argument ["./command arg_value"]
                if (isset($default[$key])) {
                    // if exists default param-value for this index(key)
                    $param = $default[$key]['name'];
                    $arguments[$param] = $arg;
                } else {
                    // no default value
                    $arguments[$key] = $arg;
                }
            } else { // if passed key-value pair ["./command arg=value"]
                $arguments[$param] = $value;
            }
        }
    }
    // project config path
    $arguments['config'] = empty($arguments['config']) ? 'project/project.json' : 'project/' . $arguments['config'] . '.json';
    return $arguments;
}


function normalizePath($path)
{
    $parts = array(); // Array to build a new path from the good parts
    $path = str_replace('\\', '/', $path); // Replace backslashes with forwardslashes
    $path = preg_replace('/\/+/', '/', $path); // Combine multiple slashes into a single slash
    $segments = explode('/', $path); // Collect path segments
    $test = ''; // Initialize testing variable
    foreach ($segments as $segment) {
        if ($segment != '.') {
            $test = array_pop($parts);
            if (is_null($test))
                $parts[] = $segment;
            else if ($segment == '..') {
                if ($test == '..')
                    $parts[] = $test;

                if ($test == '..' || $test == '')
                    $parts[] = $segment;
            } else {
                $parts[] = $test;
                $parts[] = $segment;
            }
        }
    }
    return implode('/', $parts);
}


/**
 * @param $dir
 * @return bool
 */
function createDir($dir)
{
    if (!file_exists($dir)) {
        // create all path levels for checking each directory from path
        $tokens = explode('/', trim($dir, '/'));
        $length = count($tokens);
        for ($i = 0; $i < $length; $i++) {
            $path = '/' . implode('/', array_slice($tokens, 0, $i + 1));
            //out("create dir: {$path}\n", "red");
            if (!file_exists($path) && !mkdir($path, 0755)) {
                out("Error: can't create directory {$path} of {$dir}\n", 'red');
                return false;
            }
        }
    }
    return true;
}


/**
 * Copy directory with files
 * @param String $source
 * @param String $dest
 * @return bool
 */
function copyDir($source, $dest)
{
    if (!createDir($dest)) {
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


/**
 * Delete files in some directory
 * @param String $dir
 */
function clearDir($dir)
{
    $dir = str_replace('//', '/', $dir);
    $sys = ['/bin', '/boot', '/build', '/cdrom', '/dev', '/etc', '/lib', '/lib64', '/lost+found', '/media',
        '/mnt', '/opt', '/proc', '/root', '/run', '/sbin', '/srv', '/sys', '/tmp', '/usr'];
    foreach ($sys as $v) {
        if (substr($dir, 0, strlen($v)) == $v) {
            out(" Error: you can't clear system directory!\n", "red");
            return false;
        }
    }
    foreach (glob($dir . '/*') as $file) {
        if (is_dir($file)) {
            clearDir($file);
            @rmdir($file);
        } else {
            if (strpos($file, '.gitignore') === false) {
                unlink($file);
            }
        }
    }
}


/**
 * Updates manifest file by passed values
 * @param String $file Path to original manifest file
 * @param Array $data <p>
 * Data for update. All keys is xpath of xml elements except '{marks}' key.
 * Example:
 * [
 *      'license' => 'GNU/GPL',  // key => value
 *      'install/sql' => [       // key => child_tag_details
 *          'tag' => 'file',
 *          'attr' => [
 *              'driver' => 'mysql',
 *              'charset' => 'utf8'
 *          ],
 *          'value' => 'install.sql'
 *      ],
 *      'files' => [            // key => child_tags_details_list
 *          ['tag' => 'filename', 'value' => 'index.html'],
 *          ['tag' => 'filename', 'value' => 'sitemapjen.php'],
 *      ],
 *      ...etc...
 * ]
 * </p>
 * @param String $destinationFile [optional] If argument passed, then changes will saved in this file. By default will be updated original file.
 * @return bool
 */
function updateManifest($file, $data = [], $destinationFile = '')
{
    $marks = [];
    if (!empty($data['{marks}'])) {
        $marks = $data['{marks}'];
        unset($data['{marks}']);
    }
    if (is_file($file)) {
        $xml = simplexml_load_file($file);
        if (is_object($xml)) {
            foreach ($data as $tag => $val) {
                $element = $xml->xpath('/extension/' . $tag);
                if (empty($element)) {
                    $element = $xml->addChild($tag, '');
                } else {
                    $element = $element[0];
                }
                if (is_string($val)) {
                    $element->{0} = $val;
                } elseif (is_array($val)) {
                    $element->{0} = '';
                    if (isset($val['tag'])) {
                        $attr = isset($val['attr']) ? $val['attr'] : [];
                        addXmlChild($element, $val['tag'], $val['value'], $attr);
                    } elseif (isset($val[0]['tag'])) {
                        foreach ($val as $newTag) {
                            $attr = isset($newTag['attr']) ? $newTag['attr'] : [];
                            addXmlChild($element, $newTag['tag'], $newTag['value'], $attr);
                        }
                    }
                }
            }
            if (empty($destinationFile)) {
                $destinationFile = $file;
            }
            // replace {marks}
            if (!empty($marks)) {
                $xml = $xml->asXML();
                foreach ($marks as $_mark => $_value) {
                    $xml = str_replace($_mark, $_value, $xml);
                }
                if (file_put_contents($destinationFile, $xml) !== false) {
                    return true;
                }
            } else {
                if ($xml->asXML($destinationFile) !== false) {
                    return true;
                }
            }
        }
    }
    return false;
}


/**
 * Обертка для добавления элемента одновременно с атрибутами
 * @param SimpleXMLElement $elem
 * @param String $name
 * @param String $value
 * @param Array $attributes ['attr' => 'value', 'attr2' => 'value2']
 */
function addXmlChild($elem, $name, $value, $attributes = [])
{
    $new = $elem->addChild($name, $value);
    if (!empty($attributes) && is_array($attributes)) {
        foreach ($attributes as $attr => $val) {
            $new->addAttribute($attr, $val);
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
        out('  Error! Can\'t create zip file ' . $destinationFile . "\n", 'red');
        return false;
    } else {
        $count = count($files);
        for ($i = 0; $i < $count; $i++) { // foreach work incorrect with array_merge
            $file = $files[$i];
            if (strpos($file, '.gitignore') !== false) {
                continue;
            }
            if (is_dir($file)) {
                $dir = str_replace($sourceDir, $arcRootPath, $file);
                out("     add folder " . $dir . " ... ", 'light_blue');
                if ($zip->addEmptyDir($dir)) {
                    out("ok\n", 'light_blue');
                } else {
                    out("error\n", 'red');
                }
                $subFiles = glob($file . '/*') + glob($file . '/*.*');
                $files = array_merge($files, $subFiles);
                $count = count($files);
            } else {
                $relPath = str_replace($sourceDir, $arcRootPath, $file);
                out("     add file " . $relPath . " ... ", 'light_blue');
                if ($zip->addFile($file, $relPath)) {
                    out("ok\n", 'light_blue');
                } else {
                    out("error\n", 'red');
                }
            }
        }
        $zip->close();
    }
    return true;
}