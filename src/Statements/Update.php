<?php
namespace Jarzon\QueryBuilder\Statements;

class Update extends ConditionalStatementBase
{
    protected $columns = [];
    protected $query = null;

    public function __construct(string $table, ?string $tableAlias, object $pdo)
    {
        $this->type = 'UPDATE';
        $this->pdo = $pdo;

        $this->table = $table;
        $this->tableAlias = $tableAlias;
    }

    public function set($column, string $value): self
    {
        if(is_object($column)) {
            $column = $column->getColumnName();
        }

        $this->addColumn([$column => $value]);

        return $this;
    }

    public function setRaw(string $column, string $value): self
    {
        $this->addColumn([$column => $value], true);

        return $this;
    }

    public function columns(array $columns): self
    {
        if(!is_array($columns)) {
            $columns = [$columns];
        }

        $this->columns = [];
        $this->addColumn($columns);

        return $this;
    }

    public function addColumn(array $columns, bool $isRaw = false): self
    {
        if(!$isRaw) {
            array_walk($columns, function(&$value, $column) {
                $value = $this->param($value, $column);
            });
        }

        $this->columns += $columns;

        return $this;
    }

    public function getSql(): string
    {
        if($this->query !== null) {
            return $this->query;
        }

        $table = $this->getTable();

        $columns = implode(', ', array_map(function($column, $param) use($table) {
            return "{$table}.$column = $param";
        }, array_keys($this->columns), $this->columns));

        $query = "$this->type $table SET $columns";

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