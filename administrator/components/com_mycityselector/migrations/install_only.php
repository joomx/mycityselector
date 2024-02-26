<?php
/**
 * Эта миграция выполняется только для новой установки или удалении
 *
 * TODO перед создание своей миграции, загляни в /administrator/components/com_mycityselector/migrations/readme.md
 */

class migration_install_only extends MigraObject
{

    /**
     * Описание всех таблиц вынесено в массив, чтобы его можно было прочитать в инсталлере для
     * проверки корректности установки расширения
     * @return array
     */
    static public function tables()
    {
        return [
            'mycityselector_langs' => [
                "`id` INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
                "`name` VARCHAR(120) NOT NULL ",
                "`locale` VARCHAR(10) NOT NULL",
                "`default` TINYINT NOT NULL DEFAULT '0'",
            ],
            'mycityselector_countries' => [
                "`id` INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT",
                "`subdomain` VARCHAR(80) NOT NULL",
                "`published` TINYINT(1)  NOT NULL DEFAULT '0'",
                "`ordering` INT(11) NULL",
                "`code` VARCHAR(2) NOT NULL",
                "`domain` VARCHAR(255) NULL",
                "`lat` DECIMAL(10, 8)",
                "`lng` DECIMAL(11, 8)",
                "`defult_city_id` INT(11) NULL"
            ],
            'mycityselector_country_names' => [
                "`id` INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
                "`country_id` INT(11) NOT NULL",
                "`lang_id` INT(11) UNSIGNED NOT NULL COMMENT 'from #__languages of Joomla'",
                "`name` VARCHAR(50) NOT NULL",
                "CONSTRAINT `mcs_country_names_fk` FOREIGN KEY (`country_id`) REFERENCES `#__mycityselector_countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
                "CONSTRAINT `mcs_country_name_lang_fk` FOREIGN KEY (`lang_id`) REFERENCES `#__mycityselector_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE"
            ],
            'mycityselector_provinces' => [
                "`id` INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT",
                "`country_id` INT(11) NOT NULL",
                "`subdomain` VARCHAR(80) NOT NULL",
                "`lat` DECIMAL(10, 8)",
                "`lng` DECIMAL(11, 8)",
                "`published` TINYINT(1)  NOT NULL DEFAULT '0'",
                "`ordering` INT(11) DEFAULT NULL",
                "CONSTRAINT `mcs_province_country_fk` FOREIGN KEY (`country_id`) REFERENCES `#__mycityselector_countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE"
            ],
            'mycityselector_province_names' => [
                "`id` INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
                "`province_id` INT(11) NOT NULL",
                "`lang_id` INT(11) UNSIGNED NOT NULL COMMENT 'from #__languages of Joomla'",
                "`name` VARCHAR(80) NOT NULL",
                "CONSTRAINT `mcs_province_names_fk` FOREIGN KEY (`province_id`) REFERENCES `#__mycityselector_provinces` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
                "CONSTRAINT `mcs_province_name_lang_fk` FOREIGN KEY (`lang_id`) REFERENCES `#__mycityselector_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE"
            ],
            'mycityselector_cities' => [
                "`id` INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT",
                "`country_id` INT(11) DEFAULT NULL",
                "`province_id` INT(11) DEFAULT NULL",
                "`subdomain` VARCHAR(80) NOT NULL",
                "`post_index` VARCHAR(10) NOT NULL DEFAULT ''",
                "`lat` DECIMAL(10, 8)",
                "`lng` DECIMAL(11, 8)",
                "`published` TINYINT(1) NOT NULL DEFAULT '0'",
                "`ordering` INT(11) DEFAULT NULL",
                "CONSTRAINT `mcs_city_country_fk` FOREIGN KEY (`country_id`) REFERENCES `#__mycityselector_countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
                "CONSTRAINT `mcs_city_province_fk` FOREIGN KEY (`province_id`) REFERENCES `#__mycityselector_provinces` (`id`) ON DELETE CASCADE ON UPDATE CASCADE"
            ],
            'mycityselector_city_names' => [
                "`id` INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
                "`city_id` INT(11) NOT NULL",
                "`lang_id` INT(11) UNSIGNED NOT NULL COMMENT 'from #__languages of Joomla'",
                "`name` VARCHAR(80) NOT NULL",
                "CONSTRAINT `mcs_city_names_fk` FOREIGN KEY (`city_id`) REFERENCES `#__mycityselector_cities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
                "CONSTRAINT `mcs_city_name_lang_fk` FOREIGN KEY (`lang_id`) REFERENCES `#__mycityselector_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE"
            ],
            'mycityselector_field' => [
                "`id` INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
                "`name` VARCHAR(45) DEFAULT NULL",
                "`published` TINYINT(1) NOT NULL DEFAULT '0'"
            ],
            'mycityselector_field_value' => [
                "`id` INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
                "`field_id` INT(11) UNSIGNED NOT NULL",
                "`value` TEXT",
                "`default` TINYINT(1) NOT NULL DEFAULT '0'",
                "`is_ignore` TINYINT(1) NOT NULL DEFAULT '0'",
                "CONSTRAINT `mcs_values_fields_fk` FOREIGN KEY (`field_id`) REFERENCES `#__mycityselector_field` (`id`) ON DELETE CASCADE ON UPDATE CASCADE"
            ],
            'mycityselector_value_city' => [
                "`id` INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
                "`field_value_id` INT(11) UNSIGNED NOT NULL",
                "`city_id` INT(11) NOT NULL",
                "CONSTRAINT `mcs_values_fields_fkk` FOREIGN KEY (`field_value_id`) REFERENCES `#__mycityselector_field_value` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
                "CONSTRAINT `mcs_values_cities_fk` FOREIGN KEY (`city_id`) REFERENCES `#__mycityselector_cities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE"
            ],
            'mycityselector_city_cases' => [
                "`id` INT(11) UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT",
                "`city_id` INT(11) NOT NULL",
                "`case_id` INT(11) UNSIGNED NOT NULL",
                "`lang_id` INT(11) UNSIGNED NOT NULL",
                "`value` VARCHAR(50) NOT NULL",
                "CONSTRAINT `mcs_city_cases_fk` FOREIGN KEY (`city_id`) REFERENCES `#__mycityselector_cities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
                "CONSTRAINT `mcs_city_cases_langs_fk` FOREIGN KEY (`lang_id`) REFERENCES `#__mycityselector_langs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE"
            ],
            'mycityselector_value_province' => [
                "`id` INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
                "`field_value_id` INT(11) UNSIGNED NOT NULL",
                "`province_id` INT(11) NOT NULL",
                "CONSTRAINT `mcs_province_values_fields_fkk` FOREIGN KEY (`field_value_id`) REFERENCES `#__mycityselector_field_value` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
                "CONSTRAINT `mcs_values_provinces_fk` FOREIGN KEY (`province_id`) REFERENCES `#__mycityselector_provinces` (`id`) ON DELETE CASCADE ON UPDATE CASCADE"
            ],
            'mycityselector_value_country' => [
                "`id` INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
                "`field_value_id` INT(11) UNSIGNED NOT NULL",
                "`country_id` INT(11) NOT NULL",
                "CONSTRAINT `mcs_country_values_fields_fkk` FOREIGN KEY (`field_value_id`) REFERENCES `#__mycityselector_field_value` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
                "CONSTRAINT `mcs_values_countries_fk` FOREIGN KEY (`country_id`) REFERENCES `#__mycityselector_countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE"
            ]
        ];
    }


