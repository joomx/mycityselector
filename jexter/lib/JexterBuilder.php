<?php
/**
 * Jexter builder
 */

class JexterBuilder {

    public static function make($args)
    {
        // load project.json
        $config = [];
        if (!self::loadProjectJson($args['myDir'], $config)) {
            exit(1);
        }
        $config = self::preparePath($config, $args);

        // read extension items
        $extensions = self::getExtensionsData($config);

        // create extensions packages (zip)
        foreach ($extensions as &$ext) {
            switch ($ext['type'][0]) {
                case 'component':
                    $ext['pkg'] = self::buildComponent($ext, $config); break;
                case 'plugin':
                    $ext['pkg'] = self::buildPlugin($ext, $config); break;
                case 'module':
                    $ext['pkg'] = self::buildModule($ext, $config); break;
            }
            if (empty($ext['pkg'])) {
                out("-break-\n", 'red');
                //exit; // todo only while developing
            }
        }

        // if package
        if ($config['type'] === 'package') {
            self::buildPackage($extensions, $config);
        }
    }


    /**
     * Load and parse project.json
     * @param string $myDir Path of builder directory
     * @param array &$config Configuration array (return)
     * @return bool Result of loading
     */
    private static function loadProjectJson($myDir, &$config)
    {
        out(" read config ... ", 'yellow');
        $jsonFile = $myDir . '/project/project.json';
        if (!is_file($jsonFile)) {
            out(" not found project configuration file: {$jsonFile}\n", 'red');
            return false;
        }
        $config = json_decode(file_get_contents($jsonFile), true);
        if (empty($config)) {
            out(" the project configuration file is empty or it contain syntax error.\n", 'red');
            return false;
        }
        // check required options
        foreach (['name', 'siteRoot', 'id', 'type', 'version', 'manifest'] as $option) {
            if (empty($config[$option])) {
                out(" not found '{$option}' option!\n", 'red');
                return false;
            }
        }
        $config['type'] = strtolower($config['type']);
        if ($config['type'] === 'package' && empty($config['packageItems'])) {
            out(" for 'package' extension must be 'package_items' option with list of extensions.\n", 'red');
            return false;
        }
        $config['siteRoot'] = realpath(str_replace('@builder', $myDir, $config['siteRoot']));
        out("ok\n", 'green');
        return true;
    }


    /**
     * Prepare all path and dirs from config
     */
    private static function preparePath($config, $args)
    {
        // create directory for copies of source files of extensions
        if (!empty($config['copy'])) {
            $config['copy'] = normalizePath(str_replace('@builder', $args['myDir'], $config['copy']));
            $config['copy'] .= '/' . $args['copyprefix'] . date('dmY_His');
        } else {
            $config['copy'] = normalizePath($args['myDir'] . '/src_copy/' . $args['copyprefix'] . date('dmY_His'));
        }
        createDir($config['copy']);
        // directory for resulting extension package
        if (!empty($config['destination'])) {
            $config['destination'] = normalizePath(str_replace('@builder', $args['myDir'], $config['destination']));
        } else {
            $config['destination'] = normalizePath($args['myDir'] . '/extension/');
        }
        createDir($config['destination']);
        // other
        foreach (['manifest', 'siteRoot', 'license', 'installer'] as $key) {
            if (!empty($config[$key])) {
                $config[$key] = normalizePath(str_replace('@builder', $args['myDir'], $config[$key]));
            }
        }
        return $config;
    }


    private static function getExtensionsData($config)
    {
        $extensions = [];
        if ($config['type'] === 'package') {
            foreach ($config['packageItems'] as $item) {
                $item['type'] = strtolower($item['type']);
                $item['type'] = explode('/', $item['type']);
                $item['type'][] = '';
                $extensions[] = [
                    'name' => $item['name'],
                    'id' => $item['id'],
                    'type' => $item['type'],
                    'version' => $item['version'],
                    'excludes' => empty($item['excludes']) ? [] : $item['excludes']
                ];
            }
        } else {
            $extensions[] = [
                'name' => $config['name'],
                'id' => $config['id'],
                'type' => $config['type'],
                'version' => $config['version'],
                'manifest' => $config['manifest'],
                'excludes' => empty($config['excludes']) ? [] : $config['excludes']
            ];
        }
        return $extensions;
    }


