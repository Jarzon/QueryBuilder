<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Columns;

use Jarzon\QueryBuilder\Raw;

class ColumnBase implements ColumnInterface
{
    public ?string $alias = null;
    public string|Raw|null $output = null;
    public int $paramCount = 1;

    public function __construct(
        public string $name = '',
        public ?string $tableAlias = null
    ) {

    }

    public function alias(string $alias): string
    {
        $this->alias = $alias;

        $output = $this->getColumnSelect();

        $this->output = null;

        return $output;
    }

    public function cast(string $type): static
    {
        $this->output = new Raw("CAST({$this->getOutput()} AS $type)");

        return $this;
    }

    public function getOutput(): string|Raw
    {
        return $this->output ?? new Raw($this->getColumnReference());
    }

    public function get(): string|Raw
    {
        $output = $this->output ?? new Raw($this->getColumnReference());
        $this->output = null;
        return $output;
    }

    public function getColumnOutput(): string|Raw
    {
        $output = $this->getOutput();

        $this->output = null;

        return $output;
    }

    public function getColumnName(): string
    {
        return $this->name;
    }

    public function getColumnParamName(): string
    {
        $parameterName = $this->name . ($this->paramCount > 1? $this->paramCount: '');
        $this->paramCount++;
        return $parameterName;
    }

    public function getColumnReference(): string
    {
        return ($this->tableAlias != ''? "$this->tableAlias.": '') . $this->getColumnName();
    }

    public function getColumnSelect(): string
    {
        $output = $this->getOutput();

        if($output instanceof Raw) {
            $output = $output->value;
        }

        if($this->output !== null || $this->alias !== null) {
            $output .= " AS " . ($this->alias ?? $this->name);
        }

        return $output;
    }

    /** Functions */

    protected function parseArgs(array &$args): array
    {
        foreach ($args as $i => $arg) {
            $args[$i] = $arg instanceof ColumnInterface? $arg->getOutput() :($arg instanceof Raw? $arg: "'$arg'");
        }

        return $args;
    }

    public function distinct(): static
    {
        $this->output = new Raw("DISTINCT {$this->getOutput()}");

        return $this;
    }

    public function count(): static
    {
        $this->output = new Raw("COUNT({$this->getOutput()})");

        return $this;
    }

    public function concat(array $args): void
    {
        $args = $this->parseArgs($args);

        $this->output = new Raw("CONCAT(" . implode(', ', $args) . ")");
    }

    public function preAppend(ColumnBase|Raw|string ...$args): static
    {
        $args[] = $this->getOutput();

        $this->concat($args);

        return $this;
    }

    public function append(ColumnBase|Raw|string ...$args): static
    {
        array_unshift($args,  $this->getOutput());

        $this->concat($args);

        return $this;
    }

    public function ifIsNull(string|int|float $value): static
    {
        $args[] = $this->getOutput();

        $args = $this->parseArgs($args);

        $this->output = new Raw("IFNULL(" .implode(', ', $args) . ", $value)");
        return $this;
    }

    public function if(string|int|float $value, string $operator, ColumnBase|string|int|float $expr1, ColumnBase|string|int|float $expr2): static
    {
        $this->output = new Raw("IF(" . $this->getColumnReference() . " $operator $value, $expr1, $expr2)");

        return $this;
    }

    public function ifIsGreaterThat(string|int|float $value, ColumnBase|string|int|float $expr1, ColumnBase|string|int|float $expr2): static
    {
        return $this->if($value, '>', $expr1, $expr2);
    }

    public function ifIsLowerThat(string|int|float $value, ColumnBase|string|int|float $expr1, ColumnBase|string|int|float $expr2): static
    {
        return $this->if($value, '<', $expr1, $expr2);
    }

    public function ifIsGreaterThatOrEqual(string|int|float $value, ColumnBase|string|int|float $expr1, ColumnBase|string|int|float $expr2): static
    {
        return $this->if($value, '>=', $expr1, $expr2);
    }

    public function ifIsLowerThatOrEqual(string|int|float $value, ColumnBase|string|int|float $expr1, ColumnBase|string|int|float $expr2): static
    {
        return $this->if($value, '<=', $expr1, $expr2);
    }

    public function ifIsEqual(string|int|float $value, ColumnBase|string|int|float $expr1, ColumnBase|string|int|float $expr2): static
    {
        return $this->if($value, '=', $expr1, $expr2);
    }

    public function ifIsNotEqual(string|int|float $value, ColumnBase|string|int|float $expr1, ColumnBase|string|int|float $expr2): static
    {
        return $this->if($value, '!=', $expr1, $expr2);
    }

    public function case(array $conditions, ?string $else = null): static
    {
        $cases = "CASE " . $this->getColumnReference() . " ";

        foreach ($conditions as $when => $then) {
            if($then instanceof ColumnBase) {
                $then = $then->getColumnOutput();
            }
            $cases .= "WHEN $when THEN $then ";
        }

        $this->output = new Raw($cases . ($else? "ELSE $else ": '') . 'END');

        return $this;
    }

    public function __toString(): string
    {
        $output = $this->getOutput();

        if($output instanceof Raw) {
            return $output->value;
        }

        return $output;
    }
}
