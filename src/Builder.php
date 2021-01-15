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
    static ?string $tableAlias;
    static object $pdo;
    static string|EntityBase $currentTable;
    static ?string $local = 'fr_CA';
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

    static function getCurrencyLocal()
    {
        return self::$currency_format[self::$local] ?? self::$local;
    }

    static public function setTable($table, $tableAlias = null)
    {
        self::$table = $table;
        self::$tableAlias = $tableAlias;

        static::$currentTable = $tableAlias !== null? $tableAlias: $table;
    }

    static public function select($table, $tableAlias = null): Select
    {
        if(is_callable($table)) {
            $table = $table();
        }

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

    static public function raw(string $value): Raw
    {
        return new Raw($value);
    }

    static function function(string $function, $column, $alias = null): string|array
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

    /** Columns functions */

    static function functionMultipleArgs(string $function, $column, $alias = null): string|array
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

    static function concat(array $columns, $alias = null): string|array
    {
        return self::functionMultipleArgs('CONCAT', implode(', ', $columns), $alias);
    }

    static function currentDate($alias = null): string|array
    {
        return self::functionMultipleArgs('CURDATE', null, $alias);
    }
}
