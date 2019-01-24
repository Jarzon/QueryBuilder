<?php
namespace Jarzon;

class Select extends ConditionsQueryBase
{
    protected $columns = ['*'];
    protected $join = [];
    protected $orderBy = [];
    protected $groupBy = [];
    protected $limit = [];

    public function __construct(string $table, ?array $columns)
    {
        $this->type = 'SELECT';

        $this->setTable($table);

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
            $this->limit = [$offset];
        } else {
            $this->limit = [$offset, $select];
        }

        return $this;
    }

    public function leftJoin(string $table, $firstColumnOrCallback, $operator = null, $secondColumn = null)
    {
        $this->workTables[] = $table;

        $this->join[] = new Join('LEFT', $table, $this->workTables, $firstColumnOrCallback, $operator, $secondColumn);

        return $this;
    }
}