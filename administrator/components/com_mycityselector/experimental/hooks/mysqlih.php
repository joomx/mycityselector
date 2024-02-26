<?php
defined('JPATH_PLATFORM') or die;

require_once(JPATH_ROOT . '/plugins/system/plgmycityselector/helpers/McsEventDispatcher.php');

use joomx\mcs\plugin\helpers\McsEventDispatcher;

/**
 * MySQLi Hooks database driver
 *
 * @link   https://www.php.net/manual/en/book.mysqli.php
 * @since  3.0.0
 */
class JDatabaseDriverMysqlih extends JDatabaseDriverMysqli
{
    /**
     * @inheritDoc
     */
	public $name = 'mysqli';


    /**
     * @inheritDoc
     */
    public function __construct($options = null)
    {
        $options['driver'] = 'mysqli';
        parent::__construct($options);
//        Example:
//        McsEventDispatcher::getInstance()->listenEvent('onDbRetrieve', function ($stack) {
//            var_dump($stack);
//            return $data
//        });
    }

    /**
     * @param string $sql
     */
    protected function parseTableNames($sql)
    {
        preg_match('/from\s+([^ ]+)\s+/i', $sql, $matches);
        if (!empty($matches) && isset($matches[1])) {
            $name = str_replace(['`', '"', "'"], '', $matches[1]);
            $name = explode("\n", $name); // иногда попадается перенос строки
            return [ $name[0] ];
        }
        return [];
    }


    /**
     * @param string $sql
     */
    protected function parseFieldNames($sql)
    {
        preg_match('/select\s+(.+)\s+from/i', $sql, $matches);
        if (!empty($matches) && isset($matches[1])) {
            $fields = str_replace(['`', '"', "'", ' '], '', $matches[1]);
            return explode(',', $fields);
        }
        return [];
    }


    /**
     * @inheritDoc
     */
	protected function fetchArray($cursor = null)
	{
        $data = parent::fetchArray($cursor);

        $sql = (string) $this->sql;
        $result = McsEventDispatcher::getInstance()->triggerEvent('onDbRetrieve', [
            'method' => 'fetchArray',
            'query' => $sql,
            'data' => $data,
            'tables' => $this->parseTableNames($sql),
            'fields' => $this->parseFieldNames($sql)
        ]);
        return $result['data'];
	}


    /**
     * @inheritDoc
     */
	protected function fetchAssoc($cursor = null)
	{
        $data = parent::fetchAssoc($cursor);
        $sql = (string) $this->sql;
        $result = McsEventDispatcher::getInstance()->triggerEvent('onDbRetrieve', [
            'method' => 'fetchAssoc',
            'query' => $sql,
            'data' => $data,
            'tables' => $this->parseTableNames($sql),
            'fields' => $this->parseFieldNames($sql)
        ]);
        return $result['data'];
	}


	/**
	 * @inheritDoc
	 */
	protected function fetchObject($cursor = null, $class = 'stdClass')
	{
        $data = parent::fetchObject($cursor, $class);
        $sql = (string) $this->sql;
        $result = McsEventDispatcher::getInstance()->triggerEvent('onDbRetrieve', [
            'method' => 'fetchObject',
            'query' => $sql,
            'data' => $data,
            'tables' => $this->parseTableNames($sql),
            'fields' => $this->parseFieldNames($sql)
        ]);
        return $result['data'];
	}

}
