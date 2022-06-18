<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Statements;

use Jarzon\QueryBuilder\Columns\ColumnInterface;
use Jarzon\QueryBuilder\Conditions\Condition;
use Jarzon\QueryBuilder\Entity\EntityBase;
use Jarzon\QueryBuilder\Raw;

class Join extends ConditionalStatementBase
{
    public function __construct(string $type, string|EntityBase $table, ColumnInterface|Raw|string|callable $firstColumnOrCallback, string|null $operator = null, ColumnInterface|Raw|string $secondColumn = null)
    {
        // TODO: Add join table alias support
        $this->type = "$type JOIN";

        $this->table = $table;

        if($operator !== null) {
            $this->addCondition(new Condition($firstColumnOrCallback, $operator, $secondColumn));
        } else {
            $firstColumnOrCallback($this);
        }
    }

    public function getSql(): string
    {
        return "$this->type $this->table ON ".$this->getConditions();
    }
}
