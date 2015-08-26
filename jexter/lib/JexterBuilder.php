<?php
/*
 * JEXTER
 * Joomla console package builder
 * @author Konstantin Kutsevalov (AdamasAntares) <mail@art-prog.ru>
 * @version 1.0.0 alpha
 * @license GPL v3 (license.txt)
 */


/**
 * Class JexterBuilder
 */
class JexterBuilder {

    private static $lastError = null;

    private static $lastErrorCode = null;


    /**
     * Build extension package
     * @param $args <p>
     * should have 3 keys:<br/>
     *   "myDir" - path to root folder of Jexter [require]
     *   "config" - local path to project config file ("project/project.json") [optional]
     *   "copysfx" - Suffix for directory of source copy (copy of extensions) [optional]
     * </p>
     * @return null|string
     */
    public static function run($args)
    {
        $package = null;
        // load {project}.json
        $config = [];
        if (!self::loadProjectJson($args, $config)) {
            exit(1);
        }
        $config = self::preparePath($config, $args);

        // read extension items
        $extensions = self::getExtensionsData($config);

        // create extensions packages (zip)
        foreach ($extensions as &$ext) {
            switch ($ext['type'][0]) {
                case 'component':
                    $package = $ext['pkg'] = self::buildComponent($ext, $config); break;
                case 'plugin':
                    $package = $ext['pkg'] = self::buildPlugin($ext, $config); break;
                case 'module':
                    $package = $ext['pkg'] = self::buildModule($ext, $config); break;
            }
            if (empty($ext['pkg'])) {
                out("-break-   package of extension {$ext['id']} not found...\n", 'red');
                self::$lastError = "package of extension {$ext['id']} not found...";
                self::$lastErrorCode = 4041;
                return null;
            }
        }

        // if package
        if ($config['type'] === 'package') {
            $package = self::buildPackage($extensions, $config);
        }

        return $package;
    }


