<?php
/*
 * JEXTER
 * Joomla extensions creator
 * @author Konstantin Kutsevalov (AdamasAntares) <konstantin@kutsevalov.name>
 * @version 1.0.0 alpha
 * @license GPL v3 (license.txt)
 */

namespace adamasantares\jexter;


if (!defined('JEXTER_DIR'))
{
	define('JEXTER_DIR', realpath(__DIR__ . '/../'));
}

if (!defined('SE'))
{
	define('SE', DIRECTORY_SEPARATOR);
}


/**
 * Class JexterBuilder
 */
class JexterBuilder
{

	private static $lastError = null;

	private static $lastErrorCode = null;


	/**
	 * Build extension's installer
	 *
	 * @param $args <p>
	 *              should have 1 key:<br/>
	 *              "config" - local path to project config file ("config/project.json")
	 *              </p>
	 *
	 * @return array Array of result installers path
	 */
	public static function run($args)
	{
		if (isset($args['free']) && $args['free'] == '0')
		{
			out("Building PAID/UNLIMITED version\n", 'light_cyan');
			define('IS_FREE', 'false');
		}
		else
		{
			out("Building FREE/LIMITED version!\n", 'orange');
			define('IS_FREE', 'true');
		}
		$packages     = [];
		$jexterConfig = loadMyConfig();
		// load {project}.json
		$projectConfig = [];
		if (!self::loadProjectJson($args, $projectConfig))
		{
			out("-break-   configuration file not found\n", 'red');
			self::$lastError     = "configuration file not found";
			self::$lastErrorCode = 4041;

			return [];
		}

		$projectConfig = array_replace_recursive($projectConfig, $jexterConfig);
		$projectConfig = self::preparePath($projectConfig, $args);
		// read extension items
		$extensions = self::getExtensionsData($projectConfig);

		// create extensions packages (zip)
		foreach ($extensions as $i => $ext)
		{
			switch ($ext['type'][0])
			{
				case 'component':
					$pkg = self::buildComponent($ext, $projectConfig);
					break;
				case 'plugin':
					$pkg = self::buildPlugin($ext, $projectConfig);
					break;
				case 'module':
					$pkg = self::buildModule($ext, $projectConfig);
					break;
				case 'library':
					$pkg = self::buildLibrary($ext, $projectConfig);
					break;
			}
			if (empty($pkg))
			{
				out("-break-   package of extension {$ext['id']} not found...\n", 'red');
				self::$lastError     = "package of extension {$ext['id']} not found...";
				self::$lastErrorCode = 4042;

				return []; // !
			}
			$packages[] = $extensions[$i]['pkg'] = $pkg;
		}

		// if package
		if ($projectConfig['type'] === 'package') {
			$pkg = self::buildPackage($extensions, $projectConfig);
			if (empty($pkg)) {
				out("-break-   error on creating package installer\n", 'red');
				self::$lastError     = "error on creating package installer";
				self::$lastErrorCode = 4043;
				return []; // !
			}
			$packages = [$pkg];
		}

		// remove copy folder
        if ($projectConfig['no_copy']) {
            if (!empty($projectConfig['copy'])) {
                dropDir($projectConfig['copy'], true);
            }
        }

		return $packages;
	}


