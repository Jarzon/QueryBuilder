<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Statements;

use Jarzon\QueryBuilder\Columns\ColumnInterface;
use Jarzon\QueryBuilder\Entity\EntityBase;
use Jarzon\QueryBuilder\Raw;

class Select extends ConditionalStatementBase
{
    protected array $columns = ['*'];
    protected array $join = [];
    protected array $orderBy = [];

    protected array $groupBy = [];
    protected array $limit = [];
    protected bool $groupByRollup = false;

    public function __construct($table, ?string $tableAlias, object $pdo)
    {
        $this->type = 'SELECT';
        $this->pdo = $pdo;

        $this->table = $table;
        $this->tableAlias = $tableAlias;

        if($this->table instanceof EntityBase) {
            $this->table->resetParamCount();
        }
    }

    public function getSql(): string
    {
        $columns = $this->getColumns();

        $table = $this->getTable();

        $query = "$this->type $columns FROM $table";

        if(count($this->join) > 0) {
            $joins = implode(' ', array_map(function($join) {
                return $join->getSql();
            }, $this->join));

            $query .= " $joins";
        }

        if($conditions = $this->getConditions()) {
            $query .= " WHERE $conditions";
        }

        if(count($this->groupBy) > 0) {
            $groupBy = implode(', ', $this->groupBy);

            $query .= " GROUP BY $groupBy";

            if($this->groupByRollup) {
                $query .= " WITH ROLLUP";
            }
        }

        if(count($this->orderBy) > 0) {
            $orderBy = implode(', ', array_map(function ($entry) {
                if ($entry[1] == null) {
                    return $entry[0];
                }

                return implode(' ', $entry);
            }, $this->orderBy));

            $query .= " ORDER BY $orderBy";
        }

        if(count($this->limit) > 0) {
            $limit = implode(', ', $this->limit);

            $query .= " LIMIT $limit";
        }

        return $query;
    }

    public function columns(...$columns)
    {
        $this->columns = [];
        $this->addColumns(...$columns);

        return $this;
    }

    protected function getColumns(): string
    {
        $columns = implode(', ', array_map(function($key, $name) {
            if($name === '*') return $name;

            $output = $name;

            if($name instanceof ColumnInterface) {
                $output = $name->getOutput();
            }
            else if(is_array($name)) {
                $key = array_key_first($name);
                return "$key AS $name[$key]";
            }
            else if(!is_int($key)) {
                $output = $name;
            }

            return $output;
        }, array_keys($this->columns), $this->columns));

        return $columns;
    }

    public function addColumns(...$columns)
    {
        foreach ($columns as $column) {

            if($column instanceof ColumnInterface) {
                $this->columns[] = $column->getColumnSelect();
            }
            elseif ($column instanceof Raw) {
                $this->columns[] = $column->value;
            } else {
                $this->columns[] = $column;
            }
        }

        return $this;
    }

    public function orderBy($column, string $order = '')
    {
        if($column instanceof ColumnInterface) {
            $column = $column->getColumnReference();
        }
        elseif ($column instanceof Raw) {
            $column = $column->value;
        }

        $this->orderBy[] = [$column, strtoupper($order)];

        return $this;
    }

    public function groupBy($columns, bool $rollUp = false)
    {
        if(!is_array($columns)) {
            $columns = [$columns];
        }

        $this->groupBy = $columns;
        $this->groupByRollup = $rollUp;

        return $this;
    }

    public function limit(int $offset, ?int $select = null, $isRaw = false)
    {
        if($select === null) {
            $this->limit = [$this->param($offset, 'limit1', $isRaw)];
        } else {
            $this->limit = [$this->param($offset, 'limit1', $isRaw), $this->param($select, 'limit2', $isRaw)];
        }

        return $this;
    }

    public function leftJoin($table, $firstColumnOrCallback, $operator = null, $secondColumn = null)
    {
        $this->join[] = new Join('LEFT', $table, $firstColumnOrCallback, $operator, $secondColumn);

        return $this;
    }

    public function fetchAll(int $fetch_style = 0)
    {
        $this->lastStatement = $query = $this->pdo->prepare($this->getSql());

        $query->execute($this->params);

        return $query->fetchAll($fetch_style);
    }

    public function fetch(int $fetch_style = 0)
    {
        $this->lastStatement = $query = $this->pdo->prepare($this->getSql());

        $query->execute($this->params);

        return $query->fetch($fetch_style);
    }

    public function fetchClass(?string $class = null)
    {
        $this->lastStatement = $query = $this->pdo->prepare($this->getSql());

        if($class === null && $this->table instanceof EntityBase) {
            $class = $this->table->entityClass;
        }

        $query->setFetchMode(\PDO::FETCH_CLASS, $class);

        $query->execute($this->params);

        return $query->fetch();
    }

    public function fetchClassAll(?string $class = null)
    {
        $this->lastStatement = $query = $this->pdo->prepare($this->getSql());

        if($class === null && $this->table instanceof EntityBase) {
            $class = $this->table->entityClass;
        }

        $query->setFetchMode(\PDO::FETCH_CLASS, $class);

        $query->execute($this->params);

        return $query->fetchAll();
    }

    public function fetchColumn()
    {
        $this->lastStatement = $query = $this->pdo->prepare($this->getSql());

        $query->execute($this->params);

        return $query->fetchColumn();
    }
}
