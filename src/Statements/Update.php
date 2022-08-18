<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Statements;

use Jarzon\QueryBuilder\Columns\ColumnInterface;
use Jarzon\QueryBuilder\Entity\EntityBase;
use Jarzon\QueryBuilder\Raw;

class Update extends ConditionalStatementBase
{
    protected array $columns = [];
    protected ?string $query = null;

    public function __construct(string|EntityBase $table, ?string $tableAlias, object $pdo)
    {
        $this->type = 'UPDATE';
        $this->pdo = $pdo;

        $this->table = $table;
        $this->tableAlias = $tableAlias;

        if($this->table instanceof EntityBase) {
            $this->table->resetParamCount();
        }
    }

    public function set(string|ColumnInterface|Raw $column, int|float|string $value): Update
    {
        if($column instanceof ColumnInterface) {
            $column = $column->getColumnParamName();
        }
        elseif ($column instanceof Raw) {
            $column = $column->value;
        }

        $this->addColumn([$column => $value]);

        return $this;
    }

    public function setRaw(string|ColumnInterface|Raw $column, int|float|string|Select $valueOrSubQuery): Update
    {
        if($column instanceof ColumnInterface) {
            $column = $column->getColumnParamName();
        }
        elseif ($column instanceof Raw) {
            $column = $column->value;
        }

        if($valueOrSubQuery instanceof Select) {
            $valueOrSubQuery = "({$valueOrSubQuery->getSql()})";
        }

        $this->addColumn([$column => $valueOrSubQuery], true);

        return $this;
    }

    public function columns(array $columns): Update
    {
        if(!is_array($columns)) {
            $columns = [$columns];
        }

        $this->columns = [];
        $this->addColumn($columns);

        return $this;
    }

    public function addColumn(array $columns, bool $isRaw = false): Update
    {
        if(!$isRaw) {
            $columns = array_filter($columns, function($column) {
                return !$this->table instanceof EntityBase || ($this->table instanceof EntityBase && $this->table->columnExist($column));
            }, ARRAY_FILTER_USE_KEY);

            array_walk($columns, function(&$value, $column) {
                $value = $this->param($value, $column);
            });
        }

        $this->columns += $columns;

        return $this;
    }

    public function getSql(): string
    {
        if($this->query !== null) {
            return $this->query;
        }

        $table = $this->getTable();

        $columns = implode(', ', array_map(function($column, $param) {
            return "$column = $param";
        }, array_keys($this->columns), $this->columns));

        $query = "$this->type $table SET $columns";

        if($conditions = $this->getConditions()) {
            $query .= " WHERE $conditions";
        }

        $this->query = $query;
        return $query;
    }

    public function exec(mixed ...$params): int
    {
        $this->lastStatement = $this->pdo->prepare($this->getSql());

        if(count($params) === 0) {
            $params = $this->params;
        }

        $this->lastStatement->execute($params);

        return $this->lastStatement->rowCount();
    }
}