	/**
	 * Load and parse project.json
	 *
	 * @param string $args    Arguments
	 * @param array  &$config Configuration array (return)
	 *
	 * @return bool Result of loading
	 */
	private static function loadProjectJson($args, &$config)
	{
		out(" read config ... ", 'light_blue');
		$configFile = empty($args['config']) ? 'project' . SE . 'project.json' : $args['config'];
		$jsonFile   = JEXTER_DIR . SE . $configFile;
		if (!is_file($jsonFile))
		{
			out(" not found project configuration file: {$jsonFile}\n", 'red');

			return false;
		}
		$config = json_decode(file_get_contents($jsonFile), true);
		if (empty($config))
		{
			out(" the project configuration file is empty or it contain syntax error.\n", 'red');

			return false;
		}
		// check required options
		foreach (['name', 'id', 'type', 'version'] as $option) {
			if (empty($config[$option])) {
				out(" not found '{$option}' option!\n", 'red');
				return false;
			}
		}
		if (!empty($args['version'])) {
			$config['version'] = $args['version'];
		}
		if (!empty($args['domain'])) {
			$config['domain'] = $args['domain'];
		}
		if (!empty($args['no_copy'])) {
			$config['no_copy'] = (bool) ($args['no_copy'] === 'true');
		}

		$config['type'] = strtolower($config['type']);
		if ($config['type'] === 'package' && empty($config['packageItems'])) {
			out(" for 'package' extension must be 'package_items' option with list of extensions.\n", 'red');
			return false;
		}
		out("ok\n", 'green');

		return true;
	}


	/**
	 * Prepare all path and dirs from config
	 */
	private static function preparePath($config, $args)
	{
		// create directory for copies of source files of extensions
		if (!empty($config['copy']))
		{
			$config['copy'] = normalizePath(str_replace('@builder', JEXTER_DIR, $config['copy']));
			$config['copy'] .= SE . basename($args['config'], '.json') . date('_dmY_His');
		}
		else
		{
			$config['copy'] = normalizePath(JEXTER_DIR . SE . 'src_copy' . SE . basename($args['config'], '.json') . date('_dmY_His'));
		}
		createDir($config['copy']);
		// directory for result extension's installer
		if (!empty($config['destination']))
		{
			$config['destination'] = normalizePath(str_replace('@builder', JEXTER_DIR, $config['destination']));
		}
		else
		{
			$config['destination'] = normalizePath(JEXTER_DIR . SE . 'extensions' . SE . basename($args['config'], '.json'));
		}
		createDir($config['destination']);
		// other
		foreach (['manifest', 'siteRoot', 'license', 'installer'] as $key)
		{
			if (!empty($config[$key]))
			{
				$config[$key] = normalizePath(str_replace('@builder', JEXTER_DIR, $config[$key]));
			}
		}
		if (!empty($config['packageFiles']))
		{
			foreach ($config['packageFiles'] as $key => $path)
			{
				$config['packageFiles'][$key] = normalizePath(str_replace('@builder', JEXTER_DIR, $path));
			}
		}

		return $config;
	}


	/**
	 * Returns extensions data (items and parameters) from project configuration file (json)
	 *
	 * @param $config
	 *
	 * @return array
	 */
	private static function getExtensionsData($config)
	{
		$extensions = [];
		if ($config['type'] === 'package')
		{
			foreach ($config['packageItems'] as $item)
			{
				$item['type']   = explode('/', strtolower($item['type']));
				if (count($item['type']) < 2)  $item['type'][] = '';
				$extensions[]   = [
					'name'     => $item['name'],
					'id'       => $item['id'],
					'type'     => $item['type'],
					'version'  => $item['version'],
					'script'   => empty($item['script']) ? $item['id'] . '.php' : $item['script'],
					'excludes' => empty($item['excludes']) ? [] : $item['excludes']
				];
			}
		}
		else
		{
			$type          = explode('/', strtolower($config['type']));
			$type[]        = '';
			$extensions[0] = [
				'name'     => $config['name'],
				'id'       => $config['id'],
				'type'     => $type,
				'version'  => $config['version'],
				'script'   => empty($config['script']) ? $config['id'] . '.php' : $config['script'],
				'excludes' => empty($config['excludes']) ? [] : $config['excludes']
			];
		}

		return $extensions;
	}


