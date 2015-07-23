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
}


/**
 * Читает версию расширения из конфига или командной строки
 * @return string
 */
function getVersion(){
    global $argv;
    $version = '1.0.0';
    $pattern = '/\d+\.\d+\.\d+/';
    if(is_file(dirname(__FILE__) .'/this_version.cfg')) {
        $content = file_get_contents(dirname(__FILE__) .'/this_version.cfg');
        $version = preg_match($pattern, $content) ? $content : $version;
    }
    foreach ($argv as $option) {
        if (preg_match($pattern, $option)) {
            $version = $option;
            break;
        }
    }
    return $version;
}


/**
 * Создает директорию
 * @param $dir
 * @return bool
 */
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


/**
 * Копирует директорию вместе с файлами
 * @param String $source Путь до копируемой директории
 * @param String $dest Путь для копирования
 * @return bool
 */
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


/**
 * Удаляет все файлы из указанной директории
 * @param String $dir
 */
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
 * Загружает xml файл и заменяет в нем данные на указанные в массиве
 * @param String $file Путь до файла
 * @param Array $data Массив данных для замены. Например: ['license' => 'GNU/GPL'], где
 *   ключ - это имя тега,
 *   значение - содержимое для тега
 * @param String $destFile Файл для записи результата. Если не указан, результат сохраняется в файл источник.
 */
function updateManifest($file, $data = [], $destFile=''){
    if (is_file($file)) {
        $xml = simplexml_load_file($file);
        if (is_object($xml)) {
            foreach ($data as $tag => $val) {
                $element = $xml->xpath('/extension/' . $tag); // ищем требуемый элемент
                if (empty($element)) {
                    // элемент не найден, его нужно добавить
                    $element = $xml->addChild($tag, '');
                } else {
                    $element = $element[0]; // берем первый из найденных элементов (корневые элементы уникальны)
                }
                // записываем в элемент новое значение или добавляем новые элементы
                if (is_string($val)) {
                    $element->{0} = $val; // присваиваем новое значение
                } elseif (is_array($val)) {
                    $element->{0} = ''; // очищаем элемент
                    if (isset($val['tag'])) {
                        // один элемент
                        addXmlChild($element, $val['tag'], $val['value'], @$val['attr']);
                    } elseif (isset($val[0]['tag'])) {
                        // несколько элементов
                        foreach ($val as $newTag) {
                            addXmlChild($element, $newTag['tag'], $newTag['value'], @$newTag['attr']);
                        }
                    }
                }
            }
            if (empty($destFile)) {
                $destFile = $file;
            }
            if ($xml->asXML($destFile)) {
                return true;
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
function addXmlChild($elem, $name, $value, $attributes=[]) {
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
                out(" add folder " . $dir . " ... ", 'light_blue');
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
                out(" add file " . $relPath . " ... ", 'light_blue');
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