    /**
     * Build component package by project config
     * @param array $ext configuration of plugin from project.json
     * @param array $config full config
     * @return string|null Path of result package (zip)
     */
    private static function buildComponent($ext, $config) {

        out(" creating {$ext['id']} ... bla bla bla\n", 'yellow');

        return '';
    }


    /**
     * Build plugin package by project config
     * @param array $ext configuration of plugin from project.json
     * @param array $config full config
     * @return string|null Path of result package (zip)
     */
    private static function buildPlugin($ext, $config) {
        out(" creating plugin ({$ext['id']})\n", 'yellow');

        $path = $config['siteRoot'] . '/plugins/' . $ext['type'][1] . '/' . $ext['id'];
        $copyPath = $config['copy'] . '/' . $ext['id'];
        $mainfest = $ext['id'] . '.xml';
        $scriptName = empty($ext['script']) ? $ext['id'] . '.php' : $ext['script'];
        $script = $path . '/' . $scriptName;
        $installerName = empty($ext['installer']) ? 'installer.php' : basename($ext['installer']);
        $zipFile = $config['destination'] . '/' . $ext['id'] . '_v' . str_replace('.', '', $ext['version']) . '.zip';

        out("  - checking plugin exists ... ", 'yellow');
        if (is_dir($path) && is_file($script)) {
            out("ok\n", "green");
            out("  - create source copy ... ", 'yellow');
            if (!copyDir($path, $copyPath)) {
                out("fail\n", 'red');
                return null;
            } else {
                out("ok\n", "green");
            }
            out("  - generate manifest file ... ", 'yellow');
            $files = glob($copyPath . '/*') + glob($copyPath . '/*.*');
            // prepare files list
            foreach ($files as $k => &$file) {
                $name = basename($file);
                if (is_dir($file)) {
                    $file = ['tag' => 'folder', 'attr' => [], 'value' => $name]; // if directory
                } else {
                    // if file
                    if ($name == $scriptName) { // if it's main script of plugin
                        $file = ['tag' => 'filename', 'attr' => ['plugin' => $ext['id']], 'value' => $name];
                    } elseif ($name == $installerName) { // if it's script for install
                        unset($files[$k]);
                    } else {
                        if (substr($name, -4, 4) == '.xml') {
                            unset($files[$k]);
                            $mainfest = $name;
                        } else {
                            $file = ['tag' => 'filename', 'attr' => [], 'value' => $name];
                        }
                    }
                }
            }
            $res = updateManifest($copyPath . '/' . $mainfest, [
                'creationDate' => date('M Y'),
                'version' => $config['version'],
                'scriptfile' => $installerName,
                'files' => $files
            ]);
            if (!$res) {
                out("error\n", 'red');
                return null;
            } else {
                out("ok\n", 'green');
            }
            out("  - packing ...\n", 'yellow');
            if (zipping($copyPath, $zipFile)) {
                out("    done ", 'green');
                out("({$zipFile})\n", 'light_cyan');
            } else {
                out("    fail saving {$zipFile}\n", 'red');
                return null;
            }
        } else {
            out("not found {$script}\n", "red");
        }
        return $zipFile;
    }