	/**
	 * Build component package by project config
	 *
	 * @param array $ext    configuration of plugin from project.json
	 * @param array $config full config
	 *
	 * @return string|null Path of result package (zip)
	 */
	private static function buildComponent($ext, $config)
	{
		out(" creating component ({$ext['id']})\n", 'light_blue');

		$pathSite      = $config['siteRoot'] . SE . 'components' . SE . $ext['id'];
		$pathAdmin     = $config['siteRoot'] . SE . 'administrator' . SE . 'components' . SE . $ext['id'];
		$copySitePath  = $config['copy'] . SE . $ext['id'] . SE . 'site';
		$copyAdminPath = $config['copy'] . SE . $ext['id'] . SE . 'admin';
		$mainfest      = $ext['id'] . '.xml';
		$scriptSite    = $pathSite . SE . $ext['script'];
		$scriptAdmin   = $pathAdmin . SE . $ext['script'];
		$zipFile       = $config['destination'] . SE . $ext['id'] . '_v' . str_replace('.', '', $ext['version']) . '.zip';
		$domain        = isset($config['domain']) ? $config['domain'] : '';

		out("  - checking frontend component ... ", 'light_blue');
		if (is_dir($pathSite) && is_file($scriptSite)) {
			out("ok\n", "green");
			out("  - create frontend source copy ... ", 'light_blue');
			if (!copyDir($pathSite, $copySitePath)) {
				out("fail\n", 'red');

				return null;
			} else {
				out("done\n", "green");
			}
			out("  - scan files ... ", 'light_blue');
			$filesSite = glob($copySitePath . SE . '*') + glob($copySitePath . SE . '*.*');
			for ($z = 0; $z < count($filesSite); $z++) {
				$file = $filesSite[$z];
				if (is_dir($file)) {
					$subFiles  = glob($file . SE . '*') + glob($file . SE . '*.*');
					$filesSite = array_merge($filesSite, $subFiles);
				} else {
					//removeFileNotes($file);
					setMcsFree($file);
				}
			}
			$filesSite        = glob($copySitePath . SE . '*') + glob($copySitePath . SE . '*.*');
			$filesSiteDetails = [];
			// prepare files list
			foreach ($filesSite as $file)
			{
				$name = basename($file);
				if (is_dir($file))
				{
					$filesSiteDetails[] = ['tag' => 'folder', 'attr' => [], 'value' => $name]; // if directory
				}
				else
				{
					// if file
					if (substr($name, -4, 4) == '.xml' && isManifest($file))
					{
						$mainfest = $name;
					}
					$filesSiteDetails[] = ['tag' => 'filename', 'attr' => [], 'value' => $name];
				}
			}
			out("done\n", "green");
		}
		else
		{
			out("not found {$scriptSite}\n", "red");

			return null;
		}

		out("  - checking backend component ... ", 'light_blue');
		if (is_dir($pathAdmin) && is_file($scriptAdmin))
		{
			out("ok\n", "green");
			out("  - create backend source copy ... ", 'light_blue');
			if (!copyDir($pathAdmin, $copyAdminPath))
			{
				out("fail\n", 'red');

				return null;
			}
			else
			{
				out("done\n", "green");
			}
			out("  - scan files ... ", 'light_blue');
			// сначала удаляем комменты, потом только формируем xml
			$filesAdmin = glob($copyAdminPath . SE . '*') + glob($copyAdminPath . SE . '*.*');
			// prepare files list
			for ($z = 0; $z < count($filesAdmin); $z++)
			{
				$file = $filesAdmin[$z];
				if (is_dir($file))
				{
					$subFiles   = glob($file . SE . '*') + glob($file . SE . '*.*');
					$filesAdmin = array_merge($filesAdmin, $subFiles);
				}
				else
				{
					//removeFileNotes($file);
					setMcsFree($file);
					insertPackageDomain($file, $domain);
				}
			}
			$filesAdmin        = glob($copyAdminPath . SE . '*') + glob($copyAdminPath . SE . '*.*');
			$filesAdminDetails = [];
			// prepare files list
			foreach ($filesAdmin as $file)
			{
				if (stristr($file, 'com_mycityselector.xml'))
				{
					continue;
				}
				$name = basename($file);
				if (is_dir($file))
				{
					$filesAdminDetails[] = ['tag' => 'folder', 'attr' => [], 'value' => $name]; // if directory
				}
				else
				{
					if (substr($name, -4, 4) == '.xml' && isManifest($file))
					{
						$mainfest = $name;
					}
					$filesAdminDetails[] = ['tag' => 'filename', 'attr' => [], 'value' => $name];
				}
			}
			out("done\n", "green");
		}
		else
		{
			out("not found {$scriptAdmin}\n", "red");

			return null;
		}

		out("  - generate manifest file ... ", 'light_blue');
		$data = [
			'creationDate' => date('M Y'),
			'version'      => $config['version'],
			'{marks}'      => [
				'{version}' => ''
			]
		];
		if (!empty($filesSiteDetails))
		{
			$data['files'] = $filesSiteDetails;
		}
		if (!empty($filesAdminDetails))
		{
			$data['administration/files'] = $filesAdminDetails;
		}
		$res = updateManifest($copyAdminPath . SE . $mainfest, $data);
		removeFileNotes($copyAdminPath . SE . $mainfest);
		copy($copyAdminPath . SE . $mainfest, $config['copy'] . SE . $ext['id'] . SE . $mainfest);
		if (!$res) {
			out("error\n", 'red');
			return null;
		}
        out("ok\n", 'green');

		out("  - packing ...\n", 'light_blue');
		if (zipping($config['copy'] . SE . $ext['id'], $zipFile)){
			out("    done ", 'green');
			out("({$zipFile})\n", 'light_cyan');

            dropDir($copySitePath, true);
            dropDir($copyAdminPath, true);
		} else {
			out("    fail saving {$zipFile}\n", 'red');
			return null;
		}

		return $zipFile;
	}


