<?php
namespace Jarzon;

class QueryBuilder
{
    protected $lastQuery;
    protected $table;


    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public static function table(string $table)
    {
        return new self($table);
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

        $select = new Select($this->table, $columns);

        $this->lastQuery = $select;

        return $select;
    }
}