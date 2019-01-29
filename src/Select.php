<?php
namespace Jarzon;

class Select extends ConditionsQueryBase
{
    protected $columns = ['*'];
    protected $join = [];
    protected $orderBy = [];
    protected $groupBy = [];
    protected $limit = [];

    public function __construct(string $table, ?string $tableAlias, ?array $columns, object $pdo)
    {
        $this->type = 'SELECT';
        $this->pdo = $pdo;

        $this->setTable($table, $tableAlias);

        if($columns !== null) {
            $this->columns = [];
            $this->addSelect($columns);
        }

        return $this;
    }

    protected function getColumns() {
        $columns = implode(', ', array_map(function($key, $name) {
            if($name === '*') return $name;

            $output = $name;

            if(is_int($key) === false) {
                $output = "$key AS $name";
            }

            return $this->columnAlias($output);
        }, array_keys($this->columns), $this->columns));

        return $columns;
    }

    public function getSql()
    {
        $columns = $this->getColumns();

        $query = "$this->type $columns FROM $this->table";

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

    public function addSelect($columns)
    {
        if(is_array($columns)) {
            array_map(function ($key, $column) {
                if(!is_int($key)) {
                    $this->columns[$key] = $column;
                } else {
                    $this->columns[] = $column;
                }

            }, array_keys($columns), $columns);
        } else {
            $this->columns[] = $columns;
        }

        return $this;
    }

    public function orderBy(string $column, string $order = '')
    {
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
            $this->limit = [$this->param($offset)];
        } else {
            $this->limit = [$this->param($offset), $this->param($select)];
        }

        return $this;
    }

    public function leftJoin(string $table, $firstColumnOrCallback, $operator = null, $secondColumn = null)
    {
        $this->join[] = new Join('LEFT', $table, $firstColumnOrCallback, $operator, $secondColumn);

        return $this;
    }

    public function exec(...$params)
    {
        $this->lastStatement = $query = $this->pdo->prepare($this->getSql());

        if(count($params) === 0) {
            $params = $this->params;
        }

        $query->execute($params);

        return $query->fetchAll();
    }
}