<?php
namespace Jarzon\Statements;

use \Jarzon\Conditions\Condition;
use \Jarzon\Conditions\BetweenCondition;
use \Jarzon\Conditions\InCondition;

abstract class ConditionalStatementBase extends StatementBase
{
    protected $conditions = [];

    public function where($column, ?string $operator = null, $value = null, $isRaw = false): self
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
            $this->addCondition(new Condition($column, $operator, $value));
        }

        return $this;
    }

    public function whereRaw($column, ?string $operator = null, $value = null): self
    {
        $this->where($column, $operator, $value, true);

        return $this;
    }

    public function or(string $column, string $operator, $value, $isRaw = false): self
    {
        $this->addCondition('OR');

        $this->addCondition(new Condition($this->$column, $operator, $this->param($value, $column)));

        return $this;
    }

    public function between(string $column, $start, $end, $isRaw = false): self
    {
        $this->addCondition(new BetweenCondition($this->$column, $this->param($start, "{$column}1"), $this->param($end, "{$column}2")));

        return $this;
    }

    public function notBetween(string $column, $start, $end, $isRaw = false): self
    {
        $this->addCondition(new BetweenCondition($this->$column, $this->param($start, "{$column}1"), $this->param($end, "{$column}2"), true));

        return $this;
    }

    public function in(string $column, array $list, $isRaw = false): self
    {
        $this->addCondition(new InCondition($this->$column, $list));

        return $this;
    }

    public function notIn(string $column, array $list, $isRaw = false): self
    {
        $this->addCondition(new InCondition($this->$column, $list, true));

        return $this;
    }

    public function isNull(string $column, $isRaw = false): self
    {
        $this->addCondition(new Condition($this->$column, 'IS', null));

        return $this;
    }

    public function isNotNull(string $column, $isRaw = false): self
    {
        $this->addCondition(new Condition($this->$column, 'IS NOT', null));

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