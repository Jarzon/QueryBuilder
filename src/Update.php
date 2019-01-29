<?php
namespace Jarzon;

class Update extends ConditionsQueryBase
{
    protected $columns = [];
    protected $values = [];

    public function __construct(string $table, ?string $tableAlias, object $pdo, ?array $columns = [])
    {
        $this->type = 'UPDATE';
        $this->pdo = $pdo;

        $this->setTable($table, $tableAlias);

        if($columns !== null) {
            $this->addColumn($columns);
        }

        return $this;
    }

    public function values(array $values)
    {
        array_map(function ($value) {
            $this->values[] = $value;
        }, $values);

        return $this;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function addColumn(array $columns)
    {
        $this->columns += $columns;

        return $this;
    }

    public function getSql()
    {
        $columns = implode(', ', array_map(function($column, $value) {
            return "{$this->table}.$column = {$this->param($value, $column)}";
        }, array_keys($this->columns), $this->columns));

        $query = "$this->type {$this->table} SET $columns";

        if($conditions = $this->getConditions()) {
            $query .= " WHERE $conditions";
        }

        return $query;
    }

    public function exec(...$params)
    {
        $this->lastStatement = $query = $this->pdo->prepare($this->getSql());

        if(count($params) === 0) {
            $params = $this->params;
        }

        return $query->execute($params);
    }
}