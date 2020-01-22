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
        return $this->value;
    }
}
