<?php
/**
 * Sitemap Jen
 * @author Konstantin@Kutsevalov.name
 * @version 1.3.0 beta
 */


// Функции сканера вынесены в одтельный скрипт для снижения нагрузки на сервер.
// По сути здесь нужно только подключение к БД и curl, а все остальные функции joomla попросту излишни.

$ROOT = $_SERVER['DOCUMENT_ROOT'] . '/';
$MYDIR = dirname(__FILE__) . '/';

$action = isset($_POST['action']) ? $_POST['action'] : '';
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'cron'; // по умолчанию считаем, что запущено кроном

if ($mode == 'cron') { // проверяем возможную активность ajax-запросов, при вызове скрипта через cron
    $lastTime = 0;
    is_file($MYDIR . 'ajaxtime.php') and include $MYDIR . 'ajaxtime.php'; // -> $lastTime
    if ($lastTime > 0 && (@time() - $lastTime) < 60 * 2) { // 2 минуты
        exit;
    }
}

session_start();

// TODO replace by PDO singleton extension
// БД
class jenSQL extends mysqli
{
    private $prefix = 'jos_';
    private $log = 'jenSQL.log';

    public function __construct($jc, $log)
    {
        parent::__construct($jc->host, $jc->user, $jc->password, $jc->db);
        $this->set_charset('utf8');
        $this->prefix = $jc->dbprefix;
        $this->log = $log;
        // file_put_contents( $log, " \n" );
    }

    public function query($qu = '')
    {
        $qu = str_replace('#__', $this->prefix, $qu);
        // file_put_contents( $this->log, $qu."\n\n", FILE_APPEND );
        return parent::query($qu);
    }
}

include $ROOT . 'configuration.php';
$SQL = new jenSQL(new JConfig(), $MYDIR . 'jenSQL.log');
if (mysqli_connect_error()) {
    exit(json_encode(['error' => 100, 'logs' => ['Ошибка подключения к БД :_(']]));
}


// инициализация
$ignore = [];
$options = getOptions();
$www = false; // for filtering of www aliases
$scheme = (empty($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) == 'off') ? 'http://' : 'https://';
$domain = $scheme . $_SERVER['SERVER_NAME'];
if (substr($_SERVER['SERVER_NAME'], 0, 4) != 'www.') {
    $www = false;
}

// проверяем curl
if (!function_exists('curl_init')) {
    define('IS_CURL', false);
} else {
    define('IS_CURL', true);
}

// Запуск
if ($action == 'init') {
    echo doInit();
} elseif ($action == 'stop') {
    echo doStop();
} else {
    if ($options['task_status'] == 'in_work') {
        if ($options['task_action'] == 'scan') {
            // сканируем сайт
            echo doScan($mode);
        } elseif ($options['task_action'] == 'generate') {
            // генерируем sitemap на основе самых обновленных страниц
            // читаем ссылки из базы, с сортировкой по дате
            echo doGenerate($mode);
        } else {
            if ($mode == 'ajax') {
                echo json_encode(['error' => 10, 'logs' => ['Ошибка запроса: неизвестная команда']]);
            } else {
                saveLog(['Ошибка запроса: неизвестная команда']);
            }
        }
    } else {
        // нет активной задачи
        if ($mode == 'ajax') {
            echo json_encode(['error' => 0, 'logs' => []]);
        }
    }
}

$SQL->close();

exit;


// ===============================


