<?php
/**
 * Helpers functions of Jextor
 * @author  Konstantin Kutsevalov (AdamasAntares) <konstantin@kutsevalov.name>
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
 * Вывод в консоль с поддержкой цвета
 *
 * @param String $str        Строка
 * @param String $color      Название цвета для текста:  'black', 'dark_gray', 'blue', 'light_blue', 'green', 'light_green',
 *                           'cyan', 'light_cyan', 'red', 'light_red', 'purple', 'light_purple', 'brown', 'yellow', 'light_gray', 'white'
 * @param String $background Название цвета для фона: 'black', 'red', 'green', 'yellow', 'blue', 'magenta', 'cyan', 'light_gray'
 */
function out($str, $color = '', $background = '')
{
	if (php_sapi_name() == 'cli')
	{
		if (stripos(php_uname('s'), 'win') === false && PHP_OS != 'WINNT')
		{
			$colors      = [
				'black'        => '0;30',
				'dark_gray'    => '1;30',
				'blue'         => '0;34',
				'light_blue'   => '1;34',
				'green'        => '0;32',
				'light_green'  => '1;32',
				'cyan'         => '0;36',
				'light_cyan'   => '1;36',
				'red'          => '0;31',
				'light_red'    => '1;31',
				'purple'       => '0;35',
				'light_purple' => '1;35',
				'brown'        => '0;33',
				'yellow'       => '1;33',
				'light_gray'   => '0;37',
				'white'        => '1;37',
			];
			$backgrounds = [
				'black'      => '40',
				'red'        => '41',
				'green'      => '42',
				'yellow'     => '43',
				'blue'       => '44',
				'magenta'    => '45',
				'cyan'       => '46',
				'light_gray' => '47',
			];
			if (!empty($color) && isset($colors[$color]))
			{
				$str = "\033[" . $colors[$color] . "m" . $str;
			}
			if (!empty($background) && isset($backgrounds[$background]))
			{
				$str = "\033[" . $backgrounds[$background] . "m" . $str;
			}
			echo $str . "\033[0m";
		}
		else
		{
			echo $str;
		}
	}
	else
	{
		$GLOBALS['jexter_web_global_output'][] = $str;
	}
}

/**
 * Reads line from terminal
 *
 * @param        $prompt
 * @param string $color
 *
 * @return string
 */
function input($prompt, $color = '')
{
	out($prompt, $color);
	if (PHP_OS == 'WINNT')
	{
		$line = stream_get_line(STDIN, 1024, PHP_EOL);
	}
	else
	{
		$line = readline('');
	}

	return $line;
}


/**
 * Backspace for terminal
 *
 * @param int $num
 */
function backspace($num = 1)
{
	echo str_repeat(chr(8), $num);
}

/**
 * Downloads any big file
 *
 * @param $file_source
 * @param $file_target
 * @param $color
 *
 * @return bool
 */
function downloadFile($file_source, $file_target, $color = '')
{
	$size   = 0;
	$source = fopen($file_source, 'rb');
	$target = fopen($file_target, 'w+b');
	if (!$source || !$target)
	{
		return false;
	}
	while (!feof($source))
	{
		$data = fread($source, 4096);
		if (fwrite($target, $data) === false)
		{
			return false;
		}
		if (php_sapi_name() == 'cli')
		{
			$len = strlen($size);
			backspace($len);
			$size += strlen($data);
			out($size, $color);
		}
		flush();
	}
	fclose($source);
	fclose($target);

	return true;
}

/**
 * Returns output of out
 * @return array
 */
function getOutput()
{
	if (isset($GLOBALS['jexter_web_global_output']))
	{
		return $GLOBALS['jexter_web_global_output'];
	}

	return [];
}


function loadMyConfig()
{
	$config = [
		'siteRoot' => realpath(__DIR__ . SE . '..' . SE . '..' . SE),  // by default
	];
	$file   = __DIR__ . SE . '..' . SE . 'config' . SE . 'jexter.ini';
	if (is_file($file))
	{
		$lines = parse_ini_file($file);
	}
	else
	{
		$lines = [];
	}
	$config = array_merge($config, $lines);

	return $config;
}


