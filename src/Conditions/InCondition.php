<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Conditions;

use Jarzon\QueryBuilder\Columns\ColumnInterface;

class InCondition
{
    protected string $type = 'IN';

    public function __construct(
        protected string|ColumnInterface $column,
        protected array $list = [],
        bool $not = false
    ) {
        if($column instanceof ColumnInterface) {
            $column = $column->getColumnReference();
        }
        $this->column = $column;

        if($not) {
            $this->type = 'NOT IN';
        }
    }

    public function getSql(): string
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