    /**
     * Load and parse project.json
     * @param string $args Arguments
     * @param array &$config Configuration array (return)
     * @return bool Result of loading
     */
    private static function loadProjectJson($args, &$config)
    {
        out(" read config ... ", 'yellow');
        $configFile = empty($args['config']) ? 'project/project.json' : $args['config'];
        $jsonFile = $args['myDir'] . '/' . $configFile;
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
        foreach (['name', 'siteRoot', 'id', 'type', 'version'] as $option) {
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
        $config['siteRoot'] = realpath(str_replace('@builder', $args['myDir'], $config['siteRoot']));
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
            $config['copy'] .= '/' . $args['copysfx'] . date('dmY_His');
        } else {
            $config['copy'] = normalizePath($args['myDir'] . '/src_copy/' . basename($args['config'], '.json') . $args['copysfx'] . date('dmY_His'));
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
                $item['type'] = explode('/', strtolower($item['type']));
                $item['type'][] = '';
                $extensions[] = [
                    'name' => $item['name'],
                    'id' => $item['id'],
                    'type' => $item['type'],
                    'version' => $item['version'],
                    'script' => empty($item['script']) ? $item['id'] . '.php' : $item['script'],
                    'excludes' => empty($item['excludes']) ? [] : $item['excludes']
                ];
            }
        } else {
            $type = explode('/', strtolower($config['type']));
            $type[] = '';
            $extensions[0] = [
                'name' => $config['name'],
                'id' => $config['id'],
                'type' => $type,
                'version' => $config['version'],
                'script' => empty($config['script']) ? $config['id'] . '.php' : $config['script'],
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
        out(" creating component ({$ext['id']})\n", 'yellow');

        $pathSite = $config['siteRoot'] . '/components/' . $ext['id'];
        $pathAdmin = $config['siteRoot'] . '/administrator/components/' . $ext['id'];
        $copySitePath = $config['copy'] . '/site';
        $copyAdminPath = $config['copy'] . '/admin';
        $mainfest = $ext['id'] . '.xml';
        $scriptSite = $pathSite . '/' . $ext['script'];
        $scriptAdmin = $pathAdmin . '/' . $ext['script'];
        $zipFile = $config['destination'] . '/' . $ext['id'] . '_v' . str_replace('.', '', $ext['version']) . '.zip';

        out("  - checking frontend component ... ", 'yellow');
        if (is_dir($pathSite) && is_file($scriptSite)) {
            out("ok\n", "green");
            out("  - create frontend source copy ... ", 'yellow');
            if (!copyDir($pathSite, $copySitePath)) {
                out("fail\n", 'red');
                return null;
            } else {
                out("done\n", "green");
            }
            out("  - scan files ... ", 'yellow');
            $filesSite = glob($copySitePath . '/*') + glob($copySitePath . '/*.*');
            // prepare files list
            foreach ($filesSite as $k => &$file) {
                $name = basename($file);
                if (is_dir($file)) {
                    $file = ['tag' => 'folder', 'attr' => [], 'value' => $name]; // if directory
                } else {
                    // if file
                    if (substr($name, -4, 4) == '.xml') {
                        $mainfest = $name;
                    }
                    $file = ['tag' => 'filename', 'attr' => [], 'value' => $name];
                }
            }
            out("done\n", "green");
        } else {
            out("not found {$scriptSite}\n", "red");
            return null;
        }

        out("  - checking backend component ... ", 'yellow');
        if (is_dir($pathAdmin) && is_file($scriptAdmin)) {
            out("ok\n", "green");
            out("  - create frontend source copy ... ", 'yellow');
            if (!copyDir($pathAdmin, $copyAdminPath)) {
                out("fail\n", 'red');
                return null;
            } else {
                out("done\n", "green");
            }
            out("  - scan files ... ", 'yellow');
            $filesAdmin = glob($copyAdminPath . '/*') + glob($copyAdminPath . '/*.*');
            // prepare files list
            foreach ($filesAdmin as $k => &$file) {
                $name = basename($file);
                if (is_dir($file)) {
                    $file = ['tag' => 'folder', 'attr' => [], 'value' => $name]; // if directory
                } else {
                    // if file
                    if (substr($name, -4, 4) == '.xml') {
                        $mainfest = $name;
                    }
                    $file = ['tag' => 'filename', 'attr' => [], 'value' => $name];
                }
            }
            out("done\n", "green");
        } else {
            out("not found {$scriptAdmin}\n", "red");
            return null;
        }

        out("  - generate manifest file ... ", 'yellow');
        $data = [
            'creationDate' => date('M Y'),
            'version' => $config['version'],
            '{marks}' => [
                '{version}' => ''
            ]
        ];
        if (!empty($filesSite)) {
            $data['files'] = $filesSite;
        }
        if (!empty($filesAdmin)) {
            $data['administration/files'] = $filesAdmin;
        }
        $res = updateManifest($copyAdminPath . '/' . $mainfest, $data, $config['copy'] . '/' . $mainfest);
        if (!$res) {
            out("error\n", 'red');
            return null;
        } else {
            out("ok\n", 'green');
        }
        out("  - packing ...\n", 'yellow');
        if (zipping($config['copy'], $zipFile)) {
            out("    done ", 'green');
            out("({$zipFile})\n", 'light_cyan');
        } else {
            out("    fail saving {$zipFile}\n", 'red');
            return null;
        }
        return $zipFile;
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
        $mainfest = $ext['id'] . '.xml'; // default manifest file name
        $script = $path . '/' . $ext['script'];
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
                    if ($name == $ext['script']) { // if it's main script of plugin
                        $file = ['tag' => 'filename', 'attr' => ['plugin' => $ext['id']], 'value' => $name];
                    } else {
                        if (substr($name, -4, 4) == '.xml') {
                            $mainfest = $name;
                        }
                        $file = ['tag' => 'filename', 'attr' => [], 'value' => $name];
                    }
                }
            }
            $res = updateManifest($copyPath . '/' . $mainfest, [
                'creationDate' => date('M Y'),
                'version' => $config['version'],
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
        $mainfest = $ext['id'] . '.xml'; // default manifest file name
        $script = $path . '/' . $ext['script'];
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
                    if ($name == $ext['script']) { // if it's main script of plugin
                        $file = ['tag' => 'filename', 'attr' => ['plugin' => $ext['id']], 'value' => $name];
                    } else {
                        if (substr($name, -4, 4) == '.xml') {
                            $mainfest = $name;
                        }
                        $file = ['tag' => 'filename', 'attr' => [], 'value' => $name];
                    }
                }
            }
            $res = updateManifest($copyPath . '/' . $mainfest, [
                'creationDate' => date('M Y'),
                'version' => $config['version'],
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
                    return null;
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
            return null;
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
            return null;
        }

        $forDelete = glob($config['destination'] . '/*.*');
        foreach ($forDelete as $file) {
            if (basename($file) != $zipPackageFile) {
                unlink($file);
            }
        }
        out("({$zipPackageFile})\n", 'gray');
        out(" Building complete.\n", 'green');
        return $zipPackageFile;
    }

} 