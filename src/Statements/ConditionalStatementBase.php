<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Statements;

use Jarzon\QueryBuilder\Columns\ColumnInterface;
use \Jarzon\QueryBuilder\Conditions\Condition;
use \Jarzon\QueryBuilder\Conditions\BetweenCondition;
use \Jarzon\QueryBuilder\Conditions\InCondition;
use Jarzon\QueryBuilder\Raw;

abstract class ConditionalStatementBase extends StatementBase
{
    protected array $conditions = [];

    public function where(ColumnInterface|Raw|string|callable $column, ?string $operator = null, ColumnInterface|Raw|string|int|float $value = null, bool $isRaw = false): static
    {
        $this->chaining();

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

    public function whereRaw(ColumnInterface|Raw|string $column, ?string $operator = null, ColumnInterface|string|int|float $value = null): static
    {
        $this->where($column, $operator, $value, true);

        return $this;
    }

    public function or(ColumnInterface|Raw|string|callable $column, string $operator = null, string|int|float $value = null, bool $isRaw = false): static
    {
        $this->addCondition('OR');

        if($operator === null && is_callable($column) && $column instanceof \Closure) {
            $this->addCondition('(');
            $column($this);
            $this->addCondition(')');

            return $this;
        }

        $this->addCondition(new Condition($column, $operator, $this->param($value, $column, $isRaw)));

        return $this;
    }

    public function between(ColumnInterface|Raw|string|int|float $column, Raw|ColumnInterface|string|int|float $start, Raw|ColumnInterface|string|int|float $end, bool $isRaw = false): static
    {
        $this->chaining();

        $this->addCondition(new BetweenCondition($column, $this->param($start, $column, $isRaw), $this->param($end, $column, $isRaw)));

        return $this;
    }

    public function notBetween(ColumnInterface|Raw|string $column, Raw|ColumnInterface|string|int|float $start, Raw|ColumnInterface|string|int|float $end, bool $isRaw = false): static
    {
        $this->chaining();

        $this->addCondition(new BetweenCondition($column, $this->param($start, $column, $isRaw), $this->param($end, $column, $isRaw), true));

        return $this;
    }

    public function in(ColumnInterface|Raw|string $column, array $list, bool $isRaw = false): static
    {
        $this->chaining();

        $this->addCondition(new InCondition($column, $list));

        return $this;
    }

    public function notIn(ColumnInterface|Raw|string $column, array $list, bool $isRaw = false): static
    {
        $this->chaining();

        $this->addCondition(new InCondition($column, $list, true));

        return $this;
    }

    public function isNull(ColumnInterface|Raw|string $column, bool $isRaw = false): static
    {
        $this->chaining();

        $this->addCondition(new Condition($column, 'IS', null));

        return $this;
    }

    public function isNotNull(ColumnInterface|Raw|string $column, bool $isRaw = false): static
    {
        $this->chaining();

        $this->addCondition(new Condition($column, 'IS NOT', null));

        return $this;
    }

    protected function chaining(): void
    {
        $conditionsCount = count($this->conditions);
        if($conditionsCount > 0 && $this->conditions[$conditionsCount-1] != '(') {
            $this->addCondition('AND');
        }
    }

    protected function addCondition(Condition|BetweenCondition|InCondition|string $condition, bool $isRaw = false): void
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
