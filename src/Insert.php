<?php
namespace Jarzon;

class Insert extends QueryBase
{
    protected $columns = [];
    protected $values = [];

    public function __construct(string $table, object $pdo, ?array $columns = [])
    {
        $this->type = 'INSERT INTO';
        $this->pdo = $pdo;

        $this->setTable($table);

        if($columns !== null) {
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

    public function addColumn(array $columns)
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

    public function getSql()
    {
        $columns = implode(', ', $this->columns);
        $values = rtrim(str_repeat('?, ', count($this->columns)),", ");

        $query = "$this->type {$this->table}($columns) VALUES ($values)";

        return $query;
    }

    public function exec(...$params)
    {
        $query = parent::exec(...$params);

        return $query->fetchAll();
    }
}