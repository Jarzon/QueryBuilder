<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Columns;

use Jarzon\QueryBuilder\Raw;

class Numeric extends ColumnBase
{
    public function min()
    {
        $this->output = new Raw("MIN({$this->getOutput()})");

        return $this;
    }

    public function max()
    {
        $this->output = new Raw("MAX({$this->getOutput()})");

        return $this;
    }

    public function sum()
    {
        $this->output = new Raw("SUM({$this->getOutput()})");

        return $this;
    }

    public function avg()
    {
        $this->output = new Raw("AVG({$this->getOutput()})");

        return $this;
    }

    public function round(int $precision = 2)
    {
        $this->output = new Raw("ROUND({$this->getOutput()}, $precision)");

        return $this;
    }

    public function ceiling()
    {
        $this->output = new Raw("CEILING({$this->getOutput()})");

        return $this;
    }

    public function floor()
    {
        $this->output = new Raw("FLOOR({$this->getOutput()})");

        return $this;
    }

    public function count()
    {
        $this->output = new Raw("COUNT({$this->getOutput()})");

        return $this;
    }

    public function format(int $round = 2, string $local = 'sv_SE')
    {
        $this->output = new Raw("FORMAT({$this->getOutput()}, $round" . (($local !== '')? ", '$local'": '') . ')');

        return $this;
    }

    public function currency(string $value = null)
    {
        if($value !== null) {
            $this->output = $value;
        }

        $this->format()->append(" $");

        return $this;
    }
}