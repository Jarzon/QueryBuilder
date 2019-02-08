<?php
namespace Jarzon\QueryBuilder\Columns;

class Text extends ColumnBase
{
    public $name = '';

    public function length(): self
    {
        $this->output = "CHAR_LENGTH({$this->getOutput()})";

        return $this;
    }
}