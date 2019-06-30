<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Conditions;

class BetweenCondition
{
    protected $type = 'BETWEEN';
    protected $column;
    protected $start;
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