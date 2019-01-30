<?php
namespace Jarzon;

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

    static function date($column)
    {
        return ["DATE(" . self::$currentTable . ".$column)" => $column];
    }
}