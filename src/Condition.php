<?php
namespace Jarzon;

class Condition
{
    protected $column;
    protected $operator;
    protected $value;

    public function __construct(string $column, string $operator, $value)
    {
        if($value === null) {
            $value = 'NULL';
        }

        $this->column = $column;
        $this->operator = $operator;
        $this->value = $value;

        return $this;
    }

    public function getSql()
    {
        return "$this->column $this->operator $this->value";
    }
}