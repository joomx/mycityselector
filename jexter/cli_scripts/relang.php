<?php
/**
 * Refresh languages files from component folder to system lang folder
 */

$root = __DIR__ . '/../../';

// component
out("   admin/en-GB.com_mycityselector.ini\n", "cyan");
@copy($root.'/administrator/components/com_mycityselector/language/en-GB/en-GB.com_mycityselector.ini', $root.'/administrator/language/en-GB/en-GB.com_mycityselector.ini');
out("   admin/en-GB.com_mycityselector.sys.ini\n", "cyan");
@copy($root.'/administrator/components/com_mycityselector/language/en-GB/en-GB.com_mycityselector.sys.ini', $root.'/administrator/language/en-GB/en-GB.com_mycityselector.sys.ini');

out("   admin/ru-RU.com_mycityselector.ini\n", "cyan");
@copy($root.'/administrator/components/com_mycityselector/language/ru-RU/ru-RU.com_mycityselector.ini', $root.'/administrator/language/ru-RU/ru-RU.com_mycityselector.ini');
out("   admin/ru-RU.com_mycityselector.sys.ini\n", "cyan");
@copy($root.'/administrator/components/com_mycityselector/language/ru-RU/ru-RU.com_mycityselector.sys.ini', $root.'/administrator/language/ru-RU/ru-RU.com_mycityselector.sys.ini');

out("   site/en-GB.com_mycityselector.sys.ini\n", "cyan");
@copy($root.'/components/com_mycityselector/language/en-GB/en-GB.com_mycityselector.ini', $root.'/language/en-GB/en-GB.com_mycityselector.ini');
out("   site/en-GB.com_mycityselector.sys.ini\n", "cyan");
@copy($root.'/components/com_mycityselector/language/en-GB/en-GB.com_mycityselector.sys.ini', $root.'/language/en-GB/en-GB.com_mycityselector.sys.ini');

out("   site/ru-RU.com_mycityselector.ini\n", "cyan");
@copy($root.'/components/com_mycityselector/language/ru-RU/ru-RU.com_mycityselector.ini', $root.'/language/ru-RU/ru-RU.com_mycityselector.ini');
out("   site/ru-RU.com_mycityselector.sys.ini\n", "cyan");
@copy($root.'/components/com_mycityselector/language/ru-RU/ru-RU.com_mycityselector.sys.ini', $root.'/language/ru-RU/ru-RU.com_mycityselector.sys.ini');

// module
out("   module/ru-RU.mod_mycityselector.ini\n", "cyan");
@copy($root.'/modules/mod_mycityselector/language/ru-RU/ru-RU.mod_mycityselector.ini', $root.'/language/ru-RU/ru-RU.mod_mycityselector.ini');
out("   module/ru-RU.mod_mycityselector.sys.ini\n", "cyan");
@copy($root.'/modules/mod_mycityselector/language/ru-RU/ru-RU.mod_mycityselector.sys.ini', $root.'/language/ru-RU/ru-RU.mod_mycityselector.sys.ini');

out("   module/en-GB.mod_mycityselector.ini\n", "cyan");
@copy($root.'/modules/mod_mycityselector/language/en-GB/en-GB.mod_mycityselector.ini', $root.'/language/en-GB/en-GB.mod_mycityselector.ini');
out("   module/en-GB.mod_mycityselector.sys.ini\n", "cyan");
@copy($root.'/modules/mod_mycityselector/language/en-GB/en-GB.mod_mycityselector.sys.ini', $root.'/language/en-GB/en-GB.mod_mycityselector.sys.ini');