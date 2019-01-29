<?php
namespace Jarzon;

class QueryBuilder
{
    protected $lastQuery;
    protected $table = '';
    protected $tableAlias = null;
    protected $pdo;

    public function __construct(object $pdo)
    {
        $this->pdo = $pdo;
    }

    public function table(string $table, $tableAlias = null)
    {
        $this->table = $table;
        $this->tableAlias = $tableAlias;

        return $this;
    }

    public function getSql(): string
    {
        return $this->lastQuery->getSql();
    }

    public function select($columns = null): Select
    {
        if($columns !== null && !is_array($columns)) {
            $columns = [$columns];
        }

        $query = new Select($this->table, $this->tableAlias, $columns, $this->pdo);

        $this->lastQuery = $query;

        return $query;
    }

    public function insert($columns = []): Insert
    {
        if(!is_array($columns)) {
            $columns = [$columns];
        }

        $query = new Insert($this->table, $this->tableAlias, $this->pdo, $columns);

        $this->lastQuery = $query;

        return $query;
    }

    public function update($columns = []): Update
    {
        if(!is_array($columns)) {
            $columns = [$columns];
        }

        $query = new Update($this->table, $this->tableAlias, $this->pdo, $columns);

        $this->lastQuery = $query;

        return $query;
    }

    public function delete($columns = []): Delete
    {
        if(!is_array($columns)) {
            $columns = [$columns];
        }

        $query = new Delete($this->table, $this->tableAlias, $this->pdo, $columns);

        $this->lastQuery = $query;

        return $query;
    }
}