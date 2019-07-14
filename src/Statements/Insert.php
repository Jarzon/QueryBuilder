<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Statements;

use Jarzon\QueryBuilder\Columns\ColumnBase;
use Jarzon\QueryBuilder\Entity\EntityBase;

class Insert extends StatementBase
{
    protected $columns = [];
    protected $values = [];

    public function __construct($table, object $pdo)
    {
        $this->type = 'INSERT INTO';
        $this->pdo = $pdo;

        $this->table = $table;
    }

    public function values(array $values)
    {
        $this->values = $values;

        return $this;
    }

    public function columns(...$columns)
    {
        if(is_array($columns[0])) {
            $columns = $columns[0];
        }

        $this->columns = [];
        $this->addColumn($columns);

        return $this;
    }

    public function addColumn($columns, $value = null)
    {
        if($columns instanceof ColumnBase) {
            $columns = [$columns->getColumnName() => $value];
        } else {
            $columns = array_filter($columns, function($column) {
                return (is_int($column) || !$this->table instanceof EntityBase) || ($this->table instanceof EntityBase && $this->table->columnExist($column));
            }, ARRAY_FILTER_USE_KEY);
        }

        array_walk($columns, function(&$value, $column) {
            if(!is_int($column)) {
                $value = $this->param($value, $column);
                $this->columns[$value] = $column;
            }
        });

        if(empty($this->columns)) {
            $this->columns = $columns;
        }

        return $this;
    }

    public function getSql(): string
    {
        $columns = implode(', ', $this->columns);
        $values = ':'.implode(', :', $this->columns);

        $query = "$this->type {$this->table}($columns) VALUES ($values)";

        return $query;
    }

    public function exec(...$params)
    {
        $this->lastStatement = $this->pdo->prepare($this->getSql());

        if(count($params) === 0) {
            $params = $this->params;
        }

        $this->lastStatement->execute($params);

        return $this->pdo->lastInsertId();
    }
}