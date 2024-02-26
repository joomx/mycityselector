<?php
/*
 * JEXTER
 * Joomla console package builder
 * @author Konstantin Kutsevalov (AdamasAntares) <konstantin@kutsevalov.name>
 * @version 1.0.0 alpha
 * @license GPL v3 (license.txt)
 */
namespace adamasantares\jexter;


if (!defined('JEXTER_DIR')) {
    define('JEXTER_DIR', realpath(__DIR__ . '/../'));
}


/**
 * Class JexterCreator
 */
class JexterCreator {

    public static function run($params)
    {
        $params = self::prepareParameters($params);
        switch ($params['type']) {
            case 'component': case 'comp':
                self::createComponent($params);
                break;
            case 'plugin': case 'plg':
                self::createPlugin($params);
                break;
            case 'module': case 'mod':
                self::createModule($params);
                break;
            case 'library': case 'lib':
                self::createLibrary($params);
                break;
        }
    }


    /**
     * Prepare parameters for creators methods
     * @param $params
     * @return mixed
     */
    private static function prepareParameters($params)
    {
        // Name for classes mod_articles_news -> ModArticlesNews : {jex_classname} и _JEX_CLASSNAME_
        if (!empty($params['{jex_sysname}'])) {
            $params['_JEX_SYSNAME_'] = $params['{jex_sysname}'] = strtolower($params['{jex_sysname}']);
            $name = str_replace('com_', '', $params['{jex_sysname}']); // remove COM_ prefix for component
            $params['{jex_shortname}'] = $name;
            $params['{jex_classname}'] = $params['_JEX_CLASSNAME_'] = getCamel($name);
            // Prefix for i18n T_JEX
            $params['T_JEX'] = strtoupper($params['{jex_sysname}']);
        }
        // group for plugin
        if (!empty($params['{jex_group}'])) {
            $params['_JEX_GROUP_'] = ucfirst($params['{jex_group}']);
        }
        // table for component
        if (!empty($params['{jex_one_item}']) && !empty($params['{jex_classname}'])) {
            $tableName = strtolower($params['{jex_classname}']) . '_' . strtolower($params['{jex_one_item}']);
            $params['{jex_table_name}'] = str_replace(' ', '_', $tableName);
            // name for entity
            $params['_JEX_ITEM_MODEL_'] = getCamel($params['{jex_one_item}']); // for model class name
            $params['{jex_item_model}'] = strtolower($params['_JEX_ITEM_MODEL_']); // for model file name
            // name for view
            $params['_JEX_ITEMS_VIEW_'] = getCamel($params['{jex_items}']); // for view class name
            $params['{jex_items_view}'] = strtolower($params['_JEX_ITEMS_VIEW_']); // for view file name
        }
        $params['{jex_date}'] = date('m-Y');

        // todo something else

        return $params;
    }


    /**
     * Replacing marks in files content
     * @param $files
     * @param $params
     */
    public static function replaceMarksInFiles($files, $params)
    {
        $marks = $values = [];
        foreach ($params as $name => $value) {
            if (substr($name, 0, 1) == '{' || substr($name, 0, 1) == '_' || substr($name, 0, 2) == 'T_') {
                $marks[] = $name;
                $values[] = $value;
            }
        }
        foreach ($files as $file) {
            if (is_file($file) && is_readable($file)) {
                $content = str_replace($marks, $values, file_get_contents($file));
                file_put_contents($file, $content);
            }
        }
    }


    public static function replaceMarksInFilesNames($files, $params)
    {
        foreach ($files as $file) {
            $toRename = false;
            foreach (['jex_sysname', 'jex_shortname', 'jex_item_model', 'jex_items_view'] as $code) {
                if (stripos(basename($file), $code) !== false) {
                    $toRename = true;
                }
            }
            if ($toRename) {
                $from = ['jex_sysname'];
                $to = [$params['{jex_sysname}']];
                if (!empty($params['{jex_group}'])) {
                    $from[] = 'jex_group';
                    $to[] = $params['{jex_group}'];
                }
                if (!empty($params['{jex_shortname}'])) {
                    $from[] = 'jex_shortname';
                    $to[] = $params['{jex_shortname}'];
                }
                if (!empty($params['{jex_item_model}'])) {
                    $from[] = 'jex_item_model';
                    $to[] = $params['{jex_item_model}'];
                }
                if (!empty($params['{jex_items_view}'])) {
                    $from[] = 'jex_items_view';
                    $to[] = $params['{jex_items_view}'];
                }
                rename($file, str_replace($from, $to, $file));
            }
        }
    }


    /**
     * Creates component
     * @param $params
     * @return bool
     */
    public static function createComponent($params)
    {
        // a component will be created with simple structure: one model, one view and one template
        out("Start\n", 'green');
        out("  - cloning component template...\n", 'cyan');
        $tmpDirectory = JEXTER_DIR . '/src_copy/tmp' . date('dmyHis');
        // copy files to copyDir
        copyDir(JEXTER_DIR . '/data/code-templates/component', $tmpDirectory);
        // scan folder
        $files = scanDir($tmpDirectory);
        out("  - updating file's content by parameters...\n", 'cyan');
        // replace marks in files
        self::replaceMarksInFiles($files, $params);
        // and in files names
        self::replaceMarksInFilesNames($files, $params);
        // create installer (zip)
        out("  - packing ...\n", 'cyan');
        $zipFile = JEXTER_DIR . '/extensions/' . $params['{jex_sysname}'] . '.zip';
        if (zipping($tmpDirectory, $zipFile)) {
            out("    done ", 'green');
        } else {
            out("    fail saving {$zipFile}\n", 'red');
            dropDir($tmpDirectory); // delete copy
            return false;
        }
        // delete copy
        dropDir($tmpDirectory);
        // done
        out("Your new extension is {$zipFile}\n", 'green');
        out("Finish\n", 'green');
        return true;
    }


    public static function createModule($params)
    {

    }


    /**
     * Create plugin from template
     * @param $params
     * @return bool
     */
    public static function createPlugin($params)
    {
        out("Start\n", 'green');
        out("  - cloning plugin template...\n", 'cyan');
        $tmpDirectory = JEXTER_DIR . '/src_copy/tmp' . date('dmyHis');
        // copy files to copyDir
        copyDir(JEXTER_DIR . '/data/code-templates/plugin', $tmpDirectory);
        // scan folder
        $files = scanDir($tmpDirectory);
        out("  - updating file's content by parameters...\n", 'cyan');
        // replace marks in files
        self::replaceMarksInFiles($files, $params);
        // and in files names
        self::replaceMarksInFilesNames($files, $params);
        // create installer (zip)
        out("  - packing ...\n", 'cyan');
        $zipFile = JEXTER_DIR . '/extensions/' . $params['{jex_sysname}'] . '.zip';
        if (zipping($tmpDirectory, $zipFile)) {
            out("    done ", 'green');
        } else {
            out("    fail saving {$zipFile}\n", 'red');
            dropDir($tmpDirectory); // delete copy
            return false;
        }
        // delete copy
        dropDir($tmpDirectory);
        // done
        out("Your new extension is {$zipFile}\n", 'green');
        out("Finish\n", 'green');
        return true;
    }

    public static function createLibrary($params)
    {

    }
} 