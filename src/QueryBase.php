<?php
namespace Jarzon;

class QueryBase
{
    protected $pdo;
    protected $lastStatement;

    protected $params = [];

    protected $type = '';
    protected $table = '';

    protected function setTable(string $table)
    {
        $this->table = $table;
    }

    public function exec(...$params)
    {
        $this->lastStatement = $query = $this->pdo->prepare($this->getSql());

        if(count($params) === 0) {
            $params = $this->params;
        }

        $query->execute($params);

        return $query;
    }

    protected function param($value)
    {
        $this->params[] = $value;
        return '?';
    }
}