	/**
	 * Build plugin package by project config
	 *
	 * @param array $ext    configuration of plugin from project.json
	 * @param array $config full config
	 *
	 * @return string|null Path of result package (zip)
	 */
	private static function buildPlugin($ext, $config)
	{
		out(" creating plugin ({$ext['id']})\n", 'light_blue');

		$path     = $config['siteRoot'] . SE . 'plugins' . SE . $ext['type'][1] . SE . $ext['id'];
		$copyPath = $config['copy'] . SE . $ext['id'];
		$mainfest = $ext['id'] . '.xml'; // default manifest file name
		$script   = $path . SE . $ext['script'];
		$zipFile  = $config['destination'] . SE . $ext['id'] . '_v' . str_replace('.', '', $ext['version']) . '.zip';

		out("  - checking plugin exists ... ", 'light_blue');
		if (is_dir($path) && is_file($script))
		{
			out("ok\n", "green");
			out("  - create source copy ... ", 'light_blue');
			if (!copyDir($path, $copyPath))
			{
				out("fail\n", 'red');

				return null;
			}
			else
			{
				out("ok\n", "green");
			}
			out("  - generate manifest file ... ", 'light_blue');
			$files = glob($copyPath . SE . '*') + glob($copyPath . SE . '*.*');
			for ($z = 0; $z < count($files); $z++)
			{
				$file = $files[$z];
				if (is_dir($file))
				{
					$subFiles = glob($file . SE . '*') + glob($file . SE . '*.*');
					$files    = array_merge($files, $subFiles);
				}
				else
				{
					//removeFileNotes($file);
					setMcsFree($file);
				}
			}
			// формируем xml
			$files        = glob($copyPath . SE . '*') + glob($copyPath . SE . '*.*');
			$filesDetails = [];
			// prepare files list
			foreach ($files as $file)
			{
				$name = basename($file);
				if (is_dir($file))
				{
					$filesDetails[] = ['tag' => 'folder', 'attr' => [], 'value' => $name]; // if directory
				}
				else
				{
					if ($name == $ext['script'])
					{ // if it's main script of plugin
						$filesDetails[] = ['tag' => 'filename', 'attr' => ['plugin' => $ext['id']], 'value' => $name];
					}
					else
					{
						if (substr($name, -4, 4) == '.xml' && isManifest($file))
						{
							$mainfest = $name;
						}
						$filesDetails[] = ['tag' => 'filename', 'attr' => [], 'value' => $name];
					}
				}
			}
			$res = updateManifest($copyPath . SE . $mainfest, [
				'creationDate' => date('M Y'),
				'version'      => $config['version'],
				'files'        => $filesDetails
			]);
			removeFileNotes($copyPath . SE . $mainfest);
			if (!$res)
			{
				out("error\n", 'red');

				return null;
			}
			else
			{
				out("ok\n", 'green');
			}
			out("  - packing ...\n", 'light_blue');
			if (zipping($copyPath, $zipFile))
			{
				out("    done ", 'green');
				out("({$zipFile})\n", 'light_cyan');

                dropDir($copyPath, true);
			}
			else
			{
				out("    fail saving {$zipFile}\n", 'red');

				return null;
			}
		}
		else
		{
			out("not found {$script}\n", "red");
		}

		return $zipFile;
	}


