<?php
namespace Jarzon\QueryBuilder\Statements;

class Select extends ConditionalStatementBase
{
    protected $columns = ['*'];
    protected $join = [];
    protected $orderBy = [];
    protected $groupBy = [];
    protected $limit = [];

    public function __construct(string $table, ?string $tableAlias, object $pdo)
    {
        $this->type = 'SELECT';
        $this->pdo = $pdo;

        $this->table = $table;
        $this->tableAlias = $tableAlias;
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

        if(count($this->orderBy) > 0) {
            $orderBy = implode(', ', array_map(function($column, $order) {
                if($order == null) {
                    return $column;
                }

                return "$column $order";
            }, array_keys($this->orderBy), $this->orderBy));

            $query .= " ORDER BY $orderBy";
        }

        if(count($this->groupBy) > 0) {
            $groupBy = implode(', ', $this->groupBy);

            $query .= " GROUP BY $groupBy";
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

            if(is_object($name)) {
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

            if(is_object($column)) {
                $this->columns[] = $column->getColumnSelect();
            } else {
                $this->columns[] = $column;
            }
        }

        return $this;
    }

    public function orderBy($column, string $order = '')
    {
        if(is_object($column)) {
            $column = $column->getColumnReference();
        }

        if($order === 'desc') {
            $order = 'DESC';
        }
        $this->orderBy[$column] = $order;

        return $this;
    }

    public function groupBy($columns)
    {
        if(!is_array($columns)) {
            $columns = [$columns];
        }

        $this->groupBy = $columns;

        return $this;
    }

    public function limit(int $offset, ?int $select = null)
    {
        if($select === null) {
            $this->limit = [$this->param($offset, 'limit1')];
        } else {
            $this->limit = [$this->param($offset, 'limit1'), $this->param($select, 'limit2')];
        }

        return $this;
    }

    public function leftJoin($table, $firstColumnOrCallback, $operator = null, $secondColumn = null)
    {
        $this->join[] = new Join('LEFT', $table, $firstColumnOrCallback, $operator, $secondColumn);

        return $this;
    }

    public function fetchAll(int $fetch_style)
    {
        $this->lastStatement = $query = $this->pdo->prepare($this->getSql());

        $query->execute($this->params);

        return $query->fetchAll($fetch_style);
    }

    public function fetch()
    {
        $this->lastStatement = $query = $this->pdo->prepare($this->getSql());

        $query->execute($this->params);

        return $query->fetch();
    }

    public function fetchColumn()
    {
        $this->lastStatement = $query = $this->pdo->prepare($this->getSql());

        $query->execute($this->params);

        return $query->fetchColumn();
    }
}