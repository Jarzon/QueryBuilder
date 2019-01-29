<?php
namespace Jarzon;

class Join extends ConditionsQueryBase
{
    public function __construct(string $type, $table, $firstColumnOrCallback, $operator = null, $secondColumn = null)
    {
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