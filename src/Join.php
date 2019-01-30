<?php
namespace Jarzon;

class Join extends ConditionsQueryBase
{
    public function __construct(string $type, $table, $firstColumnOrCallback, $operator = null, $secondColumn = null)
    {
        // TODO: Add join table alias support
        $this->type = "$type JOIN";

        $this->setTable($table, null);

        if($operator !== null) {
            $this->addCondition(new Condition($firstColumnOrCallback, $operator, $secondColumn));
        } else {
            $firstColumnOrCallback($this);
        }

        return $this;
    }

    public function getSql()
    {
        return "$this->type $this->table ON ".$this->getConditions();
    }
}