<?php
namespace Jarzon;

class Join
{
    protected $type = '';
    protected $table = '';
    protected $firstColumn;
    protected $operator;
    protected $secondColumn;

    public function __construct(string $type, $table, $firstColumn, $operator, $secondColumn)
    {
        $this->type = $type;

        $this->table = $table;
        $this->firstColumn = $firstColumn;
        $this->operator = $operator;
        $this->secondColumn = $secondColumn;

        return $this;
    }

    public function getSql()
    {
        return "$this->type JOIN $this->table ON $this->firstColumn $this->operator $this->secondColumn";
    }
}