// инициализация
// может быть произведена только из админ-панели
// в качестве ответа возвращает:
//		- для сканирования: количество потоков, которое можно породить (а также адреса для первого сканирования) или ошибку (error)
//		- для генерации: ошибку или пустой ответ
function doInit()
{
    global $SQL;
    global $options;
    global $domain;
    global $MYDIR;
    global $ROOT;

    $param = isset($_POST['param']) ? intval($_POST['param']) : 0;
    $thr = intval($options['threads']);
    if ($thr < 1) {
        $thr = 1;
    } elseif ($thr > 10) {
        $thr = 10;
    }

    $json = [
        'action' => '',
        'logs' => [],
        'urls' => [],
        'newcount' => 0,
        'threadsCount' => 0,
        'error' => 0,
    ];

    file_put_contents($MYDIR . 'ajaxtime.php', '<' . '?' . 'php $lastTime = ' . @time() . ';');

    if ($param == 1) {
        // ПРОДОЛЖЕНИЕ прерванного или текущего сканирования
        $links = [];
        // проверяем есть ли незавершенная задача
        if ($options['task_action'] != 'scan') {
            setOption('task_action', 'scan');
        }
        if ($options['task_status'] != 'in_work') {
            setOption('task_status', 'in_work');
        }
        // есть ли зарезервированные для сканирования адреса?
        $res = $SQL->query("SELECT `id`,`loc` FROM `#__sitemapjen_links` WHERE `changefreq` LIKE '+scan'");
        if ($res->num_rows == 0) {
            $url = @$options['task_url'];
            if (substr($url, 0, strlen($domain)) != $domain) {
                $url = $domain;
            }
            $res = $SQL->query("SELECT `id`,`loc` FROM `#__sitemapjen_links` WHERE `changefreq`='-' AND `loc` LIKE '{$url}%' LIMIT {$thr}");
        }
        while ($row = $res->fetch_assoc()) {
            $links[] = $row;
        }
        // готовим ответ для клиента
        foreach ($links as $i => $link) {
            $SQL->query("UPDATE `#__sitemapjen_links` SET `changefreq`='+scan' WHERE `id`='{$link['id']}'");
            $json['urls'][$link['id']] = $link;
        }
        if (count($json['urls']) > 0) {
            $json['action'] = 'scan init';
            $json['logs'][] = '';
            $json['threadsCount'] = $thr;
        } else {
            $json['error'] = 110;
            $json['logs'][] = 'Ошибка: не найдены адреса для сканирования.';
        }
    } elseif ($param == 2) {
        // ГЕНЕРАЦИЯ SITEMAP ПО ССЫЛКАМ ИЗ БАЗЫ
        // setOption( 'last_starttime', date('d.m.Y H:i') );
        setOption('task_url', '');
        setOption('task_action', 'generate');
        setOption('task_status', 'in_work');
        setOption('task_step', '0');
        $json['logs'][] = 'Генерация sitemap';
        $json['action'] = 'generate';
        // получаем количество адресов в базе
        $res = $SQL->query("SELECT COUNT(*) AS `count` FROM `#__sitemapjen_links`");
        $res = $res->fetch_assoc();
        // по нему определим какого вида sitemap генерировать.
        if ($res['count'] > 50000) {
            // составной sitemap
            setOption('task_gentype', '2');
        } else {
            // одиночный sitemap
            setOption('task_gentype', '1');
        }
        // удаляем старые файлы sitemap
        $files = glob($ROOT . 'sitemap*.xml');
        foreach ($files as $fl) {
            @unlink($fl);
        }
    } else {
        // НОВОЕ СКАНИРОВАНИЕ
        $url = isset($_POST['url']) ? intval($_POST['url']) : $domain;
        if (substr($url, 0, strlen($domain)) != $domain) {
            $url = $domain;
        }
        $url = rtrim($url, '/');
        // сбрасываем частоту изменений всех страниц в базе, как пометку о результате прошлого сканирования
        $SQL->query("UPDATE `#__sitemapjen_links` SET `changefreq`='-' WHERE `loc` LIKE '{$url}/%' OR `loc` LIKE '{$url}'");
        setOption('last_starttime', @date('d.m.Y H:i'));
        setOption('task_url', $url);
        setOption('task_action', 'scan');
        setOption('task_status', 'in_work');
        // резервируем несколько адресов для сканирования потоками
        $links = [];
        $res = $SQL->query("SELECT `id`,`loc` FROM `#__sitemapjen_links` WHERE `changefreq`='-' AND (`loc` LIKE '{$url}/%' OR `loc`='{$url}') LIMIT {$thr}");
        if ($res->num_rows > 0) {
            while ($link = $res->fetch_assoc()) {
                $links[] = $link;
            }
        } else {
            $SQL->query("INSERT INTO `#__sitemapjen_links` (`loc`,`changefreq`,`priority`) VALUES ('" . $SQL->real_escape_string($url) . "','-','0.5')");
            $links[] = ['id' => $SQL->insert_id, 'loc' => $url];
        }
        // готовим ответ для клиента
        foreach ($links as $i => $link) {
            $SQL->query("UPDATE `#__sitemapjen_links` SET `changefreq`='+scan' WHERE `id`='{$link['id']}'");
            $json['urls'][$link['id']] = $link;
        }
        if (count($json['urls']) > 0) {
            $json['logs'][] = 'Сканирование сайта';
            $json['threadsCount'] = $thr;
            $json['action'] = 'scan init';
        } else {
            $json['error'] = 110;
            $json['logs'][] = 'Ошибка инициализации: не найдены адреса для сканирования.';
        }
    }
    return json_encode($json);
}


