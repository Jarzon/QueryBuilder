<?php
namespace Jarzon;

class QueryBase
{
    protected $pdo;
    protected $lastStatement;

    protected $params = [];

    protected $type = '';
    protected $table = '';
    protected $tableAlias = null;

    protected function setTable(string $table, ?string $tableAlias)
    {
        $this->table = $table;
        $this->tableAlias = $tableAlias;
    }

    protected function param($value, $key = '?', bool $raw = false)
    {
        if($raw) {
            return $value;
        }

        if(is_string($key) && $key !== '?') {
            $key = ":$key";
            $this->params[$key] = $value;
        } else {
            $this->params[] = $value;
        }

        return $key;
    }

    public function columnAlias($column, $isRaw = false)
    {
        if($isRaw) {
            return $column;
        }

        return ($this->tableAlias === null? $this->table: $this->tableAlias) . ".$column";
    }

    public function getLastStatement()
    {
        return $this->lastStatement;
    }
}