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

    protected function param($value, $key = '?', bool $raw = false)
    {
        if($raw) {
            return $value;
        }

        if($key !== '?') {
            $key = ":$key";
            $this->params[$key] = $value;
        } else {
            $this->params[] = $value;
        }

        return $key;
    }

    public function getLastStatement()
    {
        return $this->lastStatement;
    }
}