function doStop()
{
    setOption('task_status', '');
    $json = [
        'action' => 'stop',
        'logs' => [],
        'urls' => [],
        'newcount' => 0,
        'threadsCount' => 0,
        'error' => 0,
    ];
    return json_encode($json);
}


function parseIgnoreList($list = '')
{
    $ignore = explode("\n", $list);
    foreach ($ignore as $i => $v) {
        $v = trim($v);
        if (empty($v)) {
            unset($ignore[$i]);
            continue;
        }
        $ignore[$i] = $v;
    }
    return $ignore;
}


// сканирование сайта
function doScan($mode)
{
    global $SQL;
    global $MYDIR;

    if ($mode == 'cron') {
        // режим CRON
        $log = [];
        // в режиме cron сначала проверяем наличие незавершенных процессов и только если они отсутствуют, запускаются новые
        $res = $SQL->query("SELECT `id`,`loc` FROM `#__sitemapjen_links` WHERE `changefreq`='+scan' OR `changefreq`='-' LIMIT 3");
        if ($res->num_rows > 0) {
            while ($link = $res->fetch_assoc()) {
                // загружаем страницу
                $page = loadPage($link['loc']);
                if ($page['content'] == '') {
                    $log[] = 'Ошибка, не удалось загрузить страницу: ' . $link['loc'];
                } else {
                    // сканируем страницу
                    $stat = scanPage($link, $page);
                    $log[] = '-&gt; ' . $link['loc'] . '............<b>+ ' . $stat . ' url</b>';
                }
            }
            saveLog($log);
        } else {
            // просканирована последняя страница
            $res = $SQL->query("SELECT COUNT(*) AS `count` FROM `#__sitemapjen_links`");
            $res = $res->fetch_assoc();
            $res2 = $SQL->query("SELECT COUNT(*) AS `count` FROM `#__sitemapjen_links` WHERE `changefreq`<>'-'");
            $res2 = $res->fetch_assoc();
            // переходим к новой операции
            setOption('task_action', 'generate');
            $log[] = 'Всего найдено ' . $res['count'] . ' и просканировано ' . $res2['count'] . ' адресов.';
            saveLog($log);
            saveLog("Приступаем к генерации sitemap\n");
        }
        return '';
    } else {
        // режим AJAX
        file_put_contents($MYDIR . 'ajaxtime.php', '<' . '?' . 'php $lastTime = ' . @time() . ';'); // время запроса
        $pnum = isset($_POST['pnum']) ? intval($_POST['pnum']) : 0; // номер процесса
        $need = isset($_POST['need']) ? intval($_POST['need']) : 1; // запрос дополнительных адресов для сканирования
        if ($need > 0) {
            $need++; // включая текущий
        }
        $json = [
            'action' => 'scan',
            'logs' => '',
            'urls' => array(),
            'thr' => $pnum,
            'newcount' => 0,
            'threadsCount' => 0,
            'error' => 0,
        ];
        if ($pnum > 0) {
            // в режиме ajax ищем зарезервированную для данного запроса запись
            $res = $SQL->query("SELECT `id`,`loc` FROM `#__sitemapjen_links` WHERE `id`='{$pnum}'");
            if ($res->num_rows > 0) {
                $link = $res->fetch_assoc();
                // загружаем страницу
                $page = loadPage($link['loc']);
                if ($page['content'] == '') {
                    $json['logs'][] = 'Ошибка, не удалось загрузить страницу: ' . $link['loc'];
                    $json['error'] = 510;
                } else {
                    // сканируем страницу
                    $json['newcount'] = scanPage($link, $page);
                }
            } else {
                $json['error'] = 120; // текущая ссылка не была найдена
            }
        } else {
            // запрос адресов (недостаточно потоков на стороне клиента)
        }
        // пытаемся получить новую ссылку(ки)
        if ($need > 0) {
            $res = $SQL->query("SELECT `id`,`loc` FROM `#__sitemapjen_links` WHERE `changefreq`='-' LIMIT {$need}");
            if ($res->num_rows > 0) {
                while ($link = $res->fetch_assoc()) {
                    $json['urls'][$link['id']] = $link;
                    $SQL->query("UPDATE `#__sitemapjen_links` SET `changefreq`='+scan' WHERE `id`='{$link['id']}'");
                }
            } else {
                // если доступных ссылок нет, возможно что ссылки закончились или просто не успели появиться новые.
                // посему не делаем поспешных выводов, а возвращаем пустой ответ, чтобы менеджер запросов на стороне клиента
                // смог сделать объективный вывод на основе других ответов.
                $json['error'] = 200; // доступных адресов не найдено
            }
        }
        return json_encode($json);
    }
    return;
}


