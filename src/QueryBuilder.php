<?php
namespace Jarzon;

class QueryBuilder
{
    protected $lastQuery;
    protected $table;
    protected $pdo;

    public function __construct(object $pdo)
    {
        $this->pdo = $pdo;
    }

    public function table(string $table)
    {
        $this->table = $table;

        return $this;
    }

    public function getSql(): string
    {
        return $this->lastQuery->getSql();
    }

    public function select($columns = null)
    {
        if($columns !== null && !is_array($columns)) {
            $columns = [$columns];
        }

        $select = new Select($this->table, $columns, $this->pdo);

        $this->lastQuery = $select;

        return $select;
    }
}