<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Statements;

use \Jarzon\QueryBuilder\Conditions\Condition;

class Join extends ConditionalStatementBase
{
    public function __construct(string $type, $table, $firstColumnOrCallback, $operator = null, $secondColumn = null)
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