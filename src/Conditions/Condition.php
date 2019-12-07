<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Conditions;

use Jarzon\QueryBuilder\Columns\ColumnInterface;

class Condition
{
    /** @var ColumnInterface|string */
    protected $column;
    protected string $operator;
    /** @var string|int|float */
    protected $value;

    public function __construct($column, string $operator, $value)
    {
        if($value === null) {
            $value = 'NULL';
        }

        if($column instanceof ColumnInterface) {
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
