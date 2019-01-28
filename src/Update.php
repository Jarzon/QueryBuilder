<?php
namespace Jarzon;

class Update extends ConditionsQueryBase
{
    protected $columns = [];
    protected $values = [];

    public function __construct(string $table, object $pdo, ?array $columns = [])
    {
        $this->type = 'UPDATE';
        $this->pdo = $pdo;

        $this->setTable($table);

        if($columns !== null) {

            if(!array_key_exists(0, $columns)) {
                $this->values(array_values($columns));
                $columns = array_keys($columns);
            }

            $this->columns = [];
            $this->addColumn($columns);
        }

        return $this;
    }

    public function values(array $values)
    {
        $this->values = $values;

        return $this;
    }

    public function getvalues(): array
    {
        return $this->values;
    }

    public function addColumn(array $columns)
    {
        if(is_array($columns)) {
            array_map(function ($column) {
                $this->columns[] = $column;
            }, $columns);
        } else {
            $this->columns[] = $columns;
        }

        return $this;
    }

    public function getSql()
    {
        $columns = implode(', ', array_map(function($key, $name) {
            return "$name = ?";
        }, array_keys($this->columns), $this->columns));

        $query = "$this->type {$this->table} SET $columns";

        if($conditions = $this->getConditions()) {
            $query .= " WHERE $conditions";
        }

        return $query;
    }

    public function exec(...$params)
    {
        return parent::exec(...$params);
    }
}