/**
 * Parsing CLI arguments
 *
 * @param array $argv    $ARGV
 * @param array $default Default values with rules of conversion. For example "command arg1 arg2 arg3=val3"
 *                       will be returned as $args[ 0 => arg1, 1 => arg2, arg3 => val3 ]. But with this argument value
 *                       [ 0 => [name => file, default => val1], 1 => [name => line] ]
 *                       we can convert it to $args[ file => arg1, line => arg2, arg3 => val3 ].
 *                       If there is no any arguments in $argv, then will be returned values from "default" keys.
 *
 * @return array
 */
function prepareArguments($argv, $default = [])
{
	$arguments = [];
	foreach ($default as $key => $data)
	{
		if (!empty($data))
		{
			$value                    = empty($default[$key]['default']) ? '' : $default[$key]['default'];
			$arguments[$data['name']] = $value;
		}
	}
	if (!empty($argv))
	{
		foreach ($argv as $key => $arg)
		{
			list($param, $value) = explode('=', $arg . '=', 2);
			$value = trim($value, '" =');
			if (empty($value) && $value != '0')
			{ // if passed simple argument ["./command arg_value"]
				if (isset($default[$key]))
				{
					// if exists default param-value for this index(key)
					$param             = $default[$key]['name'];
					$arguments[$param] = $arg;
				}
				else
				{
					// no default value
					$arguments[$key] = $arg;
				}
			}
			else
			{ // if passed key-value pair ["./command arg=value"]
				$arguments[$param] = $value;
			}
		}
	}
	// project config path
	$arguments['config'] = empty($arguments['config']) ? 'config' . SE . 'project.json' : 'config' . SE . $arguments['config'] . '.json';

	return $arguments;
}


function normalizePath($path)
{
	$parts    = array(); // Array to build a new path from the good parts
	$path     = str_replace('/', SE, $path); // Replace backslashes with forwardslashes
	$path     = str_replace('\\', SE, $path); // Replace backslashes with forwardslashes
	$path     = preg_replace('/\/+/', SE, $path); // Combine multiple slashes into a single slash
	$segments = explode(SE, $path); // Collect path segments
	$test     = ''; // Initialize testing variable
	foreach ($segments as $segment)
	{
		if ($segment != '.')
		{
			$test = array_pop($parts);
			if (is_null($test))
				$parts[] = $segment;
			else if ($segment == '..')
			{
				if ($test == '..')
					$parts[] = $test;

				if ($test == '..' || $test == '')
					$parts[] = $segment;
			}
			else
			{
				$parts[] = $test;
				$parts[] = $segment;
			}
		}
	}

	return implode(SE, $parts);
}


/**
 * Create directory
 *
 * @param $dir
 *
 * @return bool
 */
function createDir($dir)
{
	if (!file_exists($dir))
	{
		// create all path levels for checking each directory from path
		$tokens = explode(SE, trim($dir, SE));
		$length = count($tokens);
		for ($i = 0; $i < $length; $i++)
		{
			if (PHP_OS == 'WINNT')
			{
				$path = $tokens[0] . SE . implode(SE, array_slice($tokens, 1, $i + 1));
			}
			else
			{
				$path = SE . implode(SE, array_slice($tokens, 0, $i + 1));
			}
			//out("create dir: {$path}\n", "red");
			if (!file_exists($path) && !mkdir($path, 0755))
			{
				out("Error: can't create directory {$path} of {$dir}\n", 'red');

				return false;
			}
		}
	}

	return true;
}


/**
 * Copy directory with files
 *
 * @param String $source
 * @param String $dest
 *
 * @return bool
 */
function copyDir($source, $dest, $exclude = [])
{
	if (!createDir($dest))
	{
		return false;
	}
	foreach (
		$iterator = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
			\RecursiveIteratorIterator::SELF_FIRST) as $item
	)
	{
        //check excluded files or directories and miss them
        if(str_ireplace($exclude, '', $item) != $item) {
            continue;
        }

        if ($item->isDir())
        {
            $dir = $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName();

            if (!is_dir($dir))
            {
                mkdir($dir);
            }
        }
        else
        {
            copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
        }
	}

	return true;
}


