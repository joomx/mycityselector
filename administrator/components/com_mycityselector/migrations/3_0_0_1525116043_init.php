<?php
/**
 * Migration: Миграция со второй на третью версию
 * @author Konstantin
 */

// Существующие таблицы и их записи не удаляем, иначе потеряются существующие связи с уже созданным контентом

class migration_3_0_0_1525116043_init extends MigraObject
{
    private $langId;

    function apply()
    {
        $this->createLangsTable();

        $this->renameOldTables();

        $this->createAndFillCountryNamesTable();

        $this->createAndFillProvinceNamesTable();

        $this->createAndFillCityNamesTable();

        $this->updateOldTables();

        $this->updateFiledValueTable();

        $this->createValueProvinceTable();

        $this->createValueCountryTable();

        $this->updateValueCityTable();

        $this->addCountriesCodes();

        $this->addCountriesDomains();

        $this->addCountriesDefaultCities();

        return true;
    }


    function revert()
    {
        return true;
    }

    private function renameOldTables()
    {
        //переименовываем старые таблицы
        $this->renameTable('#__mycityselector_country', '#__mycityselector_countries');
        $this->renameTable('#__mycityselector_province', '#__mycityselector_provinces');
        $this->renameTable('#__mycityselector_city', '#__mycityselector_cities');
    }

    private function updateOldTables()
    {
        //стоблцы для геопозиции
        $this->addColumn('#__mycityselector_cities', 'lat', 'DECIMAL(10, 8)');
        $this->addColumn('#__mycityselector_cities', 'lng', 'DECIMAL(11, 8)');

        $this->execute("ALTER TABLE #__mycityselector_cities MODIFY province_id INT(11) NOT NULL;");
        $this->execute("ALTER TABLE #__mycityselector_cities MODIFY country_id INT(11) NOT NULL;");

        $this->execute("ALTER TABLE #__mycityselector_cities ADD FOREIGN KEY (province_id) REFERENCES #__mycityselector_provinces(id);");
        $this->execute("ALTER TABLE #__mycityselector_cities ADD FOREIGN KEY (country_id) REFERENCES #__mycityselector_countries(id);");

        //удаляем столбцы с названиями(теперь хранятся в новых таблицах)
        $this->dropColumn('#__mycityselector_countries', "'name'");
        $this->dropColumn('#__mycityselector_provinces', "'name'");
        $this->dropColumn('#__mycityselector_cities', "'name'");

        $this->addColumn('#__mycityselector_provinces', 'lat', 'DECIMAL(10, 8)');
        $this->addColumn('#__mycityselector_provinces', 'lng', 'DECIMAL(11, 8)');

        $this->dropColumn('#__mycityselector_countries', "'latitude'");


        //добавляем для перевода падежей
        $this->addColumn('#__mycityselector_city_cases', 'lang_id', 'INT(11) UNSIGNED NOT NULL');
        $this->execute("UPDATE #__mycityselector_city_cases SET `lang_id` = 1");
        $this->execute("ALTER TABLE #__mycityselector_city_cases ADD FOREIGN KEY (lang_id) REFERENCES #__mycityselector_langs(id)");
    }

    //таблицы для языков, используется для связи с новыми таблицами переводов населенных пунктов
    private function createLangsTable()
    {
        $this->createTable('#__mycityselector_langs', [
            "`id` INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
            "`name` VARCHAR(120) NOT NULL ",
            "`locale` VARCHAR(10) NOT NULL",
            "`default` TINYINT NOT NULL DEFAULT '0'",
        ]);

        $this->execute("ALTER TABLE #__mycityselector_langs ADD UNIQUE (locale)");
        $this->execute("INSERT INTO `#__mycityselector_langs` (`name`, `locale`, `default`) VALUES ('Русский', 'ru-RU', 1)");

        $this->langId = $this->getLastInsertId();
    }

    //создаем таблицу для хранения переводов названий стран
    private function createAndFillCountryNamesTable()
    {
        $this->createTable('#__mycityselector_country_names', [
            "`id` INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
            "`name` VARCHAR(50) NOT NULL",
            "`country_id` INT(11) NOT NULL",
            "`lang_id` INT(11) UNSIGNED NOT NULL",
        ]);

        $this->execute("ALTER TABLE #__mycityselector_country_names ADD FOREIGN KEY (country_id) REFERENCES #__mycityselector_countries(id);");
        $this->execute("ALTER TABLE #__mycityselector_country_names ADD FOREIGN KEY (lang_id) REFERENCES #__mycityselector_langs(id);");

        $countries = $this->query("SELECT `id`, `name` FROM #__mycityselector_countries");

        foreach ($countries as $country) {
            $this->execute("INSERT INTO `#__mycityselector_country_names` (`name`, `country_id`, `lang_id`) VALUES ('{$country['name']}', '{$country['id']}', {$this->langId})");
        }
    }

