<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Conditions;

use Jarzon\QueryBuilder\Columns\ColumnInterface;

class BetweenCondition
{
    protected string $type = 'BETWEEN';
    protected string|ColumnInterface $column;
    protected ColumnInterface|string|int|float $start;
    protected ColumnInterface|string|int|float $end;

    public function __construct($column, $start, $end, bool $not = false)
    {
        $this->column = $column;
        $this->start = $start;
        $this->end = $end;

        if($not) {
            $this->type = 'NOT BETWEEN';
        }
    }

    public function getSql(): string
    {
        return "$this->column $this->type $this->start AND $this->end";
    }
}