	/**
	 * Build module package by project config
	 *
	 * @param array $ext    configuration of plugin from project.json
	 * @param array $config full config
	 *
	 * @return string|null Path of result package (zip)
	 */
	private static function buildModule($ext, $config)
	{
		out(" creating module ({$ext['id']})\n", 'light_blue');
		$modType = ($ext['type'][1] === 'admin') ? 'administrator' . SE : '';
		$path     = $config['siteRoot'] . SE . $modType . 'modules' . SE . $ext['id']; // path of module source
		$copyPath = $config['copy'] . SE . $ext['id']; // path for copy of module
		$mainfest = $ext['id'] . '.xml'; // default manifest file name
		$script   = $path . SE . $ext['script'];
		$zipFile  = $config['destination'] . SE . $ext['id'] . '_v' . str_replace('.', '', $ext['version']) . '.zip';

		out("  - checking module exists ... ", 'light_blue');
		if (is_dir($path) && is_file($script))
		{
			out("ok\n", "green");
			out("  - create source copy ... ", 'light_blue');
			if (!copyDir($path, $copyPath, $ext['excludes']))
			{
				out("fail\n", 'red');
				return null;
			}
			else
			{
				out("ok\n", "green");
			}
			out("  - scan files ... ", 'light_blue');
			$files = glob($copyPath . SE . '*') + glob($copyPath . SE . '*.*');
			for ($z = 0; $z < count($files); $z++)
			{
				$file = $files[$z];

				if (is_dir($file))
				{
					$subFiles = glob($file . SE . '*') + glob($file . SE . '*.*');
					$files    = array_merge($files, $subFiles);
				}
				else
				{
					//removeFileNotes($file);
					setMcsFree($file);
				}
			}

			out("  - generate manifest file ... ", 'light_blue');
			$files        = glob($copyPath . SE . '*') + glob($copyPath . SE . '*.*');
			$filesDetails = [];
			// prepare files list
			foreach ($files as $file)
			{
				$name = basename($file);
				if (is_dir($file))
				{
					$filesDetails[] = ['tag' => 'folder', 'attr' => [], 'value' => $name]; // if directory
				}
				else
				{
					if ($name == $ext['script'])
					{ // if it's main script of plugin
						$filesDetails[] = ['tag' => 'filename', 'attr' => ['module' => $ext['id']], 'value' => $name];
					}
					else
					{
						if (substr($name, -4, 4) == '.xml' && isManifest($file))
						{
							$mainfest = $name;
						}
						$filesDetails[] = ['tag' => 'filename', 'attr' => [], 'value' => $name];
					}
				}
			}
			$res = updateManifest($copyPath . SE . $mainfest, [
				'creationDate' => date('M Y'),
				'version'      => $config['version'],
				'files'        => $filesDetails
			]);

			//removeFileNotes($copyPath . SE . $mainfest);
			if (!$res)
			{
				out("error\n", 'red');

				return null;
			}
			else
			{
				out("ok\n", 'green');
			}
			out("  - packing ...\n", 'light_blue');
			if (zipping($copyPath, $zipFile))
			{
				out("    done ", 'green');
				out("({$zipFile})\n", 'light_cyan');

                dropDir($copyPath, true);
			}
			else
			{
				out("    fail saving {$zipFile}\n", 'red');

				return null;
			}
		}
		else
		{
			out("not found {$script}\n", "red");
		}

		return $zipFile;
	}


