<?php
/**
 * Создание файлов миграций для mcs
 * @var $db JDatabaseDriverMysqli
 * @var $argv array
 */

$migrationsPath = realpath(__DIR__ . '/../../administrator/components/com_mycityselector/migrations/');

if (!empty($argv[0])) {

    // CREATE NEW
    if ($argv[0] === 'create') {
        if (count($argv) < 3
            || !preg_match('/^\d{1,2}\.\d{1,3}\.\d{1,3}$/', $argv[1])
            || !preg_match('/^[a-z0-9_ ]+$/i', $argv[2])) {
            out("Wrong command format.\nUse: ./jexter/run migra create 2.0.42 renameCasesTable", 'red');
            echo("\n");
            return;
        }

        $version = str_replace('.', '_', $argv[1]);
        $name = $argv[2];

        // try to get username
        $username = getenv('username');
        if (empty($username)) {
            $res = shell_exec('git config user.name');
            if (stripos($res, 'not found') === false) {
                $username = trim($res);
            } else {
                $username = 'Unknown';
            }
        }

        $time = time();
        $name = $version . '_' . $time .'_' . str_replace([' '], ['_'], $name);
        // create a migration file
        $file = $migrationsPath . '/' . $name . '.php';
        if (is_file($file)) {
            out("  Migration '{$name}' already exists!\n", 'red');
            $this->errorCode = 3;
            return;
        }

        $code = <<<TMPL
<?php
/**
 * Migration {$name}
 * @author {$username}
 */

class migration_{$name} extends MigrationObject
{

    function apply()
    {
//      todo your code here
//      examples:
//      \$this->createTable('test', [
//          "`id` BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
//          "`str` VARCHAR(120) NOT NULL",
//          "`num` FLOAT NOT NULL",
//          "`num2` INT NOT NULL DEFAULT '0'",
//      ]);
//      \$this->addColumn('test', 'column_name',  "TEXT NOT NULL AFTER `str`");
//      \$this->renameColumn('test', 'column', 'created_at');
//      \$this->retypeColumn('test', 'created_at', 'int(11)', false, '1481383022');
//      \$this->execute("INSERT INTO `test` (`str`, `num`, `created_at`) VALUES ('pum purum', '12.5', time())");
        return true; //if the method doesn't return true, then migration is failed
    }

    function revert()
    {
//      todo your code here
//      \$this->dropTable('table_name');
//      \$this->dropColumn('table_name', 'column_name');
        return true;
    }

}
TMPL;
        file_put_contents($file, $code);
        out("   Migration '{$name}' created ", 'cyan');
        out("({$file})\n");
    }


    // APPLY ONE
    if ($argv[0] === 'up') {
        require_once $migrationsPath . '/lib/migra.php';
        $migra = new MigraPhp($migrationsPath);
        $res = $migra->applyMigrations(1);
        $errors = $migra->getErrors();
        out(implode("\n", $migra->getLogs()), 'cyan');
        if ($res === false || !empty($errors)) {
            out("ERRORS:\n", 'red');
            out(implode("\n", $migra->getErrors()), 'red');
        }
        echo("\n");
        return;
    }


    // APPLY ONE
    if ($argv[0] === 'down') {
        require_once $migrationsPath . '/lib/migra.php';
        $migra = new MigraPhp($migrationsPath);
        $res = $migra->revertMigrations(1);
        $errors = $migra->getErrors();
        out(implode("\n", $migra->getLogs()), 'cyan');
        if ($res === false || !empty($errors)) {
            out("ERRORS:\n", 'red');
            out(implode("\n", $migra->getErrors()), 'red');
        }
        echo("\n");
        return;
    }

}

out("Wrong command format.
Use: `./jexter/run migra create 2.0.42 renameCasesTable`
    or
    `./jexter/run migra up`
    or
    `./jexter/run migra down`", 'red');
echo("\n");
return;