function scanPage($link, $page)
{
    global $SQL;
    $now = @date('Y-m-d');
    // парсим ссылки
    $resgl = grabLinks($link['loc'], $page['content']);
    // сохраняем в базу
    $stat = saveLinks($resgl);
    // вычисляем хеш контента загруженной страницы
    $md5c = md5(grabContent($page['content']));
    $thisLoc = $SQL->query("SELECT `md5_content`,`lastmod` FROM `#__sitemapjen_links` WHERE `id`='{$link['id']}'");
    $thisLoc = $thisLoc->fetch_assoc();
    // обновляем текущую запись
    if ($thisLoc['md5_content'] != $md5c) { // если хеш контента изменился
        // вычисляем дату прошлого изменения
        if (empty($thisLoc['md5_content'])) { // если предыдущий хеш контента пустой, значит страница сканируется впервые
            $period = 'monthly';
        } else {
            $period = getPeriod($thisLoc['lastmod']);
        }
        $qu = "UPDATE `#__sitemapjen_links`
			SET `changefreq`='{$period}',`lastmod`='{$now}',`md5_content`='{$md5c}'
			WHERE `id`='{$link['id']}'";
    } else {
        $qu = "UPDATE `#__sitemapjen_links` SET `changefreq`='monthly' WHERE `id`='{$link['id']}'";
    }
    $SQL->query($qu);
    unset($page);
    return $stat;
}


// Парсит ссылки из страницы и обновляет информацию о них в базе
function grabLinks($url, &$page)
{
    global $options;
    global $domain;
    global $www;
    global $scheme;
    $total = []; // итоговый список спарсенных адресов
    $count = preg_match_all('/<a.+href="([^"]+)"/iU', $page, $res);
    if ($count > 0) {
        unset($res[0]);
        $res = $res[1];
        // фильтруем ссылки
        $ignore = parseIgnoreList($options['ignore_list']);
        foreach ($res as $href) {
            // исключаем js ссылки и тп
            if (substr($href, 0, 1) == '#' ||
                stripos($href, 'javascript') !== false ||
                stripos($href, 'print=') !== false ||
                stripos($href, 'mailto') !== false) {
                continue;
            }
            // корректируем ссылку
            if (!$www) {
                $href = str_replace($scheme . 'www.', $scheme, $href); // бывает что на сайте проскакивают адреса с www, в то время как сам сайт без www
            }
            if (strpos($href, 'http://') === false && strpos($href, 'https://') === false) {
                if (substr($href, 0, 1) == '/') {
                    // абсолютная ссылка
                    $href = $domain . $href;
                } else {
                    // относительная ссылка (корректируем с учетом адреса загруженной страницы)
                    if (substr($url, -1, 1) != '/') {
                        $url .= '/';
                    }
                    $href = $url . $href;
                }
            }
            // ссылки на внешние сайты
            if (substr($href, 0, strlen($domain)) != $domain) {
                continue;
            }
            // опускаем #якоря :)
            $href = explode('#', $href);
            $href = $href[0];
            $ext = explode('.', $href);
            $ext = end($ext);
            if (strlen($ext) < 5 && substr($ext, -1, 1) != '/') { // если у страницы есть расширение, возможно это файл, проверим
                if (!in_array($ext, ['php', 'aspx', 'htm', 'html', 'asp', 'cgi', 'pl'])) {
                    continue;
                }
            }
            // игнор-лист
            $iflag = false;
            foreach ($ignore as $iurl) {
                $iurl = str_replace($domain, '', $iurl);
                $part = str_replace($domain, '', $href);
                $part = substr($part, 0, strlen($iurl));
                if ($iurl == $part) {
                    $iflag = true;
                    break;
                }
            }
            if ($iflag == true) {
                continue;
            }
            // Исключать адреса вида "?option=com_"
            if ($options['ignore_option_com'] == 'Y' && stripos($href, '?option=com_') !== false) {
                continue;
            }
            // Исключать адреса вида "?query=value&..."
            if ($options['only_4pu'] == 'Y' && stripos($href, '?') !== false) {
                continue;
            }
            // Исключать ссылки "nofollow"
            if ($options['ignore_nofollow'] == 'Y' && preg_match('/ rel=("|\')?nofollow("|\')?( |\>){1}/', $href) > 0) {
                continue;
            }
            $total[] = rtrim($href, '/');
        }
        $total = array_unique($total);
        // print_r( $total );
    }
    return $total;
}


