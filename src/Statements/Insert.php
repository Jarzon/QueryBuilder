<?php
namespace Jarzon\Statements;

class Insert extends StatementBase
{
    protected $columns = [];
    protected $values = [];

    public function __construct(string $table, object $pdo)
    {
        $this->type = 'INSERT INTO';
        $this->pdo = $pdo;

        $this->setTable($table, null);
    }

    public function values(array $values)
    {
        $this->values = $values;

        return $this;
    }

    public function columns(... $columns): self
    {
        $this->columns = [];
        $this->addColumn(...$columns);

        return $this;
    }

    public function addColumn(...$columns): self
    {
        if(is_array($columns)) {
            array_map(function ($key, $column) {
                if(!is_int($key)) {
                    $this->columns[$key] = $column;
                } else {
                    $this->columns[] = $column;
                }

            }, array_keys($columns), $columns);
        } else {
            $this->columns[] = $columns;
        }

        return $this;
    }

    public function getSql(): string
    {
        $columns = implode(', ', $this->columns);
        $values = ':'.implode(', :', $this->columns);

        $query = "$this->type {$this->table}($columns) VALUES ($values)";

        return $query;
    }

    public function exec(...$params)
    {
        $this->lastStatement = $query = $this->pdo->prepare($this->getSql());

        if(count($params) === 0) {
            $params = $this->params;
        }

        $query->execute($params);

        return $query->lastInsertId();
    }
}