<?php
namespace Jarzon\Columns;

class Date extends TableColumn
{
    public function date(): self
    {
        $this->output = "DATE({$this->getOutput()})";

        return $this;
    }

    public function dateAdd(string $intervalAddition): self
    {
        $this->output .= "DATE_ADD({$this->getOutput()}, INTERVAL $intervalAddition)";

        return $this;
    }
}