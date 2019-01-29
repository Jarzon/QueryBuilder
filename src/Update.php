<?php
namespace Jarzon;

class Update extends ConditionsQueryBase
{
    protected $columns = [];
    protected $query = null;

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

    public function set(string $column, string $value)
    {
        $this->addColumn([$column => $value]);

        return $this;
    }

    public function setRaw(string $column, string $value)
    {
        $this->addColumn([$column => $value], true);

        return $this;
    }

    public function addColumn(array $columns, bool $isRaw = false)
    {
        if(!$isRaw) {
            array_walk($columns, function(&$value, $column) {
                $value = $this->param($value, $column);
            });
        }

        $this->columns += $columns;

        return $this;
    }

    public function getSql()
    {
        if($this->query !== null) {
            return $this->query;
        }

        $columns = implode(', ', array_map(function($column, $param) {
            return "{$this->table}.$column = $param";
        }, array_keys($this->columns), $this->columns));

        $query = "$this->type {$this->table} SET $columns";

        if($conditions = $this->getConditions()) {
            $query .= " WHERE $conditions";
        }

        $this->query = $query;
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