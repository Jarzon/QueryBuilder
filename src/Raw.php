<?php
namespace Jarzon\QueryBuilder;

class Raw
{
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

        return $this->value;
    }
}