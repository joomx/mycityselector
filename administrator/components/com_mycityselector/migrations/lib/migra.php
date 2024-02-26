<?php

use Joomla\CMS\Factory;

/**
 * Модифицированный пакет миграций
 *
 * @class MigraPhp
 */
class MigraPhp
{

    const STATUS_INACTIVE = 0;
    const STATUS_APPLIED = 1;
    const STATUS_FAILED = 2;

    private $errors = [];

    private $logs = [];

    /**
     * @var string
     */
    private $table = '#__mycityselector_migra';

    /**
     * @var array
     */
    private $migrations = [];

    private $excludeMigrations = ['install_only'];

    /** @var \JDatabaseDriver */
    private $db = null;

    /** @var MigraObject */
    private $migraObject = null;

    private $migrationsPath = '';


    public function __construct($path = '')
    {
        $this->migrationsPath = $path;
        $this->db = Factory::getDBO();
        // проверяем наличие таблицы миграций и создаем если нужно
        $this->migraObject = new MigraObject($this->db);
        $tableName = $this->db->replacePrefix($this->table);
        if (!$this->migraObject->isTablesExists($tableName)) {
            $res = $this->migraObject->createTable($this->table, [
                "`id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
                "`created_at` INT UNSIGNED NOT NULL DEFAULT '0'",
                "`name` VARCHAR(255) NOT NULL",
                "`status` TINYINT(1) NOT NULL",
                "`author` VARCHAR(50) NOT NULL",
            ]);
            if (!$res) {
                exit("Cannot create migrations table!\n");
            }
        }
        $this->loadMigrations();
    }


    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }


    /**
     * @return array
     */
    public function getLogs()
    {
        return $this->logs;
    }


    public function markAsAppliedMigration($name)
    {
        $this->logs[] = "mark as applied '{$name}' migration";
        $file = $this->migrationsPath . '/' . $name . '.php';
        if (!is_file($file)) {
            $this->logs[] = $this->errors[] = "Migration not found: '{$name}.php'";
            return false;
        }
        include_once($file);
        $className = 'migration_' . $name;
        if (class_exists($className)) {
            if (!empty($this->migrations[$name])) {
                $this->migrations[$name]['status'] = self::STATUS_APPLIED;
                $this->saveHistory();
            }
            $this->logs[] = "'{$name}' migration finished";
            return true;
        } else {
            $this->logs[] = $this->errors[] = "Migration error. Class '{$className}' not found.";
        }
        return false;
    }


    public function runMigration($name, $mode = 'apply')
    {
        if (!in_array($mode, ['apply', 'revert'])) {
            $this->logs[] = $this->errors[] = "Wrong mode name: {$mode}";
            return false;
        }
        $this->logs[] = "{$mode}ing '{$name}' migration";
        $file = $this->migrationsPath . '/' . $name . '.php';
        if (!is_file($file)) {
            $this->logs[] = $this->errors[] = "Migration not found: '{$name}.php'";
            return false;
        }
        include_once($file);
        $className = 'migration_' . $name;
        if (class_exists($className)) {
            $object = new $className($this->db);
            /* @var $object MigraObject */
            if ($object->{$mode}() === true) {
                if (!empty($this->migrations[$name])) {
                    $this->migrations[$name]['status'] = self::STATUS_APPLIED;
                    $this->saveHistory();
                }
                $this->logs[] = "{$mode}ing '{$name}' migration finished";
                return true;
            } else {
                $this->errors = array_merge($this->errors, $object->getErrors());
                $this->logs = array_merge($this->logs, $object->getErrors());
            }
        } else {
            $this->logs[] = $this->errors[] = "Migration error. Class '{$className}' not found.";
        }
        return false;
    }


    public function createBackup()
    {
        // todo может делать копии таблиц перед накатыванием обновлений?
    }


    /**
     * Load exists migrations
     */
    private function loadMigrations()
    {
        // load migrations list
        $migrations = $this->migraObject->query("SELECT * FROM {$this->table}");
        foreach ($migrations as $migration) {
            $this->migrations[$migration['name']] = [
                'id' => $migration['id'],
                'time' => $migration['created_at'],
                'name' => $migration['name'],
                'status' => $migration['status'],
                'author' => $migration['author'],
            ];
        }
        // search new files
        $files = glob($this->migrationsPath . '/*.php');
        if (!empty($files)) {
            $newMigrations = false;
            foreach ($files as $file) {
                $name = str_replace('.php', '', basename($file));
                if (in_array($name, $this->excludeMigrations))  continue; // ignore install/uninstall migrations
                $time = explode('_', $name)[3]; // 3_0_0_1525116043_init.php
                if (!isset($this->migrations[$name])) {
                    $author = 'Unknown';
                    $content = file_get_contents($file);
                    if (preg_match("/\\@author\\s+([^\n]+)/i", $content, $match)) {
                        $match = trim($match[1]);
                        if (!empty($match)) {
                            $author = $match;
                        }
                    }
                    $this->migrations[$name] = [
                        'id' => 0,
                        'time' => $time,
                        'name' => $name,
                        'status' => self::STATUS_INACTIVE,
                        'author' => $author
                    ];
                    $newMigrations = true;
                }
            }
            if ($newMigrations) $this->saveHistory();
        }
        ksort($this->migrations);
    }


    /**
     * @return bool|int
     */
    public function applyMigrations($number = null)
    {
        $number = ($number === null) ? 100000 : $number;
        $migrations = [];
        foreach ($this->migrations as $name => $migration) {
            if ($migration['status'] == self::STATUS_INACTIVE || $migration['status'] == self::STATUS_FAILED) {
                $number--;
                $migrations[$name] = $migration;
            }
            if ($number == 0) break;
        }
        if (empty($migrations)) {
            $this->logs[] = "Inactive migrations not found.";
            return 0;
        }
        foreach ($migrations as $name => $migration) {
            $this->logs[] = "applying '{$migration['name']}' migration";
            $file = $this->migrationsPath . '/' . $migration['name'] . '.php';
            if (!is_file($file)) {
                $this->logs[] = $this->errors[] = "Migration not found: '{$migration['name']}.php'";
                return false;
            }
            include_once($file);
            $className = 'migration_' . $migration['name'];
            if (class_exists($className)) {
                $object = new $className($this->db);
                /* @var $object MigraObject */
                if ($object->apply() === true) {
                    $this->migrations[$name]['status'] = self::STATUS_APPLIED;
                    $this->saveHistory();
                } else {
                    $this->migrations[$name]['status'] = self::STATUS_FAILED;
                    $this->saveHistory();
                    $this->logs[] = $this->errors[] = "Migration '{$migration['name']}' failed.";
                    return false;
                }
                $this->errors = array_merge($this->errors, $object->getErrors());
                $this->logs = array_merge($this->logs, $object->getErrors());
            } else {
                $this->logs[] = $this->errors[] = "Migration error. Class '{$className}' not found.";
                return false;
            }
        }
        return true;
    }


    /**
     * @param int $number
     * @return bool|int
     */
    public function revertMigrations($number = null)
    {
        $number = ($number === null) ? 1 : $number;
        $migrations = [];
        $revers = array_reverse($this->migrations);
        foreach ($revers as $migration) {
            if ($migration['status'] == self::STATUS_APPLIED) {
                $number--;
                $migrations[$migration['name']] = $migration;
            }
            if ($number == 0) break;
        }
        if (empty($migrations)) {
            $this->logs[] = "Active migrations not found.";
            return 0;
        }
        foreach ($migrations as $name => $migration) {
            $this->logs[] = "reverting '{$migration['name']}' migration";
            $file = $this->migrationsPath . '/' . $migration['name'] . '.php';
            if (!is_file($file)) {
                $this->logs[] = $this->errors[] = "Migration not found: '{$migration['name']}.php'";
                return false;
            }
            require($file);
            $className = 'migration_' . $migration['name'];
            if (class_exists($className)) {
                $object = new $className($this->db);
                /* @var $object MigraObject */
                if ($object->revert() === true) {
                    $this->migrations[$name]['status'] = self::STATUS_INACTIVE;
                    $this->saveHistory();
                } else {
                    $this->migrations[$name]['status'] = self::STATUS_FAILED;
                    $this->saveHistory();
                    $this->logs[] = $this->errors[] = "Migration '{$migration['name']}' failed.";
                    return false;
                }
                $this->errors = array_merge($this->errors, $object->getErrors());
                $this->logs = array_merge($this->logs, $object->getErrors());
            } else {
                $this->logs[] = $this->errors[] = "Migration error. Class '{$className}' not found.";
                return false;
            }
        }
        return true;
    }


    /**
     * пересохранение истории миграций
     */
    private function saveHistory()
    {
        $mo = $this->migraObject;
        ksort($this->migrations);
        foreach ($this->migrations as $k => $migration) {
            if ($migration['id'] === 0) {
                // new
                $mo->execute("INSERT INTO `{$this->table}` "
                    ."(`created_at`,`name`,`author`,`status`) VALUES (:time, :name, :author, :status)", [
                    ':time' => $migration['time'],
                    ':name' => $migration['name'],
                    ':author' => $migration['author'],
                    ':status' => $migration['status'],
                ]);
                $newId = $mo->query("SELECT LAST_INSERT_ID()", [], $mo::VALUE);
                $this->migrations[$k]['id'] = $newId;
            } else {
                // update
                $mo->execute("UPDATE `{$this->table}` SET `status` = :status WHERE `id` = :id", [
                    ':id' => $migration['id'],
                    ':status' => $migration['status']
                ]);
            }
        }
    }

}


