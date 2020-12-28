<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Entity;

use Jarzon\QueryBuilder\Columns\ColumnBase;
use Jarzon\QueryBuilder\Columns\Text;
use Jarzon\QueryBuilder\Columns\Numeric;
use Jarzon\QueryBuilder\Columns\Date;

abstract class EntityBase
{
    public string $table = '';
    protected string $alias = '';
    public string $entityClass = '';

    public function __construct($alias = '', $class = '')
    {
        $this->alias = $alias;
        $this->entityClass = $class;
    }

    public function resetParamCount() {
        foreach ($this as $key => $value) {
            if($this->$key instanceof ColumnBase) {
                $this->$key->paramCount = 1;
            }
        }
    }

    public function __toString(): string
    {
        return $this->table . ($this->alias != ''? " $this->alias": '');
    }

    public function columnExist(string $name): bool
    {
        return property_exists($this, $name);
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