    //создаем таблицу для хранения переводов названий областей
    private function createAndFillProvinceNamesTable()
    {
        $this->createTable('#__mycityselector_province_names', [
            "`id` INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
            "`name` VARCHAR(50) NOT NULL",
            "`province_id` INT(11) NOT NULL",
            "`lang_id` INT(11) UNSIGNED NOT NULL",
        ]);

        $this->execute("ALTER TABLE #__mycityselector_province_names ADD FOREIGN KEY (province_id) REFERENCES #__mycityselector_provinces(id);");
        $this->execute("ALTER TABLE #__mycityselector_province_names ADD FOREIGN KEY (lang_id) REFERENCES #__mycityselector_langs(id);");

        $provinces = $this->query("SELECT `id`, `name` FROM #__mycityselector_provinces");

        foreach($provinces as $province) {
            $this->execute("INSERT INTO `#__mycityselector_province_names` (`name`, `province_id`, `lang_id`) VALUES ('{$province['name']}', '{$province['id']}', {$this->langId})");
        }
    }

    //создаем таблицу для хранения переводов названий городов
    private function createAndFillCityNamesTable()
    {
        $this->createTable('#__mycityselector_city_names', [
            "`id` INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
            "`name` VARCHAR(50) NOT NULL",
            "`city_id` INT(11) NOT NULL",
            "`lang_id` INT(11) UNSIGNED NOT NULL",
        ]);

        $this->execute("ALTER TABLE #__mycityselector_city_names ADD FOREIGN KEY (city_id) REFERENCES #__mycityselector_cities(id);");
        $this->execute("ALTER TABLE #__mycityselector_city_names ADD FOREIGN KEY (lang_id) REFERENCES #__mycityselector_langs(id);");

        $cities = $this->query("SELECT `id`, `name` FROM #__mycityselector_cities");

        foreach($cities as $city) {
            $this->execute("INSERT INTO `#__mycityselector_city_names` (`name`, `city_id`, `lang_id`) VALUES ('{$city['name']}', '{$city['id']}', {$this->langId})");
        }
    }

    private function updateFiledValueTable()
    {
        $this->execute("ALTER TABLE #__mycityselector_field_value MODIFY id int(11) unsigned NOT NULL AUTO_INCREMENT;");
    }

    private function createValueProvinceTable()
    {
        $this->createTable('#__mycityselector_value_province', [
            "`id` INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
            "`field_value_id` INT(11) UNSIGNED NOT NULL",
            "`province_id` INT(11) NOT NULL",
        ]);

        $this->execute("ALTER TABLE #__mycityselector_value_province ADD FOREIGN KEY (field_value_id) REFERENCES #__mycityselector_field_value(id) ON DELETE CASCADE ON UPDATE CASCADE;");
        $this->execute("ALTER TABLE #__mycityselector_value_province ADD FOREIGN KEY (province_id) REFERENCES #__mycityselector_provinces(id) ON DELETE CASCADE ON UPDATE CASCADE;");
    }

    private function createValueCountryTable()
    {
        $this->createTable('#__mycityselector_value_country', [
            "`id` INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
            "`field_value_id` INT(11) UNSIGNED NOT NULL",
            "`country_id`  INT(11) NOT NULL",
        ]);

        $this->execute("ALTER TABLE #__mycityselector_value_country ADD FOREIGN KEY (field_value_id) REFERENCES #__mycityselector_field_value(id) ON DELETE CASCADE ON UPDATE CASCADE;");
        $this->execute("ALTER TABLE #__mycityselector_value_country ADD FOREIGN KEY (country_id) REFERENCES #__mycityselector_countries(id) ON DELETE CASCADE ON UPDATE CASCADE;");
    }

    private function updateValueCityTable()
    {
        $this->renameColumn('#__mycityselector_value_city', 'fieldvalue_id', 'field_value_id');

        $this->execute("ALTER TABLE #__mycityselector_value_city MODIFY field_value_id int(11) unsigned NOT NULL;");

        $this->execute("ALTER TABLE #__mycityselector_value_city ADD FOREIGN KEY (field_value_id) REFERENCES #__mycityselector_field_value(id) ON DELETE CASCADE ON UPDATE CASCADE;");
        $this->execute("ALTER TABLE #__mycityselector_value_city ADD FOREIGN KEY (city_id) REFERENCES #__mycityselector_cities(id) ON DELETE CASCADE ON UPDATE CASCADE;");
    }

    private function addCountriesCodes()
    {
        $this->addColumn('#__mycityselector_countries', 'code', 'VARCHAR(2) NOT NULL');

        $this->execute("UPDATE `#__mycityselector_countries` SET `code`='RU' WHERE `subdomain` = 'russia';");
        $this->execute("UPDATE `#__mycityselector_countries` SET `code`='BY' WHERE `subdomain` = 'belarus';");
        $this->execute("UPDATE `#__mycityselector_countries` SET `code`='UA' WHERE `subdomain` = 'ukraine';");
        $this->execute("UPDATE `#__mycityselector_countries` SET `code`='KZ' WHERE `subdomain` = 'kazahstan';");
    }

    private function addCountriesDomains()
    {
        $this->addColumn('#__mycityselector_countries', 'domain', 'VARCHAR(255) NULL');
    }

    private function addCountriesDefaultCities()
    {
        $this->addColumn('#__mycityselector_countries', 'default_city_id', 'INT(11) NULL');

        $this->execute("ALTER TABLE #__mycityselector_countries ADD FOREIGN KEY (default_city_id) REFERENCES #__mycityselector_cities(id);");
    }

}
