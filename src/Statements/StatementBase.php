<?php
namespace Jarzon\Statements;

abstract class StatementBase
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

        if(is_object($key)) {
            $key =  ":" . $key->getColumnReference();

            if(array_key_exists($key, $this->params)) {
                $keys = array_keys(array_keys($this->params), $key);

                $key .= count($keys);
            }

            $this->params[] = $value;
        }
        else if(is_string($key) && $key !== '?') {
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
        return $this->table;
    }

    public function getLastStatement()
    {
        return $this->lastStatement;
    }
}