// сохраняет в базу ссылки, найденные парсером на странице
function saveLinks($links)
{
    global $SQL;
    $new = 0;
    // проверяем есть ли уже такая ссылка в базе
    $has = [];
    if (count($links) > 0) {
        $qu = "SELECT `loc` FROM `#__sitemapjen_links` WHERE ";
        foreach ($links as $i => $link) {
            if ($i > 0) {
                $qu .= " OR ";
            }
            $loc = $SQL->real_escape_string($link);
            $qu .= "`loc` LIKE '{$loc}'";
        }
        $res = $SQL->query($qu);
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $has[] = $row['loc'];
            }
        }
        unset($res);
    }
    // теперь смотрим, есть ли что добавить?
    if (count($has) < count($links)) {
        $ins = "INSERT INTO `#__sitemapjen_links` (`loc`,`lastmod`,`changefreq`,`priority`,`md5_content`) VALUES ";
        $now = @date('Y-m-d');
        $qu = [];
        foreach ($links as $i => $link) {
            $key = array_search($link, $has);
            if ($key === false) {
                // добавляем новую запись
                $loc = $SQL->real_escape_string($link);
                $qu[] = "('{$loc}','{$now}','-','0.5','')";
                $new++;
            }
        }
        if (count($qu) > 0) {
            $qu = $ins . implode(',', $qu);
            $SQL->query($qu);
        }
    }
    return $new;
}


// если в шаблоне есть теги <!--pagecontent--> и <!--/pagecontent-->
// то вырезаем все что находится между ними. Это и будет контентом страницы без шелухи.
function grabContent($content = '')
{
    $pos = strpos($content, '<!--pagecontent-->');
    if ($pos !== false) {
        $content = substr($content, $pos + 18);
    }
    $pos = strpos($content, '<!--/pagecontent-->');
    if ($pos !== false) {
        $content = substr($content, ($pos + 19) * (-1));
    }
    return strip_tags($content);
}


