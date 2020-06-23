<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder;

use Jarzon\QueryBuilder\Columns\ColumnInterface;
use Jarzon\QueryBuilder\Statements\Select;
use Jarzon\QueryBuilder\Statements\Insert;
use Jarzon\QueryBuilder\Statements\Update;
use Jarzon\QueryBuilder\Statements\Delete;

abstract class Builder
{
    /** @var string|ColumnInterface */
    static $table = '';
    static ?string $tableAlias;
    static object $pdo;
    static ?string $currency = 'french cad';
    /** @var string|ColumnInterface */
    static $currentTable;
    static array $currencies = [
        'french cad' => ' $',
        'english cad' => '$',
        'euro' => '€',
        'us dollar' => '$',
    ];
    static array $currency_format = [
        'french cad' => 'sv_SE',
        'english cad' => 'en_CA',
        'euro' => 'fr_fr',
        'us dollar' => 'en_US',
    ];

    static function setPDO(object $pdo)
    {
        self::$pdo = $pdo;
    }

    static function setCurrency(string $currency)
    {
        self::$currency = $currency;
    }

    static function getCurrency()
    {
        return self::$currencies[self::$currency];
    }

    static function getFormat()
    {
        return self::$currency_format[self::$currency];
    }

    static public function setTable($table, $tableAlias = null)
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

    /** Columns functions */

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
