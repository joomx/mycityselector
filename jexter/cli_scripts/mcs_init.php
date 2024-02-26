<?php
/**
 * Registers MCS extension in DB
 */

exit('TODO переделать этот скрипт на использование классов джумлы для установки шатаными методами' . "\n");

$siteRoot = realpath(JEXTER_DIR . '/../');

$db = JFactory::getDbo();
$config = JFactory::getConfig();
$dbUser = $config->get('user');
$dbPwd = $config->get('password');
$database = $config->get('db');
$dbPrefix = $config->get('dbprefix');


out(" Registering MCS extension ... ", 'light_blue');
// TODO это не очень красиво, но пока так
$query[] = <<<SQL
INSERT INTO `mcs_menu` (`menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES
('main', 'COM_MYCITYSELECTOR_NAME', 'com-mycityselector-name', '', 'com-mycityselector-name', 'index.php?option=com_mycityselector', 'component', 0, 1, 1, 10003, 0, '0000-00-00 00:00:00', 0, 1, '/administrator/components/com_mycityselector/images/bubble-comment.png', 0, '{}', 65, 76, 0, '', 1),
('main', 'COM_MYCITYSELECTOR_COUNTRIES', 'com-mycityselector-countries', '', 'com-mycityselector-name/com-mycityselector-countries', 'index.php?option=com_mycityselector', 'component', 0, 114, 2, 10003, 0, '0000-00-00 00:00:00', 0, 1, 'class:module', 0, '{}', 66, 67, 0, '', 1),
('main', 'COM_MYCITYSELECTOR_PROVINCES', 'com-mycityselector-provinces', '', 'com-mycityselector-name/com-mycityselector-provinces', 'index.php?option=com_mycityselector&controller=province', 'component', 0, 114, 2, 10003, 0, '0000-00-00 00:00:00', 0, 1, 'class:module', 0, '{}', 68, 69, 0, '', 1),
('main', 'COM_MYCITYSELECTOR_CITIES', 'com-mycityselector-cities', '', 'com-mycityselector-name/com-mycityselector-cities', 'index.php?option=com_mycityselector&controller=city', 'component', 0, 114, 2, 10003, 0, '0000-00-00 00:00:00', 0, 1, 'class:module', 0, '{}', 70, 71, 0, '', 1),
('main', 'COM_MYCITYSELECTOR_FIELDS', 'com-mycityselector-fields', '', 'com-mycityselector-name/com-mycityselector-fields', 'index.php?option=com_mycityselector&controller=fields', 'component', 0, 114, 2, 10003, 0, '0000-00-00 00:00:00', 0, 1, 'class:module', 0, '{}', 72, 73, 0, '', 1),
('main', 'COM_MYCITYSELECTOR_OPTIONS', 'com-mycityselector-options', '', 'com-mycityselector-name/com-mycityselector-options', 'index.php?option=com_config&view=component&component=com_mycityselector', 'component', 0, 114, 2, 10003, 0, '0000-00-00 00:00:00', 0, 1, 'class:module', 0, '{}', 74, 75, 0, '', 1);
SQL;
$query[] = <<<SQL
INSERT INTO `{$dbPrefix}extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES
('plg_editors-xtd_mcsinsert', 'plugin', 'mcsinsert', 'editors-xtd', 0, 1, 1, 0, '{\"name\":\"plg_editors-xtd_mcsinsert\",\"type\":\"plugin\",\"creationDate\":\"Jan 2017\",\"author\":\"Konstantin Kutsevalov & Vladislav Smolenskiy\",\"copyright\":\"\",\"authorEmail\":\"konstantin@kutsevalov.name\",\"authorUrl\":\"www.kutsevalov.name\",\"version\":\"\",\"description\":\"\\n\\t<h3 class=\\\"description-module-header\\\">My City Selector<\\/h3>\\n\\t<div class=\\\"description-module\\\" style=\\\"text-align:left;\\\">\\n\\t\\t\\u0414\\u0430\\u043d\\u043d\\u044b\\u0439 \\u043f\\u043b\\u0430\\u0433\\u0438\\u043d \\u044f\\u0432\\u043b\\u044f\\u0435\\u0442\\u0441\\u044f \\u0447\\u0430\\u0441\\u0442\\u044c\\u044e \\u0440\\u0430\\u0441\\u0448\\u0438\\u0440\\u0435\\u043d\\u0438\\u044f \\\"MyCitySelector\\\".\\n\\t<\\/div>\\n\\t\",\"group\":\"\",\"filename\":\"mcsinsert\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 9991, 0),
('System - My City Selector', 'plugin', 'plgmycityselector', 'system', 0, 1, 1, 0, '{\"name\":\"System - My City Selector\",\"type\":\"plugin\",\"creationDate\":\"Jan 2017\",\"author\":\"Konstantin Kutsevalov & Vladislav Smolenskiy\",\"copyright\":\"\",\"authorEmail\":\"konstantin@kutsevalov.name\",\"authorUrl\":\"www.kutsevalov.name\",\"version\":\"\",\"description\":\"\\n\\t\\n\\t<h3>My City Selector (Content plugin)<\\/h3>\\n\\t\\u0414\\u0430\\u043d\\u043d\\u044b\\u0439 \\u043f\\u043b\\u0430\\u0433\\u0438\\u043d \\u044f\\u0432\\u043b\\u044f\\u0435\\u0442\\u0441\\u044f \\u0447\\u0430\\u0441\\u0442\\u044c\\u044e \\u0440\\u0430\\u0441\\u0448\\u0438\\u0440\\u0435\\u043d\\u0438\\u044f \\\"MyCitySelector\\\".\\n\\t<br\\/>\\n\\t@devnote TODO \\u0418\\u0417\\u041c\\u0415\\u041d\\u0418\\u0422\\u042c \\u041e\\u041f\\u0418\\u0421\\u0410\\u041d\\u0418\\u0415 \\u0418 \\u0421\\u0421\\u042b\\u041b\\u041a\\u0423 \\u041d\\u0410 \\u0421\\u0410\\u0419\\u0422\\n\\t\\u0411\\u043e\\u043b\\u0435\\u0435 \\u043f\\u043e\\u0434\\u0440\\u043e\\u0431\\u043d\\u043e\\u0435 \\u043e\\u043f\\u0438\\u0441\\u0430\\u043d\\u0438\\u0435 \\u0447\\u0438\\u0442\\u0430\\u0439\\u0442\\u0435 <a href=\\\"http:\\/\\/www.kutsevalov.name\\/2013\\/08\\/13\\/modul-vibora-goroda-dlya-joomla\\/\\\" title=\\\"\\u043f\\u0435\\u0440\\u0435\\u0439\\u0442\\u0438\\\" target=\\\"_blank\\\">\\u043d\\u0430 \\u0441\\u0442\\u0440\\u0430\\u043d\\u0438\\u0446\\u0435 \\u043c\\u043e\\u0434\\u0443\\u043b\\u044f<\\/a>.\\n\\t\\n\\t\",\"group\":\"\",\"filename\":\"plgmycityselector\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 9990, 0),
('My City Selector MOD', 'module', 'mod_mycityselector', '', 0, 1, 0, 0, '{\"name\":\"My City Selector MOD\",\"type\":\"module\",\"creationDate\":\"Jan 2017\",\"author\":\"Konstantin Kutsevalov & Vladislav Smolenskiy\",\"copyright\":\"All rights reserved\",\"authorEmail\":\"konstantin@kutsevalov.name\",\"authorUrl\":\"http:\\/\\/www.kutsevalov.name\\/2013\\/08\\/12\\/modul-vibora-goroda-dlya-joomla\\/\",\"version\":\"\",\"description\":\"\\n\\t<h3 class=\\\"description-module-header\\\">My City Selector<\\/h3>\\n\\t<div class=\\\"description-module\\\" style=\\\"text-align:left;\\\">\\n\\t\\t\\u0414\\u0430\\u043d\\u043d\\u044b\\u0439 \\u043c\\u043e\\u0434\\u0443\\u043b\\u044c \\u044f\\u0432\\u043b\\u044f\\u0435\\u0442\\u0441\\u044f \\u0447\\u0430\\u0441\\u0442\\u044c\\u044e \\u0440\\u0430\\u0441\\u0448\\u0438\\u0440\\u0435\\u043d\\u0438\\u044f \\\"MyCitySelector\\\".\\n\\t<\\/div>\\n\\t\",\"group\":\"\",\"filename\":\"mod_mycityselector\"}', '{\"dialog_title\":\"\\u041f\\u043e\\u0436\\u0430\\u043b\\u0443\\u0439\\u0441\\u0442\\u0430, \\u0432\\u044b\\u0431\\u0435\\u0440\\u0438\\u0442\\u0435 \\u0432\\u0430\\u0448 \\u0433\\u043e\\u0440\\u043e\\u0434\",\"cities_list_type\":\"0\",\"cache\":\"0\",\"cache_time\":\"900\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
('Mycityselector', 'component', 'com_mycityselector', '', 1, 1, 0, 0, '{\"name\":\"Mycityselector\",\"type\":\"component\",\"creationDate\":\"Jan 2017\",\"author\":\"Konstantin Kutsevalov & Vladislav Smolenskiy\",\"copyright\":\"All rights reserved\",\"authorEmail\":\"konstantin@kutsevalov.name\",\"authorUrl\":\"http:\\/\\/kutsevalov.name\",\"version\":\"\",\"description\":\"COM_MYCITYSELECTOR_DESCRIPTION\",\"group\":\"\",\"filename\":\"com_mycityselector\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
('My City Selector', 'package', 'pkg_mycityselector', '', 0, 1, 1, 0, '{\"name\":\"My City Selector\",\"type\":\"package\",\"creationDate\":\"Jan 2017\",\"author\":\"Konstantin Kutsevalov & Vladislav Smolenskiy\",\"copyright\":\"All rights reserved\",\"authorEmail\":\"konstantin@kutsevalov.name\",\"authorUrl\":\"http:\\/\\/www.kutsevalov.name\\/2013\\/08\\/12\\/modul-vibora-goroda-dlya-joomla\\/\",\"version\":\"\",\"description\":\"\\n\\t\\t\\n\\t\",\"group\":\"\",\"filename\":\"pkg_mycityselector\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0);
SQL;
$query[] = <<<SQL
INSERT INTO `{$dbPrefix}modules` (`asset_id`, `title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`) VALUES
(0, 'My City Selector MOD', '', '', 0, 'position-0', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 'mod_mycityselector', 1, 1, '', 0, '*');
SQL;
foreach ($query as $q) {
    if (!$db->setQuery($q) || !$db->execute()) {
        $err = $db->getLastError();
        out("failed. Error: {$err}\n", 'red');
        exit;
    }
}
$sqlFile = $siteRoot . '/administrator/components/com_mycityselector/sql/install.sql';
if (!is_file($sqlFile)) {
    out("failed. SQL instructions not found.\n", 'red');
    exit;
}
$lines = file($sqlFile);
foreach ($lines as &$line) {
    $line = str_replace('#__', $dbPrefix, $line);
}
$lines = implode('', $lines);
file_put_contents(JEXTER_DIR . '/tmp/_install.sql', $lines);
$path = realpath(JEXTER_DIR . '/tmp/_install.sql');
system("mysql -u {$dbUser} -p{$dbPwd} {$database} < {$path}", $res);
if ($res !== 0) {
    out("failed.\n", 'red');
    exit;
}
out("OK\n", 'light_blue');
