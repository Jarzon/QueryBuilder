<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Columns;

class ColumnBase implements ColumnInterface
{
    public ?string $tableAlias;
    public string $name = '';
    public ?string $alias = null;
    public ?string $output = null;
    public int $paramCount = 1;

    public function __construct(string $name, $tableAlias = null)
    {
        $this->name = $name;
        $this->tableAlias = $tableAlias;
    }

    public function alias(string $alias)
    {
        $this->alias = $alias;

        return $this;
    }

    public function getOutput(): string
    {
        return $this->output ?? $this->getColumnReference();
    }

    public function getColumnOutput(): string
    {
        $output = $this->output ?? $this->getColumnReference();

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

        if($this->output !== null || $this->alias !== null) {
            $output .= " AS " . ($this->alias ?? $this->name);
        }

        return $output;
    }

    /** Functions */

    public function preAppend(...$args)
    {
        $args[] = $this->getOutput();

        $this->output = "CONCAT(" . implode(', ', $args) . ")";

        return $this;
    }

    public function append(...$args)
    {
        array_unshift($args,  $this->getOutput());

        $this->output = "CONCAT(" . implode(', ', $args) . ")";

        return $this;
    }

    public function ifIsNull($value)
    {
        $args[] = $this->getOutput();

        $this->output = "IFNULL(" .implode(', ', $args) . ", $value)";

        return $this;
    }

    public function if($value, $operator, $expr1, $expr2)
    {
        $args[] = $this->getOutput();

        $this->output = "IF(" . $this->getColumnReference() . " $operator $value, $expr1, $expr2)";

        return $this;
    }

    public function ifIsGreaterThat($value, $expr1, $expr2)
    {
        return $this->if($value, '>', $expr1, $expr2);
    }

    public function ifIsLowerThat($value, $expr1, $expr2)
    {
        return $this->if($value, '<', $expr1, $expr2);
    }

    public function ifIsGreaterThatOrEqual($value, $expr1, $expr2)
    {
        return $this->if($value, '>=', $expr1, $expr2);
    }

    public function ifIsLowerThatOrEqual($value, $expr1, $expr2)
    {
        return $this->if($value, '<=', $expr1, $expr2);
    }

    public function ifIsEqual($value, $expr1, $expr2)
    {
        return $this->if($value, '=', $expr1, $expr2);
    }

    public function ifIsNotEqual($value, $expr1, $expr2)
    {
        return $this->if($value, '!=', $expr1, $expr2);
    }

    public function __toString(): string
    {
        return $this->getOutput();
    }
}
