<?php
namespace Jarzon;

use Jarzon\Statements\Select;
use Jarzon\Statements\Insert;
use Jarzon\Statements\Update;
use Jarzon\Statements\Delete;

abstract class QueryBuilder
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

    static public function select($table, $tableAlias = null): Select
    {
        self::setTable($table, $tableAlias);

        return new Select($table, $tableAlias, self::$pdo);
    }

    static public function insert($table, $tableAlias = null): Insert
    {
        self::setTable($table, $tableAlias);

        return new Insert($table, self::$pdo);
    }

    static public function update($table, $tableAlias = null): Update
    {
        self::setTable($table, $tableAlias);

        return new Update($table, $tableAlias, self::$pdo);
    }

    static public function delete($table, $tableAlias = null): Delete
    {
        self::setTable($table, $tableAlias);

        return new Delete($table, $tableAlias, self::$pdo);
    }

    static public function raw(string $value)
    {
        return new Raw($value);
    }

    static function function(string $function, $column, $alias = null)
    {
        // No alias
        if($alias === false) {
            return "$function($column)";
        }
        // Custom alias
        else if(is_array($column)) {
            $column = array_values($column)[0];
        }
        // Default alias
        else if($alias === null) {
            if(strpos($column, '.') !== false) {
                $alias = explode('.', $column)[1];
            }
        }

        return ["$function($column)" => $alias ?? $column];
    }

    static function functionMultipleArgs(string $function, $column, $alias = null)
    {
        // No alias
        if($alias === false) {
            return "$function($column)";
        }
        // Custom alias
        else if(is_array($column)) {
            $column = array_values($column)[0];
        }
        // Default alias
        else if($alias === null) {
            if(strpos($column, '.') !== false) {
                $alias = explode('.', $column)[1];
            }
        }

        return ["$function($column)" => $alias ?? $column];
    }

    static function concat(array $columns, $alias = null)
    {
        return self::functionMultipleArgs('CONCAT', implode(', ', $columns), $alias);
    }

    static function currentDate($alias = null)
    {
        return self::functionMultipleArgs('CURDATE', null, $alias);
    }
}