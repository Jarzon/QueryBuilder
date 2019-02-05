<?php
namespace Jarzon\Statements;

class Delete extends ConditionalStatementBase
{
    public function __construct(string $table, ?string $tableAlias, object $pdo)
    {
        $this->type = 'DELETE';
        $this->pdo = $pdo;

        $this->setTable($table, $tableAlias);
    }

    public function getSql(): string
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