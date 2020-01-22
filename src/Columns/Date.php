<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Columns;

use Jarzon\QueryBuilder\Raw;

class Date extends ColumnBase
{
    public function date()
    {
        $this->output = new Raw("DATE({$this->getOutput()})");

        return $this;
    }

    public function dateAdd(string $intervalAddition)
    {
        $this->output .= new Raw("DATE_ADD({$this->getOutput()}, INTERVAL $intervalAddition)");

        return $this;
    }
}
