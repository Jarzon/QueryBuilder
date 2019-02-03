<?php
namespace Jarzon;

abstract class QueryBase
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

            if(array_key_exists($key, $this->params)) {
                $keys = array_keys(array_keys($this->params), $key);

                $key .= count($keys);
            }

            $this->params[$key] = $value;
        } else {
            $this->params[] = $value;
        }

        return $key;
    }

    protected function getTable()
    {
        return "$this->table".(isset($this->tableAlias)? " $this->tableAlias": '');
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