/**
 * Class MigrationObject
 */
class MigraObject {

    const ROWS = 'rows';
    const ROW = 'row';
    const COL = 'col';
    const VALUE = 'value';


    /** @var array */
    private $errors = [];

    /** @var \JDatabaseDriver */
    private $db = null;


    public function __construct($db)
    {
        $this->db = $db;
    }


    public function getErrors()
    {
        return $this->errors;
    }


    /**
     * Apply migration
     * @return bool
     */
    public function apply()
    {
        return false;
    }

    /**
     * Revert migration
     * @return bool
     */
    public function revert()
    {
        return false;
    }


    /**
     * @return bool
     */
    public function isTablesExists($name)
    {
        $name = $this->db->replacePrefix($name);
        $tables = $this->query("SHOW TABLES");
        foreach($tables as $table) {
            $table = array_shift($table);
            if ($table === $name) return true;
        }
        return false;
    }


    /**
     * Simple filtering of table name
     * @param string $table
     * @return string
     */
    public function filterName($table)
    {
        return trim($this->db->quoteName($table), "'`\"");
    }


    /**
     * Executes an sql query
     * @param string $sql
     * @param array $params [':param' => 'value', ':param2' => 'value2', ...]
     * @return bool
     */
    public function execute($sql, $params = [])
    {
        if (!empty($params)) {
            foreach ($params as $param => $value) {
                $value = $this->db->quote($value);
                $sql = str_replace($param, $value, $sql);
            }
        }
        $this->db->setQuery($sql);
        try {
            $result = $this->db->execute();
            if ($result === false) {
                throw new Exception('Unknown error happened on db request', 10000);
            }
            return true;
        } catch (Exception $exception) {
            $this->errors[] = "'{$sql}'\nCode: {$exception->getCode()}\nInfo: '{$exception->getMessage()}'\n";
        }
        return false;
    }


