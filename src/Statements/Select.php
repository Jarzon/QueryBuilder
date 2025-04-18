<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Statements;

use Jarzon\QueryBuilder\Columns\ColumnInterface;
use Jarzon\QueryBuilder\Entity\EntityBase;
use Jarzon\QueryBuilder\Raw;

class Select extends ConditionalStatementBase
{
    protected array $columns = ['*'];
    protected array $join = [];
    protected array $orderBy = [];

    protected array $groupBy = [];
    protected array $limit = [];
    protected bool $groupByRollup = false;

    public function __construct(string|EntityBase $table, ?string $tableAlias, object $pdo)
    {
        $this->type = 'SELECT';
        $this->pdo = $pdo;

        $this->table = $table;
        $this->tableAlias = $tableAlias;

        if($this->table instanceof EntityBase) {
            $this->table->resetParamCount();
        }
    }

    public function getSql(): string
    {
        $columns = $this->getColumns();

        $table = $this->getTable();

        $query = "$this->type $columns FROM $table";

        if(count($this->join) > 0) {
            $joins = implode(' ', array_map(function($join) {
                return $join->getSql();
            }, $this->join));

            $query .= " $joins";
        }

        if($conditions = $this->getConditions()) {
            $query .= " WHERE $conditions";
        }

        if(count($this->groupBy) > 0) {
            $groupBy = implode(', ', $this->groupBy);

            $query .= " GROUP BY $groupBy";

            if($this->groupByRollup) {
                $query .= " WITH ROLLUP";
            }
        }

        if(count($this->orderBy) > 0) {
            $orderBy = implode(', ', array_map(function ($entry) {
                if ($entry[1] == null) {
                    return $entry[0];
                }

                return implode(' ', $entry);
            }, $this->orderBy));

            $query .= " ORDER BY $orderBy";
        }

        if(count($this->limit) > 0) {
            $limit = implode(', ', $this->limit);

            $query .= " LIMIT $limit";
        }

        return $query;
    }

    public function columns(ColumnInterface|Raw|array|string|int ...$columns): Select
    {
        $this->columns = [];
        $this->addColumns(...$columns);

        return $this;
    }

    protected function getColumns(): string
    {
        return implode(', ', array_map(function($key, $name) {
            if($name === '*') return $name;

            $output = $name;

            if($name instanceof ColumnInterface) {
                $output = $name->getOutput();
            }
            else if(is_array($name)) {
                $key = array_key_first($name);
                return "$key AS $name[$key]";
            }
            else if(!is_int($key)) {
                $output = $name;
            }

            return $output;
        }, array_keys($this->columns), $this->columns));
    }

    public function addColumns(ColumnInterface|Raw|array|string|int ...$columns): Select
    {
        foreach ($columns as $column) {

            if($column instanceof ColumnInterface) {
                $this->columns[] = $column->getColumnSelect();
            }
            elseif ($column instanceof Raw) {
                $this->columns[] = $column->value;
            } else {
                $this->columns[] = $column;
            }
        }

        return $this;
    }

    public function orderBy(ColumnInterface|Raw|string $column, string $order = ''): Select
    {
        if($column instanceof ColumnInterface) {
            $column = $column->getColumnReference();
        }
        elseif ($column instanceof Raw) {
            $column = $column->value;
        }

        $this->orderBy[] = [$column, strtoupper($order)];

        return $this;
    }

    public function groupBy(ColumnInterface|string|array $columns, bool $rollUp = false): Select
    {
        if(!is_array($columns)) {
            $columns = [$columns];
        }

        $this->groupBy = $columns;
        $this->groupByRollup = $rollUp;

        return $this;
    }

    public function limit(int $offset, int|null $select = null, bool $isRaw = false): Select
    {
        if($select === null) {
            $this->limit = [$this->param($offset, 'limit1', $isRaw)];
        } else {
            $this->limit = [$this->param($offset, 'limit1', $isRaw), $this->param($select, 'limit2', $isRaw)];
        }

        return $this;
    }

    public function leftJoin(string|EntityBase $table, ColumnInterface|Raw|string|callable $firstColumnOrCallback, string|null $operator = null, ColumnInterface|Raw|string|null $secondColumn = null): Select
    {
        $this->join[] = new Join('LEFT', $table, $firstColumnOrCallback, $operator, $secondColumn);

        return $this;
    }

    public function fetchAll(int $fetch_style = 0): array|false
    {
        $this->lastStatement = $query = $this->pdo->prepare($this->getSql());

        $query->execute($this->params);

        return $query->fetchAll($fetch_style);
    }

    public function fetch(int $fetch_style = 0): object|false
    {
        $this->lastStatement = $query = $this->pdo->prepare($this->getSql());

        $query->execute($this->params);

        return $query->fetch($fetch_style);
    }

    public function fetchArray(): array|false
    {
        $this->lastStatement = $query = $this->pdo->prepare($this->getSql());

        $query->execute($this->params);

        return $query->fetch(\PDO::FETCH_ASSOC);
    }

    public function fetchClass(string|null $class = null): object|false
    {
        $this->lastStatement = $query = $this->pdo->prepare($this->getSql());

        if($class === null && $this->table instanceof EntityBase) {
            $class = $this->table->entityClass;
        }

        $query->execute($this->params);

        return $query->fetchObject($class);
    }

    public function fetchClassAll(string|null $class = null, int $fetch_style = 0): array|false
    {
        $this->lastStatement = $query = $this->pdo->prepare($this->getSql());

        if($class === null && $this->table instanceof EntityBase) {
            $class = $this->table->entityClass;
        }

        $query->execute($this->params);

        return $query->fetchAll($fetch_style | \PDO::FETCH_CLASS, $class);
    }

    public function fetchColumn(): string|int|float|bool|null
    {
        $this->lastStatement = $query = $this->pdo->prepare($this->getSql());

        $query->execute($this->params);

        return $query->fetchColumn();
    }

    public function explain(): void
    {
        $sql = $this->getSql();

        echo "<h2>SQL</h2>";

        $esql = str_replace('SELECT', "<br><b>SELECT</b>", $sql);
        $esql = str_replace('FROM', "<br><b>FROM</b>", $esql);
        $esql = str_replace('WHERE', "<br><b>WHERE</b>", $esql);
        $esql = str_replace('LEFT JOIN', "<br><b>LEFT JOIN</b>", $esql);
        $esql = str_replace('ORDER BY', "<br><b>ORDER BY</b>", $esql);
        $esql = str_replace('LIMIT', "<br><b>LIMIT</b>", $esql);

        echo "$esql<br>";

        foreach ($this->params as $i => $v) {
            $sql = str_replace($i, $v . '', $sql);
        }

        $query = $this->pdo->query('EXPLAIN ANALYZE ' . $sql);
        $query->execute();
        echo "<h2>ANALYZE</h2>
        <div>";
        $lines = explode('->', $query->fetch()->EXPLAIN);
        foreach ($lines as $v) {
            $line = explode(': ', $v);
            if(count($line) <= 1) continue;
            echo "<b>$line[0]</b>: $line[1]<br>";
        }
        echo "</div><br><br>";

        $query = $this->pdo->query('EXPLAIN ' . $sql);
        $query->execute();
        echo "<h2>EXPLAIN</h2>
        <div>";
        foreach ($query->fetch() as $i => $v) {
            echo "<div>
                <b>$i</b>: <span>$v</span>
                </div>";
        }
        echo "</div>";
        exit;
    }
}
