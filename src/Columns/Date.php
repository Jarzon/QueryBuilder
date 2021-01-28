<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Columns;

use Jarzon\QueryBuilder\Raw;

class Date extends ColumnBase
{
    public function date(): Date
    {
        $this->output = new Raw("DATE({$this->getOutput()})");

        return $this;
    }

    public function dateAdd(string $intervalAddition): Date
    {
        $this->output = new Raw("{$this->getOutput()} + INTERVAL $intervalAddition");

        return $this;
    }

    public function dateSub(string $intervalAddition): Date
    {
        $this->output = new Raw("{$this->getOutput()} - INTERVAL $intervalAddition");

        return $this;
    }

    public function dateDiff(string|Date $column): Date
    {
        if($column instanceof Date) {
            $column = $column->getOutput();
        }

        $this->output = new Raw("DATEDIFF({$this->getOutput()}, $column)");

        return $this;
    }
}
