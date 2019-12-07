<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder;

class Raw
{
    /** @var string|int|float */
    public $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        if(is_string($this->value)) {
            return "'$this->value'";
        }
        else if(is_int($this->value) || is_float($this->value)) {
            return "$this->value";
        }

        return (string)$this->value;
    }
}