    /**
     * Executes an sql query and return result rows
     * @param string $sql
     * @param array $params [':param' => 'value', ':param2' => 'value2', ...]
     * @param string $return Constant of class ( ::ROWS | ::ROW | ::COL | ::VALUE ) ROWS by default
     * @return array
     */
    public function query($sql, $params = [], $return = self::ROWS)
    {
        if (!empty($params)) {
            foreach ($params as $param => $value) {
                $value = $this->db->quote($value);
                $sql = str_replace($param, $value, $sql);
            }
        }
        $this->db->setQuery($sql);
        try {
            $result = $this->db->execute();
            if ($result === false) {
                throw new Exception('Unknown error happened on db request', 10000);
            }
            if ($return == 'row') {
                $data = $this->db->loadAssoc();
            } else if ($return == 'col') {
                $data = $this->db->loadColumn();
            } else if ($return == 'value') {
                $data = $this->db->loadResult();
            } else {
                $data = $this->db->loadAssocList();
            }
            return $data;
        } catch (Exception $exception) {
            $this->errors[] = "'{$sql}'\nCode: {$exception->getCode()}\nInfo: '{$exception->getMessage()}'\n";
        }
        return [];
    }


    /**
     * Creates new table (if not exists)
     * @param string $name Table name
     * @param array $columns Example: ['`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT', '`str` varchar(120) NOT NULL']
     * @param string $options Default: 'ENGINE=InnoDB DEFAULT CHARSET=utf8'
     * @return bool
     */
    public function createTable($name, $columns, $options = 'ENGINE=InnoDB DEFAULT CHARSET=utf8')
    {
        $name = $this->filterName($name);
        $sql = "CREATE TABLE IF NOT EXISTS {$name} (" . implode(', ', $columns) . ") {$options}";
        return $this->execute($sql);
    }


