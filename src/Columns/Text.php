<?php
namespace Jarzon\Columns;

class Text extends TableColumn
{
    public $name = '';

    public function length(): self
    {
        $this->output = "CHAR_LENGTH({$this->getOutput()})";

        return $this;
    }
}