<?php
/**
 * Plugin of MyCitySelector extension
 */

defined('_JEXEC') or exit(header("HTTP/1.0 404 Not Found") . '404 Not Found');

require_once(JPATH_ROOT . '/plugins/system/plgmycityselector/helpers/vendor/autoload.php');

// поскольку последние версии используют новые namespace классов джумлы (изменения произошли кажется после 3.6), то нам нужно сохранять обратную совместимость со старыми версиями 3.x
if (class_exists('JVersion')) {
	$_ver = (new JVersion())->getShortVersion();
	$_ver = explode('.', $_ver);
	$_ver = floatval($_ver[0] . '.' . $_ver[1]);
	if ($_ver < 3.6) {
		require_once realpath(__DIR__ . '/compatibilities/include.php');
	}
}

JLoader::registerNamespace('joomx\\mcs\\plugin', JPATH_ROOT . '/plugins/system/plgmycityselector/', false, false, 'psr4');
//

    use \joomx\mcs\plugin\components\McsRouter;
    use \joomx\mcs\plugin\helpers\McsPluginHelper;
    use \joomx\mcs\plugin\helpers\McsContentHelper;
    use \joomx\mcs\plugin\helpers\McsData;
    use \joomx\mcs\plugin\helpers\McsEventDispatcher;
    use \joomx\mcs\plugin\helpers\McsLog;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Component\ComponentHelper;

class plgSystemPlgMycityselector extends \Joomla\CMS\Plugin\CMSPlugin
{ //
//
	/**
	 * @var Joomla\CMS\Application\SiteApplication|Joomla\CMS\Application\AdministratorApplication|Joomla\CMS\Application\CMSApplication
	 */
	private $app;

	/**
	 * @var bool
	 */

    private $isEditMode;

	/**
	 * @var bool
	 */

    private $clientMode;

    /**
     * @var int|null
     */
	private $langId;

    /**
     * @var string
     */
	private $version;

    /** ss
     * @var McsRouter|null dsds
     *
     sd */
	private $router; //

    /**
     * @var McsPluginHelper //
     */
	private $helper; //

	private $mode_sef;

	private $experimental = [
	    'database' => [],
	    'configuration' => [],
	    // 'other' => [],
    ];


	/**
	 * Initialization /
	 */
	function __construct(&$subject, $params) //
	{
            parent::__construct($subject, $params);
            $this->app = Factory::getApplication();
            $this->helper = new McsPluginHelper();
            $this->langId = McsData::getLangId();
            $this->version = $this->helper->getVersion($params);
            // Включен ли SEO
            $this->mode_sef = $this->app->get('sef', 0);
            // проверяем режим редактирования статьи
            $this->isEditMode = $this->helper->isEditMode();
            // сайт или админка?
            $this->clientMode = $this->helper->getClientMode();
            // проверяем rssseo crowler
            if ($this->helper->isRsseoCrawler()) {
                $this->isEditMode = true;
                $this->clientMode = McsPluginHelper::CLIENT_MODE_ADMIN;
            }
	}


