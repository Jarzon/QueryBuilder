<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Columns;

class Numeric extends ColumnBase
{
    public function min()
    {
        $this->output = "MIN({$this->getOutput()})";

        return $this;
    }

    public function max()
    {
        $this->output = "MAX({$this->getOutput()})";

        return $this;
    }

    public function sum()
    {
        $this->output = "SUM({$this->getOutput()})";

        return $this;
    }

    public function avg()
    {
        $this->output = "AVG({$this->getOutput()})";

        return $this;
    }

    public function round(int $precision = 2)
    {
        $this->output = "ROUND({$this->getOutput()}, $precision)";

        return $this;
    }

    public function ceiling()
    {
        $this->output = "CEILING({$this->getOutput()})";

        return $this;
    }

    public function floor()
    {
        $this->output = "FLOOR({$this->getOutput()})";

        return $this;
    }

    public function count()
    {
        $this->output = "COUNT({$this->getOutput()})";

        return $this;
    }

    public function format(int $round = 2, string $local = 'sv_SE')
    {
        $this->output = "FORMAT({$this->getOutput()}, $round" . (($local !== '')? ", '$local'": '') . ')';

        return $this;
    }

    public function currency(string $value = null)
    {
        if($value !== null) {
            $this->output = $value;
        }

        $this->format()->append("' $'");

        return $this;
    }
}
