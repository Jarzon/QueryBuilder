<?php
namespace Jarzon\QueryBuilder\Columns;

class Number extends ColumnBase
{
    public function min(): self
    {
        $this->output = "MIN({$this->getOutput()})";

        return $this;
    }

    public function max(): self
    {
        $this->output = "MAX({$this->getOutput()})";

        return $this;
    }

    public function sum(): self
    {
        $this->output = "SUM({$this->getOutput()})";

        return $this;
    }

    public function avg(): self
    {
        $this->output = "AVG({$this->getOutput()})";

        return $this;
    }

    public function ceiling(): self
    {
        $this->output = "CEILING({$this->getOutput()})";

        return $this;
    }

    public function floor(): self
    {
        $this->output = "FLOOR({$this->getOutput()})";

        return $this;
    }

    public function count(): self
    {
        $this->output = "COUNT({$this->getOutput()})";

        return $this;
    }

    public function format(int $round = 2, string $local = 'fr_CA'): self
    {
        $this->output = "FORMAT({$this->getOutput()}, $round" . (($local !== '')? ", '$local'": '') . ')';

        return $this;
    }

    public function currency(string $value = null): self
    {
        if($value !== null) {
            $this->output = $value;
        }

        $this->format()->append("' $'");

        return $this;
    }
}