<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Columns;

class Date extends ColumnBase
{
    public function date()
    {
        $this->output = "DATE({$this->getOutput()})";

        return $this;
    }

    public function dateAdd(string $intervalAddition)
    {
        $this->output .= "DATE_ADD({$this->getOutput()}, INTERVAL $intervalAddition)";

        return $this;
    }
}