// генерация sitemap на основании базы
function doGenerate($mode)
{
    global $SQL;
    global $ROOT;
    global $options;
    global $domain;

    $json = [
        'action' => 'generate',
        'logs' => '',
        'urls' => [],
        'thr' => '0',
        'newcount' => 0,
        'threadsCount' => 0,
        'error' => 0,
    ];

    $limit = 50000;
    $next = intval(@$options['task_step']);
    $type = $options['task_gentype'];

    // номер файла
    $num = '';
    if ($type == '2') {
        if ($next == 0) {
            $num = '1';
        }
        if ($next >= $limit) {
            $num = intval($next / $limit) + 1;
        }
    }
    $file = 'sitemap' . $num . '.xml';

    // считываем очередные N адресов из базы
    $res = $SQL->query("SELECT `loc`,`lastmod`,`changefreq`,`priority` FROM `#__sitemapjen_links` ORDER BY `lastmod` DESC LIMIT {$next},{$limit}");
    if ($res->num_rows > 0) {
        $fl = fopen($ROOT . $file, 'w');
        $head = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        if (fwrite($fl, $head) === false) {
            return json_encode(['error' => 600, 'logs' => ['Ошибка записи в файл!']]);
        }
        $total = 0;
        while ($link = $res->fetch_assoc()) {
            $loc = str_replace('&', '&amp;', $link['loc']);
            if (!empty($link['lastmod'])) {
                $link['lastmod'] = '	<lastmod>' . $link['lastmod'] . '</lastmod>' . "\n";
            }
            if ($link['changefreq'] != '-' && $link['changefreq'] != '+scan') {
                $link['changefreq'] = '	<changefreq>' . $link['changefreq'] . '</changefreq>' . "\n";
            } else {
                $link['changefreq'] = '';
            }
            fwrite($fl,
                '<url>' . "\n" .
                '	<loc>' . $loc . '</loc>' . "\n" .
                $link['lastmod'] .
                $link['changefreq'] .
                '	<priority>' . $link['priority'] . '</priority>' . "\n" .
                '</url>' . "\n"
            );
            $total++;
        }
        fwrite($fl, '</urlset>');
        fclose($fl);
        $json['logs'][] = 'generated -> ' . $file;
        if ($total < $limit) {
            // если прочитанное количество записей меньше лимита, значит больше адресов в базе нет
            // генерация завершена
            if ($type == '2') {
                // нужен составной sitemap
                $files = glob($ROOT . 'sitemap*.xml');
                $fl = fopen($ROOT . 'sitemap.xml', 'w');
                $head = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
                if (fwrite($fl, $head) === false) {
                    return json_encode(['error' => 600, 'logs' => ['Ошибка записи в файл!']]);
                }
                foreach ($files as $xml) {
                    $loc = $domain . '/' . basename($xml);
                    fwrite($fl,
                        '<sitemap>' . "\n" .
                        '	<loc>' . $loc . '</loc>' . "\n" .
                        '	<lastmod>' . @date('Y-m-d') . '</lastmod>' . "\n" .
                            '</sitemap>' . "\n"
                    );
                }
                fwrite($fl, '</sitemapindex>');
                fclose($fl);
                $json['logs'][] = 'index map -> sitemap.xml';
                $json['logs'][] = 'Всего ' . ($next + $total) . ' ссылок в ' . $num . ' файлах.';
            } else {
                $json['logs'][] = 'Всего ' . $total . ' ' . modifWordByCount(['ссылка', 'ссылки', 'ссылок'], $total) . '.';
            }
            $json['action'] = 'end';
            setOption('task_action', '');
            setOption('task_status', '');
        } else {
            $next += $limit;
            setOption('task_step', $next);
        }
    } else {
        // нет записей - вообще нет...
        $json['logs'][] = 'В базе нет ссылок для генерации sitemap.';
        $json['action'] = 'stop';
        setOption('task_action', '');
        setOption('task_status', '');
    }
    if ($mode == 'cron') {
        saveLog($json['logs']);
        return '';
    }
    return json_encode($json);
}


function loadPage($url)
{
    // на случай, если не установлен curl
    if (!IS_CURL) {
        $cnt = file_get_contents($url);
        $header = ['content' => $cnt, 'errno' => 0, 'errmsg' => ''];
    } else {
        $options = [
            CURLOPT_CUSTOMREQUEST => "GET", //set request type post or get
            CURLOPT_POST => false, //set to GET
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'], //set user agent
            CURLOPT_COOKIEFILE => ".cookie.txt", //set cookie file
            CURLOPT_COOKIEJAR => ".cookie.txt", //set cookie jar
            CURLOPT_RETURNTRANSFER => true, // return web page
            CURLOPT_HEADER => false, // don't return headers
            CURLOPT_FOLLOWLOCATION => true, // follow redirects
            CURLOPT_ENCODING => "", // handle all encodings
            CURLOPT_AUTOREFERER => true, // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
            CURLOPT_TIMEOUT => 120, // timeout on response
            CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
        ];
        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        $header = curl_getinfo($ch);
        $header['errno'] = curl_errno($ch);
        $header['errmsg'] = curl_error($ch);
        $header['content'] = $content;
        curl_close($ch);
    }
    return $header;
}


