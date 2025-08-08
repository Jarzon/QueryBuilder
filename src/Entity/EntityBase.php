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
    public Date $currentDate;

    public function __construct(
        protected string $alias = '',
        public string $entityClass = ''
    ) {
        $this->currentDate = $this->date('CURRENT_DATE');
    }

    public function resetParamCount():void
    {
        /** @phpstan-ignore-next-line */
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

    public function columnIsNotEmpty(string $name, string|int|bool $value): bool
    {
        if ($this->{$name} instanceof Text) {
            return $value !== '';
        } else {
            return !empty($value);
        }
    }

    protected function getAlias(): string
    {
        return $this->alias ?? $this->table;
    }

    protected function table(string $name): EntityBase
    {
        $this->table = $name;

        return $this;
    }

    protected function text(string $name): Text
    {
        return new Text($name, $this->getAlias());
    }

    protected function number(string $name): Numeric
    {
        return new Numeric($name, $this->getAlias());
    }

    protected function date(string $name): Date
    {
        return new Date($name, $this->getAlias());
    }
}
