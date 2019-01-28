<?php
namespace Jarzon;

class QueryBase
{
    protected $pdo;
    protected $lastStatement;

    protected $params = [];

    protected $type = '';
    protected $table = '';

    protected function setTable(string $table)
    {
        $this->table = $table;
    }

    protected function param($value, bool $raw = false)
    {
        if($raw) {
            return $value;
        }

        $this->params[] = $value;
        return '?';
    }
}