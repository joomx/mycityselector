<?php
/**
 * helpers
 */

namespace joomx\sqlquery\helpers;


function splitQueries($sqlString)
{
    $lines = explode("\n", $sqlString);
    foreach ($lines as &$line) {
        $line = trim($line);
    }
    $sqlString = implode("\n", $lines);
    $queries = explode(";\n", $sqlString);
    $queries = array_filter($queries, function($v) {
        return ('' !== trim($v));
    });
    return $queries;
}


function executeQueries($db, $queries, &$results)
{
    foreach ($queries as $_query_string) {
        $_query_string = trim($_query_string);
        $query = $db->setQuery($_query_string);
        if (strtolower(substr($_query_string, 0, 6)) == 'select' || strtolower(substr($_query_string, 0, 4)) == 'show') {
            try {
                $list = $query->loadAssocList();
                $results[] = $list;
            } catch (Exception $e) {
                $results[] = '<pre>Fail: ' . $e->getMessage() . '</pre>';
            }
        } else {
            try {
                $results[] = '<pre>' . ($query->execute() ? 'Success' : 'Fail: ') . '</pre>';
            } catch (Exception $e) {
                $results[] = '<pre>Fail: ' . $e->getMessage() . '</pre>';
            }
        }
    }
}

// TODO

function getQueriesHistory($table)
{



}

function setQueriesHistory($table, $query)
{



}