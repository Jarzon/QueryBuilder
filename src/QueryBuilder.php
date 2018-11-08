<?php
namespace Jarzon;

class QueryBuilder
{
    protected $lastQuery;


    public function __construct()
    {

    }

    public function getSql(): string
    {
        return $this->lastQuery->getSql();
    }

    public function select(string $table)
    {
        $select = new Select($table);

        $this->lastQuery = $select;

        return $select;
    }
}