<?php
namespace Jarzon\QueryBuilder\Statements;

use \Jarzon\QueryBuilder\Conditions\Condition;
use \Jarzon\QueryBuilder\Conditions\BetweenCondition;
use \Jarzon\QueryBuilder\Conditions\InCondition;

abstract class ConditionalStatementBase extends StatementBase
{
    protected $conditions = [];

    public function where($column, ?string $operator = null, $value = null, $isRaw = false)
    {
        $conditionsCount = count($this->conditions);
        if($conditionsCount > 0 && $this->conditions[$conditionsCount-1] != '(') {
            $this->addCondition('AND');
        }

        if($operator === null && is_callable($column) && $column instanceof \Closure) {
            $this->addCondition('(');
            $column($this);
            $this->addCondition(')');

            return $this;
        }

        $value = $this->param($value, $column, $isRaw);

        $this->addCondition(new Condition($column, $operator, $value));

        return $this;
    }

    public function whereRaw($column, ?string $operator = null, $value = null)
    {
        $this->where($column, $operator, $value, true);

        return $this;
    }

    public function or($column, string $operator, $value, $isRaw = false)
    {
        $this->addCondition('OR');

        $this->addCondition(new Condition($column, $operator, $this->param($value, $column)));

        return $this;
    }

    public function between($column, $start, $end, $isRaw = false)
    {
        $this->addCondition(new BetweenCondition($column, $this->param($start, $column), $this->param($end, $column)));

        return $this;
    }

    public function notBetween($column, $start, $end, $isRaw = false)
    {
        $this->addCondition(new BetweenCondition($column, $this->param($start, $column), $this->param($end, $column), true));

        return $this;
    }

    public function in($column, array $list, $isRaw = false)
    {
        $this->addCondition(new InCondition($column, $list));

        return $this;
    }

    public function notIn($column, array $list, $isRaw = false)
    {
        $this->addCondition(new InCondition($column, $list, true));

        return $this;
    }

    public function isNull($column, $isRaw = false)
    {
        $this->addCondition(new Condition($column, 'IS', null));

        return $this;
    }

    public function isNotNull($column, $isRaw = false)
    {
        $this->addCondition(new Condition($column, 'IS NOT', null));

        return $this;
    }

    protected function addCondition($condition, $isRaw = false)
    {
        $this->conditions[] = $condition;
    }

    protected function getConditions(): ?string
    {
        if(count($this->conditions) === 0) return null;

        $conditions = implode(' ', array_map(function($condition) {
            if(is_string($condition)) {
                return $condition;
            }

            return $condition->getSql();
        }, $this->conditions));

        return $conditions;
    }
}