/**
 * Delete files in some directory
 *
 * @param string $dir
 *
 * @return boolean
 */
function clearDir($dir, $all = false)
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
	$files = glob($dir . '/{,.}*', GLOB_BRACE);
	foreach ($files as $file) {
	    if (substr($file, -3, 3) == '/..' || substr($file, -2, 2) == '/.') {
            continue;
        }
		if (is_dir($file)) {
			clearDir($file, $all);
			@rmdir($file);
		} else {
			if ($all) {
				unlink($file);
			} else {
			    if (strpos($file, '.gitignore') === false) {
                    unlink($file);
                }
            }
		}
	}

	return true;
}


/**
 * Delete directory (with files)
 */
function dropDir($dir, $all = false)
{
	if (clearDir($dir, $all)) {
		rmdir($dir);
		return true;
	}

	return false;
}


/**
 * Scan directory and return all sub-folders and files
 */
function scanDir($path)
{
	$i     = 0;
	$files = glob($path . SE . '*') + glob($path . SE . '*');
	while (isset($files[$i]))
	{
		$file = $files[$i];
		if (is_dir($file))
		{
			$files = array_merge($files, glob($file . SE . '*') + glob($file . SE . '*.*'));
		}
		$i++;
	}

	return $files;
}


/**
 * Convert any name like "Hello camel" or "salut_janne" to "HelloCamel" and "SalutJanne"
 *
 * @param string $name
 *
 * @return string
 */
function getCamel($name)
{
	$name = str_replace(['  ', '-', '_', '$', '.', ',', ':'], [' ', ' ', ' ', '', ' ', ' ', ' '], $name);
	$name = explode(' ', trim($name));
	foreach ($name as &$part)
	{
		$part = ucfirst($part);
	}
	$name = implode('', $name);

	return $name;
}


/**
 * Returns array with configurations files of projects
 *
 * @param bool $fullPath
 * @param bool $onlyNames
 *
 * @return array
 */
function getProjectsConfig($fullPath = false, $onlyNames = false)
{
	$path  = realpath(__DIR__ . SE . '..' . SE . 'config') . SE . '*.json';
	$files = glob($path);
	if (!$fullPath)
	{
		foreach ($files as &$file)
		{
			$file = basename($file);
		}
	}
	if ($onlyNames)
	{
		foreach ($files as &$file)
		{
			$file = basename($file, '.json');
		}
	}

	return $files;
}


/**
 * Returns icons list from icons folder
 */
function getIconsList($default = null)
{
	$path  = realpath(__DIR__ . SE . '..' . SE . 'data' . SE . 'icons' . SE . 'component' . SE) . SE . '*.png';
	$files = glob($path);
	foreach ($files as &$file)
	{
		$file = basename($file, '.json');
	}
	if (!empty($default))
	{
		$files = array_merge([$default], $files);
	}

	return $files;
}


/**
 * Asks user about parameters by scenario
 *
 * @param $args
 * @param $scenario
 *
 * @return array
 * @see scenario.php
 */
