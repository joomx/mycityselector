<?php
/**
 * Demo script for "jrun" command
 */

out("Read users\nEnter count: ", "cyan");
$input = intval($this->in());
$limit = "";
if ($input > 0) {
    $limit = "";
} else {
    $limit = " LIMIT {$input}";
}
$db->setQuery("SELECT `username` from #__users" . $limit);
$result = $db->loadAssocList();
foreach ($result as $user) {
    out("  {$user['username']}\n", 'light_blue');
}