// вычисляет период, за который поменялся контент страницы с последнего сканирования
function getPeriod($date)
{
    // - always
    // - hourly
    // + daily
    // + weekly
    // + monthly
    // + yearly
    // - never
    //конвертируем в timestamp (в секунды)
    $arr = explode(' ', $date);
    $arr = explode('-', $arr[0]);
    // mktime( 0, 0, 0, 12, 32, 1997 )
    $timestamp2 = @mktime(0, 0, 0, @date('m'), @date('d'), @date('Y'));
    $timestamp1 = @mktime(0, 0, 0, $arr[1], $arr[2], $arr[0]);
    $days = floor(($timestamp2 - $timestamp1) / 86400);
    $period = 'yearly';
    if ($days <= 2) { // ну один день сверху накинем чо.
        $period = 'daily';
    } elseif ($days > 2 && $days <= 8) {
        $period = 'weekly';
    } elseif ($days > 8 && $days <= 31) {
        $period = 'monthly';
    } elseif ($days > 31 && $days <= 365) {
        $period = 'yearly';
    }
    return $period;
}


function getOptions()
{
    global $SQL;
    // считываем текущие настройки
    $res = $SQL->query("SELECT * FROM `#__sitemapjen_options`");
    $opt = [];
    while ($row = $res->fetch_assoc()) {
        $opt[$row['param']] = $row['value'];
    }
    // проверяем список исключаемых адресов, если он пуст, сканируем robots.txt на наличие disallow
    if (trim($opt['ignore_list']) == '') {
        $opt['ignore_list'] = parseRobotstxt();
    }
    return $opt;
}


function parseRobotstxt()
{
    global $ROOT;
    $disallow = '';
    if (is_file($ROOT . 'robots.txt')) {
        $cnt = file($ROOT . 'robots.txt');
        foreach ($cnt as $line) {
            $line = trim($line);
            if (substr($line, 0, 9) == 'Disallow:') {
                $disallow .= trim(substr($line, 9)) . "\n";
            }
        }
    }
    return $disallow;
}


// сохранение настроек
function saveOptions()
{
    global $SQL;
    // считываем текущие настройки
    $res = $SQL->query("SELECT * FROM `#__sitemapjen_options`");
    while ($row = $res->fetch_assoc()) {
        if (substr($row['param'], 0, 5) == 'task_') {
            continue;
        }
        $pName = isset($_POST[$row['param']]) ? $_POST[$row['param']] : false;
        if ($pName !== false) {
            $param = $SQL->real_escape_string($pName);
            $SQL->query("UPDATE `#__sitemapjen_options` SET `value`='{$param}' WHERE `param`='{$row['param']}'");
        }
    }
    return true;
}


function setOption($name, $value)
{
    global $SQL;
    $name = $SQL->real_escape_string($name);
    $value = $SQL->real_escape_string($value);
    $SQL->query("UPDATE `#__sitemapjen_options` SET `value`='{$value}' WHERE `param`='{$name}'");
}


/**
 * Фукцния склоняет слова в соответствии с числовым значением.
 * @param array $words например: array('слон','слона','слонов')
 * @param int $number например: 7
 * @return string вернет "слонов"
 */
function modifWordByCount($words, $number)
{
    $result = '';
    if (!is_array($words) || count($words) < 3) {
        return 'ERR<!--Ошибка modifWordByCount(): аргумент $words должен быть массивом из трех ячеек.-->';
    }
    if ($number == 0) {
        $result = $words[2];
    } elseif ($number == 1) {
        $result = $words[0];
    } elseif (($number > 20) && (($number % 10) == 1)) {
        $result = $words[2];
    } elseif ((($number >= 2) && ($number <= 4)) || ((($number % 10) >= 2) && (($number % 10) <= 4)) && ($number > 20)) {
        $result = $words[1];
    } else {
        $result = $words[2];
    }
    return $result;
}


function saveLog($logs = [])
{
    global $MYDIR;
    $lines = [];
    if (is_file($MYDIR . 'cron-log.txt')) {
        $lines = file($MYDIR . 'cron-log.txt');
    }
    if (count($lines) > 100) {
        for ($i = 0; $i < 20; $i++) {
            unset($lines[$i]);
        }
    }
    foreach ($logs as $line) {
        $lines[] = '<div class="line">' . $line . '</div>' . "\n";
    }
    $log = implode('', $lines);
    file_put_contents('cron-log.txt', $log, FILE_APPEND);
}