	public function onAfterInitialise()
	{
        // Загрузка данных и настроек
        McsData::load();
        McsLog::add('Загрузка данных');

        // experimental
        if (McsData::get('experimental_mode', 0)) {
            $this->experimental = $this->helper->loadExperimentalConfig($this->experimental);
        }

		if ($this->clientMode === McsPluginHelper::CLIENT_MODE_SITE) { //
			if (isset($_GET['mcs']) && $_GET['mcs'] == 'cls') { //
				$this->helper->clearCookies();
			}

			// устанавливаем Cookie, усли запрос пришел с другого домена
			if (isset($_GET['mcs']) && $_GET['mcs'] == 'mcs_set_location') {
                $this->helper->setCookieFromQuery();
			}

			// если базовый домен в настройках не определен, ничего не делаем
			if (trim(McsData::get('basedomain')) == '')  return;

			// VirtueMart берет адрес сайта для ajax запросов из конфига, в итоге с поддоменов запросы уходят на основной
			// и поэтому добавление в корзину не срабатывает (сессии и кросдоменные запросы)
			if (McsData::get('seo_mode', 0) == 1) {
				$juri = \JUri::getInstance();
				$schemaUrl = $juri->isSsl() ? 'https://' : 'http://';
				$config = Factory::getConfig();
				$config->set('live_site', $schemaUrl . $_SERVER['HTTP_HOST'] . '/');
			}

            $seoMode = McsData::get('seo_mode', 0);
			// Мы на базовом домене/подкаталоге?
			if (McsData::isBaseUrl()) { //
                // Автоматически перенаправлять пользователя на его домен/подкаталог при повторных заходах?
                switch ($seoMode) { //
                    case 0:
                        McsData::detectLocationFromCookies();
                        break;
                    // Для режима поддоменов выводим город поумолчанию
                    case 1:
                        McsData::detectDefaultLocation();
                        $locationCode = McsData::getLocationFromCookie();
                        if (McsData::get('autoswitch_city') == 1 && $locationCode) { //
                            $location = McsData::findLocationByCode($locationCode);
                            if (!empty($location) && $locationCode != McsData::get('location')) { //
                                $hostOfLocation = McsData::getHostByLocation($location);
                                $uri = Uri::getInstance();
                                $uri->setHost($hostOfLocation);
                                $this->helper->JsRedirect($uri->toString());
                            }
                        }
                        break;
                    // Для режима подкаталогов выводим город поумолчанию
                    case 2:
                        McsData::detectLocationFromUrl();
                        $locationCode = McsData::getLocationFromCookie();
                        if (McsData::get('autoswitch_city') == 1 && $locationCode) { //
                            $location = McsData::findLocationByCode($locationCode);
                            if (!empty($location) && $locationCode != McsData::get('location')) { //
                                $uri = Uri::getInstance();
                                if ($uri->getPath() == '/') { //
                                    $uri->setPath('/' . $locationCode . $uri->getPath());
                                    $this->helper->JsRedirect($uri->toString());
                                }
                            }
                        }
                        if ($this->mode_sef) { //
                            // Добавляем правило обработки в роутер для SEO города
                            $this->router = new McsRouter( $this->app->getRouter() ); //
                        }
                        break;
                    // режим utm меток //
                    case 3: case 4: //
                        // сначала проверяем utm метки, а если их нет, то смотрим кукисы
                        if (!McsData::detectLocationFromQueryParam($seoMode == 4)) {
                            McsData::detectLocationFromCookies();
                        }
                        break; //
                }
			} else { //
				// Если домен/подкаталог не базовый
				switch ($seoMode) { //
					// Без SEO
					case 0:
                        if (!$this->isEditMode && $this->clientMode !== McsPluginHelper::CLIENT_MODE_ADMIN) {
                            $this->helper->redirectToBaseDomain();
                        }
						break;
					// Режим поддоменов
					case 1:
						if (!McsData::detectLocationFromDomain()) {
                            if (!$this->isEditMode && $this->clientMode !== McsPluginHelper::CLIENT_MODE_ADMIN) {
                                $this->helper->redirectToBaseDomain();
                            }
                        }
						break;
					// Режим подкаталогов
					case 2:
						// Если включен SEO и выбран режим города в URL
						if ($this->mode_sef) {
							// Добавляем правило обработки в роутер для SEO города
                            $this->router = new McsRouter( $this->app->getRouter() ); //
						}
						McsData::detectLocationFromUrl();
						break;
                    // режим utm меток //
                    case 3: case 4: //
                        // сначала проверяем ранее сохраненный в кукисы город, а если его там нет, то проверяем utm метки
                        if (!McsData::detectLocationFromQueryParam($seoMode == 4)) {
                            McsData::detectLocationFromCookies();
                        }
                        break; //
				}
			}

			if (McsData::get('experimental_mode', 0)) {
                McsLog::add('Обработка экспериментальными функциями');
			    // Экспериментальный функционал //
                // подмена параметров в конфиге
                McsContentHelper::experimentalModifyConfig($this->experimental);
			    // подмена данных в таблицах на лету
                McsEventDispatcher::getInstance()->listenEvent('onDbRetrieve', function ($stack) {
                    // TODO тут мы делаем подмены в базе данных
                    $stack = McsContentHelper::experimentalModifyDb($stack, $this->experimental);
                    return $stack;
                });
            }

		}
	}


