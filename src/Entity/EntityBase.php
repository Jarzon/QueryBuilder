<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Entity;

use Jarzon\QueryBuilder\Columns\Text;
use Jarzon\QueryBuilder\Columns\Numeric;
use Jarzon\QueryBuilder\Columns\Date;

abstract class EntityBase
{
    protected $table = '';
    protected $alias = '';

    public function __construct($alias = '')
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
        return new Numeric($name, $this->getAlias());
    }

    protected function date($name)
    {
        return new Date($name, $this->getAlias());
    }
}