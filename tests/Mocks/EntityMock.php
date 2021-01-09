<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Tests\Mocks;

use Jarzon\QueryBuilder\Columns\Date;
use Jarzon\QueryBuilder\Columns\Numeric;
use Jarzon\QueryBuilder\Columns\Text;
use Jarzon\QueryBuilder\Entity\EntityBase;

class EntityMock extends EntityBase
{
    public Numeric $id;
    public Text $name;
    public Text $email;
    public Date $date;
    public Numeric $number;
    public Date $created;

    public function __construct($alias = '')
    {
        parent::__construct($alias);

        $this->table('users');

        $this->id = $this->number('id');
        $this->name = $this->text('name');
        $this->email = $this->text('email');
        $this->date = $this->date('date');
        $this->number = $this->number('number');
        $this->created = $this->date('created');
    }
}
