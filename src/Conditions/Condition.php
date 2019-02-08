<?php
namespace Jarzon\QueryBuilder\Conditions;

class Condition
{
    protected $column;
    protected $operator;
    protected $value;

    public function __construct($column, string $operator, $value)
    {
        if($value === null) {
            $value = 'NULL';
        }

        if(is_object($column)) {
            $column = $column->getColumnReference();
        }

        $this->column = $column;
        $this->operator = $operator;
        $this->value = $value;
    }

    public function getSql()
    {
        return "$this->column $this->operator $this->value";
    }
}