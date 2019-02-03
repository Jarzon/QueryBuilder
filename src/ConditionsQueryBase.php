<?php
namespace Jarzon;

use \Jarzon\Conditions\Condition;
use \Jarzon\Conditions\BetweenCondition;
use \Jarzon\Conditions\InCondition;

abstract class ConditionsQueryBase extends QueryBase
{
    protected $conditions = [];

    public function where($column, ?string $operator = null, $value = null, $isRaw = false)
    {
        $value = $this->param($value, $column, $isRaw);

        $conditionsCount = count($this->conditions);
        if($conditionsCount > 0 && $this->conditions[$conditionsCount-1] != '(') {
            $this->addCondition('AND');
        }

        if(is_callable($column) && $column instanceof \Closure) {
            $this->addCondition('(');
            $column($this);
            $this->addCondition(')');
        } else {
            $this->addCondition(new Condition($this->columnAlias($column, $isRaw), $operator, $value));
        }

        return $this;
    }

    public function whereRaw($column, ?string $operator = null, $value = null)
    {
        $this->where($column, $operator, $value, true);

        return $this;
    }

    public function or(string $column, string $operator, $value, $isRaw = false)
    {
        $this->addCondition('OR');

        $this->addCondition(new Condition($this->columnAlias($column, $isRaw), $operator, $this->param($value, $column)));

        return $this;
    }

    public function between(string $column, $start, $end, $isRaw = false)
    {
        $this->addCondition(new BetweenCondition($this->columnAlias($column, $isRaw), $this->param($start, "{$column}1"), $this->param($end, "{$column}2")));

        return $this;
    }

    public function notBetween(string $column, $start, $end, $isRaw = false)
    {
        $this->addCondition(new BetweenCondition($this->columnAlias($column, $isRaw), $this->param($start, "{$column}1"), $this->param($end, "{$column}2"), true));

        return $this;
    }

    public function in(string $column, array $list, $isRaw = false)
    {
        $this->addCondition(new InCondition($this->columnAlias($column, $isRaw), $list));

        return $this;
    }

    public function notIn(string $column, array $list, $isRaw = false)
    {
        $this->addCondition(new InCondition($this->columnAlias($column, $isRaw), $list, true));

        return $this;
    }

    public function isNull(string $column, $isRaw = false)
    {
        $this->addCondition(new Condition($this->columnAlias($column, $isRaw), 'IS', null));

        return $this;
    }

    public function isNotNull(string $column, $isRaw = false)
    {
        $this->addCondition(new Condition($this->columnAlias($column, $isRaw), 'IS NOT', null));

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