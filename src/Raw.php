<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder;

class Raw
{
    public string|int|float $value;

    public function __construct(string|int|float $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
