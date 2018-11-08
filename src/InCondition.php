<?php
namespace Jarzon;

class InCondition
{
    protected $type = 'IN';
    protected $column;
    protected $list = [];

    public function __construct(string $column, array $list, bool $not = false)
    {
        $this->column = $column;
        $this->list = $list;

        if($not) {
            $this->type = 'NOT IN';
        }

        return $this;
    }

    public function getSql()
    {
        $list = implode(', ', array_map(function($value) {
            if(is_string($value)) {
                $value = "'$value'";
            }
            return $value;
        }, $this->list));
        return "$this->column $this->type ($list)";
    }
}