function readCliParameters($args, $scenario)
{
	$params = [];
	$steps  = ['common_start', $args['type'], 'common_finish'];
	if (!isset($scenario[$args['type']]))
	{
		out("Wrong type name '{$args['type']}! Please, enter one of correct types: plugin, module, component, library.' \n", 'red');

		return [];
	}
	$pref = ['plugin' => 'plg_', 'module' => 'mod_', 'component' => 'com_', 'library' => 'lib_'];
	$pref = $pref[$args['type']];

	// asking parameters
	foreach ($steps as $step)
	{
		if (empty($scenario[$step]))
		{
			continue;
		}
		foreach ($scenario[$step] as $param)
		{
			if (!empty($param['condition']) && is_array($param['condition']))
			{
				// if step has condition
				$todo = true;
				foreach ($param['condition'] as $paramName => $paramCondition)
				{
					if (!isset($params[$paramName]) || !preg_match($paramCondition, $params[$paramName]))
					{
						$todo = false;
					}
					else
					{
					}
				}
				if ($todo === false)
				{
					continue; // skip current parameter, because condition isn't met
				}
			}
			// check options for select
			$options = null;
			if (!empty($param['options']))
			{
				if (!empty($param['options_title']))
				{
					out($param['options_title'] . "\n", 'cyan');
				}
				if (is_callable($param['options']))
				{
					$options = $param['options'](); // call function
				}
				else
				{
					$options = $param['options'];
				}
				$count = count($options);
				foreach ($options as $n => $option)
				{
					out("[{$n}] {$option}\t\t");
					if ($count > 4 && $n % 2 > 0) out("\n");
					if ($count <= 4) out("\n");
				}
				out("\n");
			}
			// ask user
			$prompt = str_replace(['{ext}', '{pref}'], [$args['type'], $pref], $param['prompt']);
			out($prompt, 'cyan');
			do
			{
				$error = false;
				$value = trim(input(": "));
				// validation
				if (!empty($param['filter']))
				{
					if (!empty($value) && !preg_match($param['filter'], $value))
					{
						out("wrong value, try again\n", 'red');
						$error = true;
					}
					elseif (empty($value))
					{
						$value = $param['default'];
					}
				}
				if (!empty($options) && !empty($param['value_as_option']) && $param['value_as_option'] === true)
				{
					$value = intval($value);
					if (isset($options[$value]))
					{
						$value = $options[$value];
					}
				}
			} while ($error);
			$value                  = str_replace(['{ext}', '{pref}'], [$args['type'], $pref], $value);
			$params[$param['name']] = $value;
		}
	}
	$params = array_merge($args, $params);

	return $params;
}


/**
 * Removes all lines from file that contains "@devnode" in any line position
 *
 * @param $file
 *
 * @deprecated Нам это больше не нужно, файлы все равно зашифрованы
 */
function removeFileNotes($file)
{
//	if (is_file($file) && in_array(pathinfo($file, PATHINFO_EXTENSION), ['php', 'js', 'html', 'txt', 'xml']))
//	{
//		$lines = file($file);
//		foreach ($lines as $k => $line)
//		{
//			if (stripos($line, '@devnote') !== false || stripos($line, '//devnote') !== false) {
//				unset($lines[$k]);
//			}
//		}
//		$lines = implode('', $lines);
//		file_put_contents($file, $lines);
//	}
}


/**
 * @param $file
 */
//function setMcsFree($file)
//{
//	if (defined('IS_FREE'))
//	{
//		if (is_file($file) && in_array(pathinfo($file, PATHINFO_EXTENSION), ['php', 'js', 'html', 'txt', 'xml']))
//		{
//			$lines = file($file);
//			foreach ($lines as $k => $line)
//			{
//				if (preg_match('/const\s+MCS_FREE\s+=\s*(\w+);/i', $line, $match))
//				{
//					$lines[$k] = str_replace($match[1], IS_FREE, $line);
//				}
//			}
//			$lines = implode('', $lines);
//			file_put_contents($file, $lines);
//		}
//	}
//}

function insertPackageDomain($file, $domain = 'unknown')
{

	if (substr($file, -13, 13) == 'installer.php')
	{
		if (is_file($file))
		{
			$content = file_get_contents($file);
			$content = str_replace('{mcs_domain}', $domain, $content);
			out("\nInserted domain '" . $domain . "' in file " . $file . "\n", "green");
			return file_put_contents($file, $content);
		}
		else
		{
			//todo заменить на вывод ошибки
			die('File not found' . $file);
		}
	}
}


/**
 * Returns true if a file is manifest
 *
 * @param $file
 */
function isManifest($file)
{
	if (is_file($file))
	{
		$xml = simplexml_load_file($file);
		if (is_object($xml))
		{
			$element = $xml->xpath('/extension');
			if (!empty($element))
			{
				return true;
			}
		}
	}

	return false;
}


