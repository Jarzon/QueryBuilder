<?php
namespace Jarzon;

use Jarzon\Columns\Text;
use Jarzon\Columns\Number;
use Jarzon\Columns\Date;

abstract class TableBase
{
    protected $table = '';
    protected $alias = null;

    public function __construct($alias = null)
    {
        $this->alias = $alias;
    }

    public function __toString(): string
    {
        return $this->table . ($this->alias != ''? " $this->alias": '');
    }

    protected function getAlias()
    {
        return $this->alias ?? $this->table;
    }

    protected function table($name): self
    {
        $this->table = $name;

        return $this;
    }

    protected function text($name): self
    {
        $this->$name = new Text($name, $this->getAlias());

        return $this;
    }

    protected function number($name): self
    {
        $this->$name = new Number($name, $this->getAlias());

        return $this;
    }

    protected function date($name): self
    {
        $this->$name = new Date($name, $this->getAlias());

        return $this;
    }
}