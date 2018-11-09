<?php
namespace Jarzon;

class Select
{
    protected $queryType = 'SELECT';
    protected $table = '';
    protected $columns = ['*'];
    protected $conditions = [];
    protected $orderBy = [];
    protected $groupBy = [];
    protected $limit = [];

    public function __construct(string $table, ?array $columns)
    {
        $this->table = $table;
        if($columns !== null) {
            $this->columns = [];
            $this->addSelect($columns);
        }

        return $this;
    }

    public function getSql()
    {
        $columns = implode(', ', array_map(function($key, $name) {
            $output = $name;

            if(is_int($key) === false) {
                $output = "$key AS $name";
            }

            return $output;
        }, array_keys($this->columns), $this->columns));

        $query = "$this->queryType $columns FROM $this->table";

        if(count($this->conditions) > 0) {
            $conditions = implode(' ', array_map(function($condition) {
                if(is_string($condition)) {
                    return $condition;
                }

                return $condition->getSql();
            }, $this->conditions));

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

    protected function addCondition($condition)
    {
        $this->conditions[] = $condition;
    }

    public function where($column, ?string $operator = null, $value = null)
    {
        if(is_string($value)) {
            $value = "'$value'";
        }

        $conditionsCount = count($this->conditions);
        if($conditionsCount > 0 && $this->conditions[$conditionsCount-1] != '(') {
            $this->conditions[] = 'AND';
        }

        if(is_callable($column) && $column instanceof \Closure) {
            $this->conditions[] = '(';
            $column($this);
            $this->conditions[] = ')';
        } else {
            $this->addCondition(new Condition($column, $operator, $value));
        }

        return $this;
    }

    public function or(string $column, string $operator, $value)
    {
        if(is_string($value)) {
            $value = "'$value'";
        }

        $this->addCondition('OR');

        $this->addCondition(new Condition($column, $operator, $value));

        return $this;
    }

    public function between(string $column, $start, $end)
    {
        $this->addCondition(new BetweenCondition($column, $start, $end));

        return $this;
    }

    public function notBetween(string $column, $start, $end)
    {
        $this->addCondition(new BetweenCondition($column, $start, $end, true));

        return $this;
    }

    public function in(string $column, array $list)
    {
        $this->addCondition(new InCondition($column, $list));

        return $this;
    }

    public function notIn(string $column, array $list)
    {
        $this->addCondition(new InCondition($column, $list, true));

        return $this;
    }

    public function isNull(string $column)
    {
        $this->addCondition(new Condition($column, 'IS', null));

        return $this;
    }

    public function isNotNull(string $column)
    {
        $this->addCondition(new Condition($column, 'IS NOT', null));

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
            $this->limit = [$offset];
        } else {
            $this->limit = [$offset, $select];
        }

        return $this;
    }
}