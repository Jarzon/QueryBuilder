<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Columns;

class Text extends ColumnBase
{
    public $name = '';

    public function length()
    {
        $this->output = "CHAR_LENGTH({$this->getOutput()})";

        return $this;
    }
}