    function apply()
    {
        $this->createTables();
        $this->addCountriesDefaultCities();
        $this->fillTables();
        return true;
    }

    function createTables()
    {
        $tables = self::tables();
        foreach ($tables as $tableName => $columns) {
            $this->createTable('#__' . $tableName, $columns,
                'ENGINE = InnoDB DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1');
        }
    }

    function addCountriesDefaultCities()
    {
        $this->addColumn('#__mycityselector_countries', 'default_city_id', 'INT(11) NULL');

        $this->execute("ALTER TABLE #__mycityselector_countries ADD FOREIGN KEY (default_city_id) REFERENCES #__mycityselector_cities(id);");
    }

    function fillTables()
    {
        $this->fillTable('langs', '#__mycityselector_langs');
        $this->fillTable('countries', '#__mycityselector_countries');
        $this->fillTable('country_names', '#__mycityselector_country_names');
        $this->fillTable('provinces', '#__mycityselector_provinces');
        $this->fillTable('province_names', '#__mycityselector_province_names');
        $this->fillTable('cities', '#__mycityselector_cities');
        $this->fillTable('city_names', '#__mycityselector_city_names');

//        exit;
    }

    function fillTable($fileName, $tableName)
    {
        $tableJson = file_get_contents(__DIR__."/data/{$fileName}.json");
        $tableArray = json_decode($tableJson, true);

        $keys = array_keys($tableArray[0]);
        foreach ($keys as $_key => $_value) {
            $keys[$_key] = "`{$_value}`";
        }
        $columns = implode(', ', $keys);
        $sqlQueryTemplate = "INSERT INTO `{$tableName}` ({$columns}) VALUES (__VALUES__)";
        foreach ($tableArray as $row) {
            foreach ($row as $key => $value) {
                if ($key === 'lat' || $key === 'lng') {
                    $row[$key] = empty($value) ? 'NULL' : "{$value}";
                } else {
                    $row[$key] = "'{$value}'";
                }
            }
            $values = implode(', ', $row);
            $sqlQuery = str_replace('__VALUES__', $values, $sqlQueryTemplate);
            if (!$this->execute($sqlQuery)) {
//                echo "query: {$sqlQuery}<br>";
//                echo "error: ";
//                print_r($this->getErrors());
//                echo "<br>";
            }
        }
    }

    function revert()
    {
        $this->execute("SET FOREIGN_KEY_CHECKS=0;");

        $tables = self::tables();
        foreach ($tables as $tableName => $columns) {
            $this->dropTable('#__' . $tableName);
        }

        $this->execute("SET FOREIGN_KEY_CHECKS=1;");

        return true;
    }

}