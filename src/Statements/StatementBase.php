<?php declare(strict_types=1);
namespace Jarzon\QueryBuilder\Statements;

use Jarzon\QueryBuilder\Columns\ColumnInterface;
use Jarzon\QueryBuilder\Entity\EntityBase;

abstract class StatementBase
{
    protected object $pdo;
    protected object $lastStatement;

    public array $params = [];

    protected string $type = '';
    protected string|EntityBase $table = '';
    protected ?string $tableAlias;

    protected function param($value, $key = '?', bool $raw = false)
    {
        if($value instanceof ColumnInterface) {
            return $value->getColumnOutput();
        }
        else if($raw) {
            return $value;
        }

        if($key instanceof ColumnInterface) {
            $key =  ":" . $key->getColumnParamName();

            $this->params[$key] = $value;
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
        return $this->table . (isset($this->tableAlias)? " $this->tableAlias" : '');
    }

    public function getLastStatement(): ?object
    {
        return $this->lastStatement;
    }
}
