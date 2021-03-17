<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Columns;

use Jarzon\QueryBuilder\Raw;

class Date extends ColumnBase
{
    public function date(string $format = null): Date
    {
        $function = 'DATE';
        $args = '';

        if($format !== null) {
            $function .= '_FORMAT';
            $args = ", '$format'";
        }

        $this->output = new Raw("$function({$this->getOutput()}$args)");

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
