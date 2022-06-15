<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Columns;

use Jarzon\QueryBuilder\Raw;

class Text extends ColumnBase
{
    public string $name = '';

    public function length(): Text
    {
        $this->output = new Raw("CHAR_LENGTH({$this->getOutput()})");

        return $this;
    }

    public function substring(int $start, int $end): Text
    {
        $this->output = new Raw("SUBSTRING({$this->getOutput()}, $start, $end)");

        return $this;
    }

    public function limit(int $numberOfChars): Text
    {
        $this->substring(1, $numberOfChars);

        return $this;
    }

    public function replace(string $find, string $replace): Text
    {
        $this->output = new Raw("REPLACE({$this->getOutput()}, '$find', '$replace')");

        return $this;
    }
}