/**
 * Updates manifest file by passed values
 *
 * @param String $file            Path to original manifest file
 * @param Array  $data            <p>
 *                                Data for update. All keys is xpath of xml elements except '{marks}' key.
 *                                Example:
 *                                [
 *                                'license' => 'GNU/GPL',  // key => value
 *                                'install/sql' => [       // key => child_tag_details
 *                                'tag' => 'file',
 *                                'attr' => [
 *                                'driver' => 'mysql',
 *                                'charset' => 'utf8'
 *                                ],
 *                                'value' => 'install.sql'
 *                                ],
 *                                'files' => [            // key => child_tags_details_list
 *                                ['tag' => 'filename', 'value' => 'index.html'],
 *                                ['tag' => 'filename', 'value' => 'sitemapjen.php'],
 *                                ],
 *                                ...etc...
 *                                ]
 *                                </p>
 * @param String $destinationFile [optional] If argument passed, then changes will saved in this file. By default will be updated original file.
 *
 * @return bool
 */
function updateManifest($file, $data = [], $destinationFile = '')
{
	$marks = [];
	if (!empty($data['{marks}']))
	{
		$marks = $data['{marks}'];
		unset($data['{marks}']);
	}
	if (is_file($file))
	{
		$xml = simplexml_load_file($file);
		if (is_object($xml))
		{
			foreach ($data as $tag => $val)
			{
				$element = $xml->xpath('/extension/' . $tag);
				if (empty($element))
				{
					$element = $xml->addChild($tag, '');
				}
				else
				{
					$element = $element[0];
				}
				if (is_string($val))
				{
					$element[0] = $val;
				}
				elseif (is_array($val))
				{
					$element[0] = '';
					if (isset($val['tag']))
					{
						$attr = isset($val['attr']) ? $val['attr'] : [];
						addXmlChild($element, $val['tag'], $val['value'], $attr);
					}
					elseif (isset($val[0]['tag']))
					{
						foreach ($val as $newTag)
						{
							$attr = isset($newTag['attr']) ? $newTag['attr'] : [];
							addXmlChild($element, $newTag['tag'], $newTag['value'], $attr);
						}
					}
				}
			}
			if (empty($destinationFile))
			{
				$destinationFile = $file;
			}
			// replace {marks}
			if (!empty($marks))
			{
				$xml = $xml->asXML();
				foreach ($marks as $_mark => $_value)
				{
					$xml = str_replace($_mark, $_value, $xml);
				}
				if (file_put_contents($destinationFile, $xml) !== false)
				{
					return true;
				}
				else
				{
					out('(put) Can not write to file ' . $destinationFile . "\n", 'red');
				}
			}
			else
			{
				if ($xml->asXML($destinationFile) !== false)
				{
					return true;
				}
				else
				{
					out('(asXML) Can not write to file ' . $destinationFile . "\n", 'red');
				}
			}
		}
	}
	else
	{
		out('File not found ' . $file . "\n", 'red');
	}

	return false;
}


/**
 * Обертка для добавления элемента одновременно с атрибутами
 *
 * @param \SimpleXMLElement $elem
 * @param String            $name
 * @param String            $value
 * @param Array             $attributes ['attr' => 'value', 'attr2' => 'value2']
 */
function addXmlChild($elem, $name, $value, $attributes = [])
{
	$new = $elem->addChild($name, $value);
	if (!empty($attributes) && is_array($attributes))
	{
		foreach ($attributes as $attr => $val)
		{
			$new->addAttribute($attr, $val);
		}
	}
}


/**
 * Zipping directory
 *
 * @param string $sourceDir       The file for zipping
 * @param string $destinationFile The Archive file name
 * @param bool   $overwrite       By default is true
 * @param string $arcRootPath     path in archive as root for files
 *
 * @return bool true on success
 */
