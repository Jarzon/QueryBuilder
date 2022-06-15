<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Conditions;

use Jarzon\QueryBuilder\Columns\ColumnInterface;
use Jarzon\QueryBuilder\Raw;

class BetweenCondition
{
    protected string $type = 'BETWEEN';

    public function __construct(
        protected string|ColumnInterface $column,
        protected Raw|ColumnInterface|string|int|float $start,
        protected Raw|ColumnInterface|string|int|float $end,
        bool $not = false
    ) {

        if($not) {
            $this->type = 'NOT BETWEEN';
        }
    }

    public function getSql(): string
    {
        return "$this->column $this->type $this->start AND $this->end";
    }
}
