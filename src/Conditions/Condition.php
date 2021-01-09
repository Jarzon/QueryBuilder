<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Conditions;

use Jarzon\QueryBuilder\Columns\ColumnInterface;
use Jarzon\QueryBuilder\Columns\Date;
use Jarzon\QueryBuilder\Columns\Numeric;
use Jarzon\QueryBuilder\Columns\Text;
use Jarzon\QueryBuilder\Raw;

class Condition
{
    protected ColumnInterface|string $column;
    protected string $operator;
    protected Numeric|Text|Date|Raw|string|int|float $value;

    public function __construct($column, string $operator, $value)
    {
        if($value === null) {
            $value = 'NULL';
        }

        if($column instanceof ColumnInterface) {
            $column = $column->getColumnReference();
        }
        elseif ($column instanceof Raw) {
            $column = $column->value;
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