	// TODO library
	private static function buildLibrary($ext, $config)
	{
		//removeFileNotes($file);
	}

	/**
	 * Build extensions package by project config
	 *
	 * @param array $extensions configuration of all extensions from project.json
	 * @param array $config     full config
	 *
	 * @return null
	 */
	private static function buildPackage($extensions, $config)
	{
		out(" packing extensions to package ...\n", 'light_blue');

		$domain = isset($config['domain']) ? $config['domain'] : '';

		$files = [];
		// generate extensions list (of current package)
		foreach ($extensions as $ext)
		{
			if (!empty($ext['pkg']))
			{
				if (is_file($ext['pkg']))
				{
					$file = [
						'tag'   => 'file',
						'attr'  => [
							'type' => $ext['type'][0],
							'id'   => $ext['id'],
						],
						'value' => basename($ext['pkg'])
					];
					switch ($ext['type'][0])
					{
						case 'plugin':
							$file['attr']['group'] = $ext['type'][1];
							break;
						case 'module':
							$file['attr']['client'] = $ext['type'][1];
							break;
					}
					$files[] = $file;
				}
				else
				{
					out("  Error: not found {$ext['pkg']}\n", 'red');

					return null;
				}
			}
		}

		// package files (just copy its to package folder)
		if (!empty($config['packageFiles']))
		{
			out("  - copy package files ... \n", 'light_blue');
			foreach ($config['packageFiles'] as $file)
			{
				out("     add file " . $file . " ... ", 'light_blue');
				if (copy($file, $config['destination'] . SE . basename($file)))
				{
					insertPackageDomain($config['destination'] . SE . basename($file), $domain);
					out("ok\n", 'light_blue');
				}
				else
				{
					out("error\n", 'red');
				}
			}
		}

		out("  - generate manifest file {$config['manifest']} ... ", 'light_blue');
		$manifest = basename($config['manifest']);
		$res      = updateManifest(
			$config['manifest'],
			[
				'creationDate' => date('M Y'),
				'version'      => $config['version'],
				'files'        => $files
			],
			$config['destination'] . SE . $manifest
		);
		//removeFileNotes($config['destination'] . SE . $manifest);
		if (!$res)
		{
			out("error on Manifest file updating\n", 'red');

			return null;
		}
		else
		{
			out("ok\n", 'green');
		}

		out("  - packing ...\n", 'light_blue');
		$zipPackageFile = $config['id'] . '.zip';
		@unlink($config['destination'] . SE . $zipPackageFile);
		if (zipping($config['destination'], $config['destination'] . SE . $zipPackageFile))
		{
			out("     done", 'green');
		}
		else
		{
			out("     error\n", 'red');

			return null;
		}

		$forDelete = glob($config['destination'] . SE . '*.*');
		foreach ($forDelete as $file)
		{
			if (basename($file) != $zipPackageFile)
			{
				unlink($file);
			}
		}
		out("({$config['destination']}" . SE . "{$zipPackageFile})\n", 'gray');
		out(" Building complete.\n", 'green');

		return $zipPackageFile;
	}

}