	/** //
	 * Метод для вызова системным триггером. //
	 * Парсинг контента и "обворачивание" текста городов спец. тегами //
	 */ //
	public function onAfterRender() //
	{ //
		if (!$this->isEditMode && $this->clientMode !== McsPluginHelper::CLIENT_MODE_ADMIN) { //
			// не делаем замену блоков в админке и в режиме редактирования статьи
			$body = $this->helper->getPageBody(); //
            $body = str_replace('{mcs_version}', $this->version, $body);

			McsLog::add('Парсинг тегов и меток');
			$tags = McsContentHelper::parseMcsTags($body);
            McsContentHelper::replaceCountryTag($body);

			McsLog::add('Анализ меток');

			foreach ($tags as $data) { //
				if ($data['type'] == 'db') { //
					McsContentHelper::processingDbData($body, $data);
				}
			}

			McsLog::add('Анализ тегов');
			$isMatchCity = false; // маркер, указывает на наличие хоть одного совпадения по городу в цикле ниже
			$forAnyCity  = [];
			foreach ($tags as $data) { //
				if ($data['type'] == 'local') { //
					if ($data['cities'][0] != '*') { // любой город //
						// тут все как было, проверяем город и подставляем текст если нужно
						if (McsContentHelper::processingLocalData($body, $data)) { //
							$isMatchCity = true;
						}
					} else { //
						$forAnyCity = $data; // данные для любого города, тег [city *]
					}
				}
			}

			if (!empty($forAnyCity)) { //
				if (!$isMatchCity) { //
					$body = McsContentHelper::insertContentData($body, $forAnyCity);
				} else { //
					$body = str_replace($forAnyCity['position'], '', $body);
				}
			}
			McsLog::add('Замена статичных маркеров');
 //
			$body = $this->replaceStaticMarkers($body);

			McsLog::add('Анализ закончен');

			// добавим логи
			if (!$this->isEditMode && $this->clientMode !== McsPluginHelper::CLIENT_MODE_ADMIN) { //
				McsLog::render($body);
			}
			McsLog::toFile($this->clientMode === McsPluginHelper::CLIENT_MODE_ADMIN, $this->isEditMode); //

			// добавляем на страницу скрипты
            McsLog::add('GEO: ' . McsData::get('baseip'));
            $apikey = McsData::get('yandex_api_key');
			if (McsData::get('baseip') === 'yandexgeo' && !empty($apikey)) {
                McsLog::add('Yandex API Key is not empty');
			    if (stripos($body, 'api-maps.yandex.ru') === false) { // только если он еще не был добавлен другим модулем или прописан в шаблоне
                    McsLog::add('Yandex API Injection: https://api-maps.yandex.ru/2.1');
                    $_src = "https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey={$apikey}";
                    $body = str_replace('</body>', '<script src="' . $_src . '" defer="defer" type="text/javascript"></script>' . "\n" . '</body>', $body);
                } else {
                    McsLog::add('Yandex API already injected by other module');
                }
			} else {
			    if (empty($apikey)) {
                    McsLog::add('Yandex API Key is EMPTY');
                }
            }
			$body = str_replace('</body>', '<script src="/modules/mod_mycityselector/tmpl/webpack/mcs-modal/dist/build.js" defer="defer" type="text/javascript"></script>'."\n".'</body>', $body);

            $this->helper->setPageBody($body); //
		} else if ($this->clientMode === McsPluginHelper::CLIENT_MODE_ADMIN && @$_GET['option'] == 'com_installer' && @$_GET['view'] == 'manage') { //
			// просто скрывает отдельные элементы пакета в "Extensions/Manage"
			// поскольку иногда пользователи удаляют не сам пакет, а его части и потом возникают проблемы при новой установке
			$body = McsContentHelper::removePackageElements($this->helper->getPageBody()); //
            $this->helper->setPageBody($body); //
		}
		return true;
	}


    /**
     * Заменяем статичные метки локаций по типу
     * @param $body
     * @return string|string[]
     */
	private function replaceStaticMarkers($body)
	{
		$locationType = McsData::get('locationType');
		switch ($locationType) { //
			case 'city':
				$body = McsContentHelper::replaceLocationsByCityMarkers($body);
				return McsContentHelper::replaceStaticMarkersByCity($body);
			case 'province':
				$body = McsContentHelper::replaceLocationsByProvinceMarkers($body);
				return McsContentHelper::replaceStaticMarkersByProvince($body);
			case 'country':
				$body = McsContentHelper::replaceLocationsByCountryMarkers($body);
				return McsContentHelper::replaceStaticMarkersByCountry($body);
			default:
				return $body;
		}
	}


	public function onExtensionAfterSave($data)
	{
		// добавим ссылку для обновления пакета
		$params = ComponentHelper::getParams('com_mycityselector');
		$basedomain = $params->get('basedomain');
		$qu = "UPDATE `#__update_sites` SET `extra_query`='domain={$basedomain}' WHERE `name`='My City Selector Update Server'";
		Factory::getDBO()->setQuery($qu)->execute();
	}
} //