    /**
     * Renames table
     * @param string $name
     * @param string $newName
     * @return bool
     */
    public function renameTable($name, $newName)
    {
        $name = $this->filterName($name);
        $newName = $this->filterName($newName);
        if (!$this->isTablesExists($name)) {
            $this->errors[] = "Cannot rename table '{$name}' because it doesn't exists";
        }
        $sql = "RENAME TABLE {$name} TO {$newName};";
        return $this->execute($sql);
    }


    /**
     * Drops table if exists
     * @param string $name
     * @return bool
     */
    public function dropTable($name)
    {
        $name = $this->filterName($name);
        if (!$this->isTablesExists($name)) {
            $this->errors[] = "Cannot delete table '{$name}' because it doesn't exists";
        }
        return $this->execute("DROP TABLE IF EXISTS `{$name}`");
    }


    /**
     * @param string $table
     * @param string $column
     * @param string $type
     * @return bool
     */
    public function addColumn($table, $column, $type)
    {
        $table = $this->filterName($table);
        $column = $this->filterName($column);
        if (!$this->isTablesExists($table)) {
            $this->errors[] = "Cannot add new column '{$column}' because table '{$table}' doesn't exists";
        }
        // check column
        $row = $this->query("SHOW COLUMNS FROM `{$table}`", [], self::ROWS, true);
        foreach ($row as $col) {
            if ($col['Field'] == $column) {
                $this->errors[] = "Column '{$column}' already exists";
                return false;
            }
        }
        return $this->execute("ALTER TABLE `{$table}` ADD `{$column}` {$type}");
    }


    /**
     * @param string $table
     * @param string $column
     * @return bool
     */
    public function dropColumn($table, $column)
    {
        $table = $this->filterName($table);
        $column = $this->filterName($column);
        if (!$this->isTablesExists($table)) {
            $this->errors[] = "Cannot delete column '{$column}' because table '{$table}' doesn't exists";
        }
        // check column
        $row = $this->query("SHOW COLUMNS FROM `{$table}`", [], self::ROWS, true);
        $isExist = false;
        foreach ($row as $col) {
            if ($col['Field'] == $column) {
                $isExist = true;
            }
        }
        if ($isExist) {
            if ($this->execute("ALTER TABLE `{$table}` DROP `{$column}`")) {
                return true;
            }
        } else {
            $this->errors[] = "Not found '{$column}' column in '{$table}' table.";
        }
        return false;
    }


    /**
     * @param string $table
     * @param string $column
     * @param string $newName
     * @return bool
     */
    public function renameColumn($table, $column, $newName)
    {
        $table = $this->filterName($table);
        $column = $this->filterName($column);
        if (!$this->isTablesExists($table)) {
            $this->errors[] = "Cannot rename column '{$column}' because table '{$table}' doesn't exists";
        }
        // check column
        $row = $this->query("SHOW COLUMNS FROM {$table}", [], self::ROWS, true);
        $isExist = false;
        $type = '';
        $isNull = '';
        $default = '';
        foreach ($row as $col) {
            if ($col['Field'] == $column) {
                $type = $col['Type'];
                $isNull = ($col['Null'] == 'NO') ? ' NOT NULL' : '';
                $default = empty($col['Default']) ? '' : " DEFAULT '{$col['Default']}'";
                $isExist = true;
            }
        }
        if ($isExist) {
            // ALTER TABLE `demo` CHANGE `col3` `col4` VARCHAR(30) NOT NULL DEFAULT 'abc';
            if ($this->execute("ALTER TABLE `{$table}` CHANGE `{$column}` `{$newName}` {$type} {$isNull} {$default}")) {
                return true;
            }
        } else {
            $this->errors[] = "Not found '{$column}' column in '{$table}' table.";
        }
        return false;
    }


