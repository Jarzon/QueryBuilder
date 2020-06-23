<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Columns;

use Jarzon\QueryBuilder\Raw;

class ColumnBase implements ColumnInterface
{
    public ?string $tableAlias;
    public string $name = '';
    public ?string $alias = null;
    public $output = null;
    public int $paramCount = 1;

    public function __construct(string $name, $tableAlias = null)
    {
        $this->name = $name;
        $this->tableAlias = $tableAlias;
    }

    public function alias(string $alias)
    {
        $this->alias = $alias;

        $output = $this->getColumnSelect();

        $this->output = null;

        return $output;
    }

    /** @return string|Raw */
    public function getOutput()
    {
        return $this->output ?? new Raw($this->getColumnReference());
    }

    public function getColumnOutput()
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

    protected function parseArgs(array &$args)
    {
        foreach ($args as $i => $arg) {
            $args[$i] = $arg instanceof ColumnInterface? $arg->getOutput() :($arg instanceof Raw? $arg: "'$arg'");
        }

        return $args;
    }

    public function concat(array $args)
    {
        $args = $this->parseArgs($args);

        $this->output = new Raw("CONCAT(" . implode(', ', $args) . ")");
    }

    public function preAppend(...$args)
    {
        $args[] = $this->getOutput();

        $this->concat($args);

        return $this;
    }

    public function append(...$args)
    {
        array_unshift($args,  $this->getOutput());

        $this->concat($args);

        return $this;
    }

    public function ifIsNull($value)
    {
        $args[] = $this->getOutput();

        $args = $this->parseArgs($args);

        $this->output = new Raw("IFNULL(" .implode(', ', $args) . ", $value)");

        return $this;
    }

    public function if($value, $operator, $expr1, $expr2)
    {
        $this->output = new Raw("IF(" . $this->getColumnReference() . " $operator $value, $expr1, $expr2)");

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
        $output = $this->getOutput();

        if($output instanceof Raw) {
            return $output->value;
        }

        return $output;
    }
}
