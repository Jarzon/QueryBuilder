<?php
namespace Jarzon\QueryBuilder\Columns;

class ColumnBase
{
    public $tableAlias = null;
    public $name = '';
    public $alias = '';
    public $output = '';
    public $paramCount = 1;

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

    public function getOutput(): string
    {
        return !empty($this->output)? $this->output : $this->getColumnReference();
    }

    public function getColumnName(): string
    {
        return $this->name;
    }

    public function getColumnParamName(): string
    {
        $output = $this->name . ($this->paramCount > 1? $this->paramCount: '');
        $this->paramCount++;
        return $output;
    }

    public function getColumnReference(): string
    {
        return ($this->tableAlias != ''? "$this->tableAlias.": '') . $this->getColumnName();
    }

    public function getColumnSelect(): string
    {
        $output = $this->getOutput() . ($this->output !== '' || $this->alias !== ''? " AS ".(!empty($this->alias)? $this->alias: $this->name): '');

        $this->output = '';
        $this->alias = '';

        return $output;
    }

    public function __toString(): string
    {
        return $this->getOutput();
    }

    public function resetCounter()
    {
        $this->paramCount = 1;
    }
}