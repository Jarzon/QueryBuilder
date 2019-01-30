<?php
namespace Jarzon;

class Delete extends ConditionsQueryBase
{
    protected $columns = [];
    protected $values = [];

    public function __construct(string $table, ?string $tableAlias, object $pdo, ?array $columns = [])
    {
        $this->type = 'DELETE';
        $this->pdo = $pdo;

        $this->setTable($table, $tableAlias);

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
        $table = $this->getTable();

        $query = "$this->type $table";

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