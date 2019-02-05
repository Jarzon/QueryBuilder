<?php
namespace Jarzon\Columns;

class TableColumn
{
    public $tableAlias = null;
    public $name = '';
    public $alias = '';
    public $output = '';

    public function __construct(string $name, $tableAlias = null)
    {
        $this->name = $name;
        $this->alias = $name;
        $this->tableAlias = $tableAlias;
    }

    public function alias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function preAppend(...$args): self
    {
        $args[] = $this->getOutput();

        $this->output = "CONCAT(" . implode(', ', $args) . ")";

        return $this;
    }

    public function append(...$args): self
    {
        array_unshift($args,  $this->getOutput());

        $this->output = "CONCAT(" . implode(', ', $args) . ")";

        return $this;
    }

    public function getOutput()
    {
        return !empty($this->output)? $this->output : $this->getColumnReference();
    }

    public function getColumnReference()
    {
        return ($this->tableAlias != ''? "$this->tableAlias.": '') . $this->name;
    }

    public function getColumnSelect()
    {
        return ($this->getOutput()) . " AS $this->alias";
    }

    public function __toString()
    {
        return $this->getOutput();
    }
}