    /**
     * Build module package by project config
     * @param array $ext configuration of plugin from project.json
     * @param array $config full config
     * @return string|null Path of result package (zip)
     */
    private static function buildModule($ext, $config) {
        out(" creating module ({$ext['id']})\n", 'yellow');

        $path = $config['siteRoot'] . '/modules/' . $ext['id']; // path of module source
        $copyPath = $config['copy'] . '/' . $ext['id']; // path for copy of module
        $mainfest = $ext['id'] . '.xml'; // manifest file name
        $scriptName = empty($ext['script']) ? $ext['id'] . '.php' : $ext['script']; // main script name
        $script = $path . '/' . $scriptName;
        $installerName = empty($ext['installer']) ? 'installer.php' : basename($ext['installer']); // installer script name
        $zipFile = $config['destination'] . '/' . $ext['id'] . '_v' . str_replace('.', '', $ext['version']) . '.zip';

        out("  - checking module exists ... ", 'yellow');
        if (is_dir($path) && is_file($script)) {
            out("ok\n", "green");
            out("  - create source copy ... ", 'yellow');
            if (!copyDir($path, $copyPath)) {
                out("fail\n", 'red');
                return null;
            } else {
                out("ok\n", "green");
            }
            out("  - generate manifest file ... ", 'yellow');
            $files = glob($copyPath . '/*') + glob($copyPath . '/*.*');
            // prepare files list
            foreach ($files as $k => &$file) {
                $name = basename($file);
                if (is_dir($file)) {
                    $file = ['tag' => 'folder', 'attr' => [], 'value' => $name]; // if directory
                } else {
                    // if file
                    if ($name == $scriptName) { // if it's main script of plugin
                        $file = ['tag' => 'filename', 'attr' => ['plugin' => $ext['id']], 'value' => $name];
                    } elseif ($name == $installerName) { // if it's script for install
                        unset($files[$k]);
                    } else {
                        if (substr($name, -4, 4) == '.xml') {
                            unset($files[$k]);
                            $mainfest = $name;
                        } else {
                            $file = ['tag' => 'filename', 'attr' => [], 'value' => $name];
                        }
                    }
                }
            }
            $res = updateManifest($copyPath . '/' . $mainfest, [
                'creationDate' => date('M Y'),
                'version' => $config['version'],
                'scriptfile' => $installerName,
                'files' => $files
            ]);
            if (!$res) {
                out("error\n", 'red');
                return null;
            } else {
                out("ok\n", 'green');
            }
            out("  - packing ...\n", 'yellow');
            if (zipping($copyPath, $zipFile)) {
                out("    done ", 'green');
                out("({$zipFile})\n", 'light_cyan');
            } else {
                out("    fail saving {$zipFile}\n", 'red');
                return null;
            }
        } else {
            out("not found {$script}\n", "red");
        }
        return $zipFile;
    }


    /**
     * Build extensions package by project config
     * @param array $extensions configuration of all extensions from project.json
     * @param array $config full config
     * @return null
     */
    private static function buildPackage($extensions, $config)
    {
        out(" packing to package ...\n", 'yellow');

        $files = [];
        foreach ($extensions as $ext) {
            if (!empty($ext['pkg'])) {
                if (is_file($ext['pkg'])) {
                    $file = [
                        'tag' => 'file',
                        'attr' => [
                            'type' => 'module',
                            'id' => $ext['type'][0],
                        ],
                        'value' => $ext['pkg']
                    ];
                    switch ($ext['type'][0]) {
                        case 'plugin':
                            $file['attr']['group'] = $ext['type'][1]; break;
                        case 'module':
                            $file['attr']['client'] = $ext['type'][1]; break;
                    }
                    $files[] = $file;
                } else {
                    out("  Error: not found {$ext['pkg']}\n", 'red');
                    return;
                }
            }
        }

        out("  - generate manifest file {$config['manifest']} ... ", 'yellow');
        $manifest = basename($config['manifest']);
        $res = updateManifest(
            $config['manifest'],
            [
                'creationDate' => date('M Y'),
                'version' => $config['version'],
                'files' => $files
            ],
            $config['destination'] . '/' . $manifest
        );
        if (!$res) {
            out("error\n", 'red');
            return;
        } else {
            out("ok\n", 'green');
        }

        // license
        if (!empty($config['license']) && is_file($config['license'])) {
            copy($config['license'], $config['destination'] . '/license.txt');
        }

        out("  - packing ...\n", 'yellow');
        $zipPackageFile = $config['id'] . '.zip';
        if (zipping($config['destination'], $config['destination'] . '/' . $zipPackageFile)) {
            out("     done", 'green');
        } else {
            out("     error\n", 'red');
            return;
        }

        $forDelete = glob($config['destination'] . '/*.*');
        foreach ($forDelete as $file) {
            if (basename($file) != $zipPackageFile) {
                unlink($file);
            }
        }
        out("({$zipPackageFile})\n", 'gray');
        out(" Building complete.\n", 'green');
    }

} 