<?php

/**
 * Миграция для экспериментальной версии "подмена данных на лету из таблиц при чтении"
 * Class migration_3_1_0_1631441346_db_repl
 * @author Konstantin
 */

class migration_3_1_0_1631441346_db_repl extends MigraObject
{
    function apply()
    {
        $this->addColumn('#__mycityselector_field', 'is_db_replacement', 'TINYINT NULL DEFAULT NULL');
        $this->addColumn('#__mycityselector_field', 'db_replacement_path', 'TINYINT NULL DEFAULT NULL');
        return true;
    }


    function revert()
    {
        $this->dropColumn('#__mycityselector_field', 'is_db_replacement');
        $this->dropColumn('#__mycityselector_field', 'db_replacement_path');
        return true;
    }

}
