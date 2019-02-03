<?php
namespace Jarzon\Conditions;

class BetweenCondition
{
    protected $type = 'BETWEEN';
    protected $column;
    protected $start;
    protected $end;

    public function __construct(string $column, $start, $end, bool $not = false)
    {
        $this->column = $column;
        $this->start = $start;
        $this->end = $end;

        if($not) {
            $this->type = 'NOT BETWEEN';
        }

        return $this;
    }

    public function getSql()
    {
        return "$this->column $this->type $this->start AND $this->end";
    }
}