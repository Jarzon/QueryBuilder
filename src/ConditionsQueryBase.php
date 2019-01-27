<?php
namespace Jarzon;

class ConditionsQueryBase extends QueryBase
{
    protected $conditions = [];
    protected $params = [];

    public function where($column, ?string $operator = null, $value = null, $isRaw = false)
    {
        if(!$isRaw) {
            $value = $this->param($value);
        }

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

    public function whereRaw($column, ?string $operator = null, $value = null)
    {
        $this->where($column, $operator, $value, true);

        return $this;
    }

    public function or(string $column, string $operator, $value)
    {
        $value = $this->param($value);

        $this->addCondition('OR');

        $this->addCondition(new Condition($column, $operator, $value));

        return $this;
    }

    public function between(string $column, $start, $end)
    {
        $this->addCondition(new BetweenCondition($column, $this->param($start), $this->param($end)));

        return $this;
    }

    public function notBetween(string $column, $start, $end)
    {
        $this->addCondition(new BetweenCondition($column, $this->param($start), $this->param($end), true));

        return $this;
    }

    public function in(string $column, array $list)
    {
        $this->addCondition(new InCondition($column, $list));

        return $this;
    }

    public function notIn(string $column, array $list)
    {
        $this->addCondition(new InCondition($column, $list, true));

        return $this;
    }

    public function isNull(string $column)
    {
        $this->addCondition(new Condition($column, 'IS', null));

        return $this;
    }

    public function isNotNull(string $column)
    {
        $this->addCondition(new Condition($column, 'IS NOT', null));

        return $this;
    }

    protected function param($value)
    {
        $this->params[] = $value;
        return '?';
    }

    protected function addCondition($condition)
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