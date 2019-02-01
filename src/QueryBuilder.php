<?php
namespace Jarzon;

use mysql_xdevapi\Exception;

class QueryBuilder
{
    static $table = '';
    static $tableAlias = null;
    static $pdo;
    static $currentTable = '';

    static function setPDO(object $pdo)
    {
        self::$pdo = $pdo;
    }

    static public function setTable(string $table, $tableAlias = null)
    {
        self::$table = $table;
        self::$tableAlias = $tableAlias;

        static::$currentTable = $tableAlias !== null? $tableAlias: $table;
    }

    static public function select(string $table, $tableAlias = null): Select
    {
        self::setTable($table, $tableAlias);

        $query = new Select($table, $tableAlias, self::$pdo);

        return $query;
    }

    static public function insert(string $table, $tableAlias = null): Insert
    {
        self::setTable($table, $tableAlias);

        $query = new Insert($table, self::$pdo);

        return $query;
    }

    static public function update(string $table, $tableAlias = null): Update
    {
        self::setTable($table, $tableAlias);

        $query = new Update($table, $tableAlias, self::$pdo);

        return $query;
    }

    static public function delete(string $table, $tableAlias = null): Delete
    {
        self::setTable($table, $tableAlias);

        $query = new Delete($table, $tableAlias, self::$pdo);

        return $query;
    }

    static function function(string $function, $column, $alias = null)
    {
        if($alias === false) {
            return "$function(".self::$currentTable.".$column)";
        }
        else if(is_array($column)) {
            $column = array_values($column)[0];
        }

        return ["$function(".self::$currentTable.".$column)" => $alias ?? $column];
    }

    static function functionMultipleArgs(string $function, $column, $alias = null)
    {
        if($alias === false) {
            return "$function(".self::$currentTable.".$column)";
        }

        return ["$function($column)" => $alias ?? $column];
    }

    // Number

    static function min(string $column, $alias = null)
    {
        return self::function('MIN', $column, $alias);
    }

    static function max(string $column, $alias = null)
    {
        return self::function('MAX', $column, $alias);
    }

    static function sum(string $column, $alias = null)
    {
        return self::function('SUM', $column, $alias);
    }

    static function avg(string $column, $alias = null)
    {
        return self::function('AVG', $column, $alias);
    }

    static function ceiling(string $column, $alias = null)
    {
        return self::function('CEILING', $column, $alias);
    }

    static function floor(string $column, $alias = null)
    {
        return self::function('FLOOR', $column, $alias);
    }

    static function count(string $column, $alias = null)
    {
        return self::function('COUNT', $column, $alias);
    }

    static function format(string $column, int $round = 2, string $local = 'fr_CA', $alias = null)
    {
        return self::function('FORMAT', "$column, $round".(($local !== '')? ", '$local'": ''), $alias ?? $column);
    }

    static function currency(string $value, $alias = null)
    {
        return self::concat([self::format($value, 2, 'fr_CA', false), "' $'"], $alias ?? $value);
    }

    // String

    static function length(string $column, $alias = null)
    {
        return self::function('CHAR_LENGTH', $column, $alias);
    }

    static function concat($columns, $alias = null)
    {
        if(!is_array($columns)) {
            $columns = [$columns];
        }
        else if(is_array($columns[0])) {
            throw new \Exception("Received Array expected string. You probably forgot to disable last function alias");
        }

        return self::functionMultipleArgs('CONCAT', implode(', ', $columns), $alias);
    }

    static function groupConcat(string $rawSQL, $alias = null)
    {
        return self::functionMultipleArgs('GROUP_CONCAT', $rawSQL, $alias);
    }

    // Date

    static function date(string $column, $alias = null)
    {
        return self::function('DATE', $column, $alias);
    }

    static function currentDate($alias = null)
    {
        return self::functionMultipleArgs('CURDATE', null, $alias);
    }

    static function dateAdd($rawDate, $rawIntervalAddition, $alias = null)
    {
        return self::functionMultipleArgs('DATE_ADD', "$rawDate, $rawIntervalAddition", $alias);
    }
}