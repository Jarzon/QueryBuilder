<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Columns;

class Text extends ColumnBase
{
    public string $name = '';

    public function length()
    {
        $this->output = "CHAR_LENGTH({$this->getOutput()})";

        return $this;
    }

    public function substring(int $start, int $end)
    {
        $this->output = "SUBSTRING({$this->getOutput()}, $start, $end)";

        return $this;
    }

    public function limit(int $numberOfChars)
    {
        $this->substring(1, $numberOfChars);

        return $this;
    }
}
