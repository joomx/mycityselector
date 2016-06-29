<?php
/**
 * Refresh languages files from component folder to system lang folder
 */

$root = __DIR__ . '/../../';
out("admin/en-GB.com_mycityselector.ini\n", "green");
@copy($root.'/administrator/components/com_mycityselector/language/en-GB/en-GB.com_mycityselector.ini', $root.'/administrator/language/en-GB/en-GB.com_mycityselector.ini');
out("admin/en-GB.com_mycityselector.sys.ini\n", "green");
@copy($root.'/administrator/components/com_mycityselector/language/en-GB/en-GB.com_mycityselector.sys.ini', $root.'/administrator/language/en-GB/en-GB.com_mycityselector.sys.ini');

out("admin/ru-RU.com_mycityselector.ini\n", "green");
@copy($root.'/administrator/components/com_mycityselector/language/ru-RU/ru-RU.com_mycityselector.ini', $root.'/administrator/language/ru-RU/ru-RU.com_mycityselector.ini');
out("admin/ru-RU.com_mycityselector.sys.ini\n", "green");
@copy($root.'/administrator/components/com_mycityselector/language/ru-RU/ru-RU.com_mycityselector.sys.ini', $root.'/administrator/language/ru-RU/ru-RU.com_mycityselector.sys.ini');

out("site/en-GB.com_mycityselector.sys.ini\n", "green");
@copy($root.'/components/com_mycityselector/language/en-GB/en-GB.com_mycityselector.sys.ini', $root.'/language/en-GB/en-GB.com_mycityselector.sys.ini');
out("site/en-GB.com_mycityselector.sys.ini\n", "green");
@copy($root.'/components/com_mycityselector/language/en-GB/en-GB.com_mycityselector.sys.ini', $root.'/language/en-GB/en-GB.com_mycityselector.sys.ini');

out("site/ru-RU.com_mycityselector.ini\n", "green");
@copy($root.'/components/com_mycityselector/language/ru-RU/ru-RU.com_mycityselector.ini', $root.'/language/ru-RU/ru-RU.com_mycityselector.ini');
out("site/ru-RU.com_mycityselector.sys.ini\n", "green");
@copy($root.'/components/com_mycityselector/language/ru-RU/ru-RU.com_mycityselector.sys.ini', $root.'/language/ru-RU/ru-RU.com_mycityselector.sys.ini');
