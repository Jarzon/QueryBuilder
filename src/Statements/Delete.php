<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Statements;

class Delete extends ConditionalStatementBase
{
    public function __construct($table, ?string $tableAlias, object $pdo)
    {
        $this->type = 'DELETE FROM';
        $this->pdo = $pdo;

        $this->table = $table;
        $this->tableAlias = $tableAlias;
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

    public function exec(...$params): int
    {
        $this->lastStatement = $query = $this->pdo->prepare($this->getSql());

        if(count($params) === 0) {
            $params = $this->params;
        }

        $query->execute($params);

        return $query->rowCount();
    }
}
