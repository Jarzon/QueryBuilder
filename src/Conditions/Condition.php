<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Conditions;

use Jarzon\QueryBuilder\Columns\ColumnBase;
use Jarzon\QueryBuilder\Columns\ColumnInterface;
use Jarzon\QueryBuilder\Raw;

class Condition
{
    protected string $column;
    protected ColumnBase|Raw|string|int|float $value;

    public function __construct(
        ColumnInterface|Raw|string $column,
        protected string $operator,
        ColumnBase|Raw|string|int|float|null $value
    ) {
        if($column instanceof ColumnInterface) {
            $column = $column->getColumnReference();
        }
        elseif ($column instanceof Raw) {
            $column = $column->value;
        }

        $this->column = $column;
        $this->value = $value === null? 'NULL' : $value;
    }

    public function getSql(): string
    {
        return "$this->column $this->operator $this->value";
    }
}
