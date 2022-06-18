<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder;

use Jarzon\QueryBuilder\Entity\EntityBase;
use Jarzon\QueryBuilder\Statements\Select;
use Jarzon\QueryBuilder\Statements\Insert;
use Jarzon\QueryBuilder\Statements\Update;
use Jarzon\QueryBuilder\Statements\Delete;

abstract class Builder
{
    static string|EntityBase $table = '';
    static string|null $tableAlias;
    static object $pdo;
    static string|EntityBase $currentTable;
    static string|null $local = 'fr_CA';
    static array $currencies = [
        'fr_CA' => ' $',
        'en_CA' => '$',
        'fr_FR' => ' €',
        'en_US' => '$',
    ];

    // Used to change some locals to others because they don't match real world usage
    static array $currency_format = [
        'fr_CA' => 'sv_SE',
        'fr_FR' => 'sv_SE',
    ];

    static function setPDO(object $pdo): void
    {
        self::$pdo = $pdo;
    }

    static function setLocal(string $local): void
    {
        self::$local = $local;
    }

    static function getCurrency(): string
    {
        return self::$currencies[self::$local];
    }

    static function getCurrencyLocal(): string
    {
        return self::$currency_format[self::$local] ?? self::$local;
    }

    static public function setTable(string|EntityBase $table, string $tableAlias = null): void
    {
        self::$table = $table;
        self::$tableAlias = $tableAlias;

        static::$currentTable = $tableAlias !== null? $tableAlias: $table;
    }

    static public function select(EntityBase|string|callable $table, string $tableAlias = null): Select
    {
        if(is_callable($table)) {
            $table = $table();
        }

        self::setTable($table, $tableAlias);

        return new Select($table, $tableAlias, self::$pdo);
    }

    static public function insert(EntityBase|string $table, string $tableAlias = null): Insert
    {
        self::setTable($table, $tableAlias);

        return new Insert($table, self::$pdo);
    }

    static public function update(EntityBase|string $table, string $tableAlias = null): Update
    {
        self::setTable($table, $tableAlias);

        return new Update($table, $tableAlias, self::$pdo);
    }

    static public function delete(EntityBase|string $table, string $tableAlias = null): Delete
    {
        self::setTable($table, $tableAlias);

        return new Delete($table, $tableAlias, self::$pdo);
    }

    static public function raw(string $value): Raw
    {
        return new Raw($value);
    }

    static function function(string $function, string|array $column, string|false|null $alias = null): string|array
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
            if(str_contains($column, '.')) {
                $alias = explode('.', $column)[1];
            }
        }

        return ["$function($column)" => $alias ?? $column];
    }

    /** Columns functions */

    static function functionMultipleArgs(string $function, string|array|null $column, string|false|null $alias = null): string|array
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
            if(str_contains($column, '.')) {
                $alias = explode('.', $column)[1];
            }
        }

        return ["$function($column)" => $alias ?? $column];
    }

    static function concat(array $columns, string|false|null $alias = null): string|array
    {
        return self::functionMultipleArgs('CONCAT', implode(', ', $columns), $alias);
    }

    static function currentDate(string|false|null $alias = null): string|array
    {
        return self::functionMultipleArgs('CURDATE', null, $alias);
    }
}
