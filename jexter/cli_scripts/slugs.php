<?php
/**
 * TODO переделать, чтобы создавались slugs для всех записей, не только городов
 * TODO условие: если данное поле у записи пустое
 *
 */


function slugTranslit($str)
{
    $letters = [
        'й' => 'j', 'ц' => 'ts', 'у' => 'u', 'к' => 'k', 'е' => 'e', 'н' => 'n', 'г' => 'g', 'ш' => 'sh', 'щ' => 'sch',
        'з' => 'z', 'х' => 'h', 'ъ' => '', 'ф' => 'f', 'ы' => 'y', 'в' => 'v', 'а' => 'a', 'п' => 'p', 'р' => 'r', 'о' => 'o',
        'л' => 'l', 'д' => 'd', 'ж' => 'zh', 'э' => 'e', 'я' => 'ya', 'ч' => 'ch', 'с' => 's', 'м' => 'm', 'и' => 'i',
        'т' => 't', 'ь' => '', 'б' => 'b', 'ю' => 'yu', 'ё' => 'e',
        'Й' => 'j', 'Ц' => 'ts', 'У' => 'u', 'К' => 'k', 'Е' => 'e', 'Н' => 'n', 'Г' => 'g', 'Ш' => 'sh', 'Щ' => 'sch',
        'З' => 'z', 'Х' => 'h', 'Ъ' => '', 'Ф' => 'f', 'Ы' => 'y', 'В' => 'v', 'А' => 'a', 'П' => 'p', 'Р' => 'r', 'О' => 'o',
        'Л' => 'l', 'Д' => 'd', 'Ж' => 'zh', 'Э' => 'e', 'Я' => 'ya', 'Ч' => 'ch', 'С' => 's', 'М' => 'm', 'И' => 'i',
        'Т' => 't', 'Ь' => '', 'Б' => 'b', 'Ю' => 'yu', 'Ё' => 'e', ' ' => '-', ',' => '-', '?' => '-', '!' => '-',
        '/' => '-', '(' => '', ')' => '', '___' => '-', '__' => '-'
    ];
    foreach ($letters as $key => $value) {
        $str = str_replace($key, $value, $str);
    }
    return $str;
}

// TODO rewrite for new tables

$db->setQuery("SELECT * from `#__mycityselector_city`");
$result = $db->loadAssocList();
foreach ($result as $city) {
    $city['subdomain'] = slugTranslit(trim($city['name']));
    $db->setQuery("UPDATE `#__mycityselector_city` SET `subdomain` = '{$city['subdomain']}' WHERE `id` = {$city['id']}");
    $db->execute();
    out("   '{$city['name']}' => '{$city['subdomain']}'\n", 'light_blue');
}