function zipping($sourceDir = '', $destinationFile = '', $overwrite = true, $arcRootPath = '/')
{
	if (file_exists($destinationFile) && !$overwrite)
	{
		return false;
	}
	$sourceDir = rtrim($sourceDir, '/\\') . SE;
	$files     = glob($sourceDir . '*') + glob($sourceDir . '*.*'); // read files list BEFORE create arc file
	$zip       = new \ZipArchive();
	if (is_file($destinationFile) && $overwrite === true)
	{
		@unlink($destinationFile);
	}
	$ret = $zip->open($destinationFile, \ZipArchive::CREATE);
	if ($ret !== true)
	{
		out('  Error! Can\'t create zip file ' . $destinationFile . "  ", 'red');
		switch ($ret)
		{
			case \ZipArchive::ER_EXISTS:
				out(" 'File already exists.'\n", 'red');
				break;
			case \ZipArchive::ER_INCONS:
				out(" 'Zip archive inconsistent.'\n", 'red');
				break;
			case \ZipArchive::ER_INVAL:
				out(" 'Invalid argument.'\n", 'red');
				break;
			case \ZipArchive::ER_MEMORY:
				out(" 'Malloc failure.'\n", 'red');
				break;
			case \ZipArchive::ER_NOENT:
				out(" 'No such file.'\n", 'red');
				break;
			case \ZipArchive::ER_NOZIP:
				out(" 'Not a zip archive.'\n", 'red');
				break;
			case \ZipArchive::ER_OPEN:
				out(" 'Can't open file.'\n", 'red');
				break;
			case \ZipArchive::ER_READ:
				out(" 'Read error.'\n", 'red');
				break;
			case \ZipArchive::ER_SEEK:
				out(" 'Seek error.'\n", 'red');
				break;
		}

		return false;
	}
	else
	{
		$count = count($files);
		for ($i = 0; $i < $count; $i++)
		{ // foreach work incorrect with array_merge
			$file = $files[$i];
			if (strpos($file, '.gitignore') !== false)
			{
				continue;
			}
			if (is_dir($file))
			{
				$dir = str_replace($sourceDir, $arcRootPath, $file);
				out("     add folder " . $dir . " ... ", 'light_blue');
				if ($zip->addEmptyDir($dir))
				{
					out("ok\n", 'light_blue');
				}
				else
				{
					out("error\n", 'red');
				}
				$subFiles = glob($file . '/*') + glob($file . '/*.*');
				$files    = array_merge($files, $subFiles);
				$count    = count($files);
			}
			else
			{
				$relPath = str_replace($sourceDir, $arcRootPath, $file);
				out("     add file " . $relPath . " ... ", 'light_blue');
				if ($zip->addFile($file, $relPath))
				{
					out("ok\n", 'light_blue');
				}
				else
				{
					out("error\n", 'red');
				}
			}
		}
		$zip->close();
	}

	return true;
}


function unzipping($file, $destination)
{
	if (!is_dir($destination))
	{
		if (!createDir($destination))
		{
			return false;
		}
	}
	else
	{
		clearDir($destination);
	}
	$zip = new \ZipArchive();
	$res = $zip->open($file);
	if ($res === true)
	{
		if ($zip->extractTo($destination))
		{
			$zip->close();

			return true;
		}
		$zip->close();
	}

	return false;
}

/**
 * Returns joomla configuration object
 *
 * @param $siteRoot
 *
 * @return \Joomla\Registry\Registry
 */
function loadJoomlaConfig($siteRoot)
{
	include_once $siteRoot . SE . 'libraries' . SE . 'vendor' . SE . 'joomla' . SE . 'registry' . SE . 'src' . SE . 'FormatInterface.php';
	include_once $siteRoot . SE . 'libraries' . SE . 'vendor' . SE . 'joomla' . SE . 'registry' . SE . 'src' . SE . 'AbstractRegistryFormat.php';
	include_once $siteRoot . SE . 'libraries' . SE . 'vendor' . SE . 'joomla' . SE . 'registry' . SE . 'src' . SE . 'Factory.php';
	include_once $siteRoot . SE . 'libraries' . SE . 'vendor' . SE . 'joomla' . SE . 'registry' . SE . 'src' . SE . 'Format' . SE . 'Php.php';
	include_once $siteRoot . SE . 'libraries' . SE . 'vendor' . SE . 'joomla' . SE . 'registry' . SE . 'src' . SE . 'Registry.php';
	include_once $siteRoot . SE . 'libraries' . SE . 'vendor' . SE . 'joomla' . SE . 'utilities' . SE . 'src' . SE . 'ArrayHelper.php';
	include_once $siteRoot . SE . 'configuration.php';
	$config   = new \JConfig();
	$registry = new \Joomla\Registry\Registry();
	$registry->loadObject($config);

	return $registry;
}