    /**
     * Changes a type of column
     * @param string $table
     * @param string $column
     * @param string $newType
     * @param bool $null
     * @param string $default
     * @return bool
     */
    public function retypeColumn($table, $column, $newType, $null = false, $default = '')
    {
        $table = $this->filterName($table);
        $column = $this->filterName($column);
        if (!$this->isTablesExists($table)) {
            $this->errors[] = "Cannot ''retype'' column '{$column}' because table '{$table}' doesn't exists";
        }
        // check column
        $row = $this->query("SHOW COLUMNS FROM {$table}", [], self::ROWS, true);
        $isExist = false;
        $null = $null ? 'NULL' : 'NOT NULL';
        $default = empty($default) ? '' : 'DEFAULT ' . $this->db->quote($default);
        foreach ($row as $col) {
            if ($col['Field'] == $column) {
                $isExist = true;
            }
        }
        if ($isExist) {
            // ALTER TABLE `demo` CHANGE `col3` `col4` VARCHAR(30) NOT NULL DEFAULT 'abc';
            if ($this->execute("ALTER TABLE `{$table}` CHANGE `{$column}` `{$column}` {$newType} {$null} {$default}")) {
                return true;
            }
        } else {
            $this->errors[] = "Not found '{$column}' column in '{$table}' table.";
        }
        return false;
    }


    /**
     * Inserts rows
     * @param string $table
     * @param array $columns ['column1' => 'value1', 'column2' => 'value2']
     * @return bool
     */
    public function insert($table, $columns = [])
    {
        $table = $this->filterName($table);
        if (!$this->isTablesExists($table)) {
            $this->errors[] = "Cannot insert row because table '{$table}' doesn't exists";
        }
        if (!empty($columns)) {
            $params = $markers = [];
            foreach ($columns as $col => $value) {
                $params[ ':' . $col ] = $value;
                $markers[] = ':' . $col;
            }
            $markers = implode(" , ", $markers);
            $columns = array_keys($columns);
            $columns = "`" . implode("`,`", $columns) . "`";
            $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$markers})";
            return $this->execute($sql, $params);
        }
        return false;
    }


    /**
     * Updates row
     * @param string $table
     * @param array $columns ['column3' => 'new_value']
     * @param string $condition "column = value OR column2 > 10"
     * @return bool
     */
    public function update($table, $columns = [], $condition = "")
    {
        $table = $this->filterName($table);
        if (!$this->isTablesExists($table)) {
            $this->errors[] = "Cannot update row because table '{$table}' doesn't exists.";
        }
        if (!empty($columns)) {
            $pairs = [];
            $params = [];
            foreach ($columns as $col => $value) {
                $params[ ':' . $col ] = $value;
                $pairs[] = "`{$col}` = " .  ':' . $col;
            }
            $pairs = implode(" ,", $pairs);
            $sql = "UPDATE {$table} SET {$pairs} " . (empty($condition) ? '' : "WHERE {$condition}");
            return $this->execute($sql, $params);
        }
        return false;
    }


    /**
     * Deletes rows
     * @param string $table
     * @param string $condition "column = value OR column2 > 10"
     * @return bool
     */
    public function delete($table, $condition)
    {
        $table = $this->filterName($table);
        if (!$this->isTablesExists($table)) {
            $this->errors[] = "Cannot delete row(s) because table '{$table}' doesn't exists";
        }
        $sql = "DELETE FROM {$table} WHERE {$condition}";
        return $this->execute($sql);
    }

    /**
     * Return joomla db(Factory::getDBO()) object
     * @return JDatabaseDriver
     */
    public function getDb()
    {
        return $this->db;
    }

    public function getLastInsertId()
    {
        return $this->db->insertid();
    }

}
