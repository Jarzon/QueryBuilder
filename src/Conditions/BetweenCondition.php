<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Conditions;

use Jarzon\QueryBuilder\Columns\ColumnInterface;

class BetweenCondition
{
    protected string $type = 'BETWEEN';
    /** @var string|ColumnInterface */
    protected $column;
    /** @var ColumnInterface|string|int|float */
    protected $start;
    /** @var ColumnInterface|string|int|float */
    protected $end;

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
