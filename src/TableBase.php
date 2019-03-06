<?php
namespace Jarzon\QueryBuilder;

use Jarzon\QueryBuilder\Columns\Text;
use Jarzon\QueryBuilder\Columns\Number;
use Jarzon\QueryBuilder\Columns\Date;

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

    protected function table($name)
    {
        $this->table = $name;

        return $this;
    }

    protected function text($name)
    {
        return new Text($name, $this->getAlias());
    }

    protected function number($name)
    {
        return new Number($name, $this->getAlias());
    }

    protected function date($name)
    {
        return new Date($name, $this->getAlias());
    }
}