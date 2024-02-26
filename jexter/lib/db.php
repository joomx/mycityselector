<?php
/*
 * Database helper
 */

namespace adamasantares\jexter;

if (!defined('JEXTER_DIR')) {
    define('JEXTER_DIR', realpath(__DIR__ . '/../'));
}

if (!class_exists('PDO')) {
    out("The PDO extension not installed!\n", 'red');
    exit;
}


class JexterDbHelper
{

    /**
     * @var \PDO
     */
    private $dbh = null;

    private $connectionError = '';

    private $errorCode = 0;

    private $errorInfo = null;

    /**
     * Creates new db connection
     * @param $dbname
     * @param $user
     * @param $password
     * @param string $host
     */
    function __construct($dbname, $user, $password, $host = 'localhost')
    {
        try {
            $this->dbh = new \PDO("mysql:host={$host};dbname={$dbname}", $user, $password);
        } catch (\PDOException $e) {
            $this->dbh = null;
            $this->connectionError = $e->getMessage();
        }
    }

    public function isConnected()
    {
        return !empty($this->dbh);
    }

    public function getConnectionError()
    {
        return $this->connectionError;
    }

    public function getLastError()
    {
        return $this->errorCode . ': ' . print_r($this->errorInfo, true);
    }

    public function dropAllTables()
    {
        $res = $this->dbh->query("SHOW TABLES");
        $tables = $res->fetchAll();
        $this->dbh->exec("SET FOREIGN_KEY_CHECKS=0");
        foreach ($tables as $table) {
            $this->dbh->query("DROP TABLE {$table[0]}");
        }
    }

    public function execute($sql, $params = [])
    {
        $sth = $this->dbh->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
        if ($sth->execute(empty($params) ? null : $params)) {
            return true;
        }
        $this->errorCode = $sth->errorCode();
        $this->errorInfo = $sth->errorInfo();
        return false;
    }

    public function query($sql, $params = [])
    {
        $sth = $this->dbh->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
        if ($sth->execute(empty($params) ? null : $params)) {
            return $sth->fetchAll();
        }
        $this->errorCode = $sth->errorCode();
        $this->errorInfo = $sth->errorInfo();
        return [];
    }

}



