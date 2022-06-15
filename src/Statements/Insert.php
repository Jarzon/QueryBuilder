<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Statements;

use Jarzon\QueryBuilder\Columns\ColumnBase;
use Jarzon\QueryBuilder\Entity\EntityBase;
use Jarzon\QueryBuilder\Raw;

class Insert extends StatementBase
{
    protected array $columns = [];
    protected ?Select $select = null;
    protected array $values = [];

    public function __construct(string|EntityBase $table, object $pdo)
    {
        $this->type = 'INSERT INTO';
        $this->pdo = $pdo;

        $this->table = $table;
        if($this->table instanceof EntityBase) {
            $this->table->resetParamCount();
        }
    }

    public function values(array $values): Insert
    {
        $this->values = $values;

        return $this;
    }

    public function columns(...$columns): Insert
    {
        if(is_array($columns[0])) {
            $columns = $columns[0];
        }

        $this->columns = [];
        $this->addColumn($columns);

        return $this;
    }

    public function select(Select $subQuery): Insert
    {
        $this->select = $subQuery;

        return $this;
    }

    public function addColumn($columns, $value = null, bool $raw = false): Insert
    {
        if($value !== null) {
            $columns = [$value => $columns];
        }

        foreach ($columns as $i => $column) {
            if(!$column instanceof ColumnBase && !$this->table->columnExist($i)) continue;

            if($column instanceof ColumnBase) {
                $value = $this->param($i, $column, $raw);
                $this->columns[$value] = $column->getColumnName();
            }
            else if(!empty($column)) {
                $value = $this->param($column, $i, $raw);
                $this->columns[$i] = $i;
            }
        }

        return $this;
    }

    public function getSql(): string
    {
        $columns = implode(', ', $this->columns);
        $values = ':' . implode(', :', $this->columns);

        $table = $this->table;

        if($this->table instanceof EntityBase) {
            $table = $this->table->table;
        }

        $output = "$this->type {$table}($columns) ";

        if($this->select === null) {
            $output .= "VALUES ($values)";
        } else {
            $output .= $this->select->getSql();
            $this->params = $this->select->params;
        }

        return $output;
    }

    public function exec(...$params): int
    {
        $this->lastStatement = $this->pdo->prepare($this->getSql());

        if(count($params) === 0) {
            $params = $this->params;
        }

        $this->lastStatement->execute($params);

        $id = $this->pdo->lastInsertId();

        if(is_numeric($id)) {
            return (int)$id;
        }

        return $id;
    }
}
