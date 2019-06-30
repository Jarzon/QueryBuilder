<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Tests\Mocks;

use Jarzon\QueryBuilder\Entity\EntityBase;

class EntityMock extends EntityBase
{
    public $id;
    public $name;
    public $email;
    public $date;
    public $number;
    public $created;

    function build()
    {
        $this->table('users');

        $this->id = $this->number('id');
        $this->name = $this->text('name');
        $this->email = $this->text('email');
        $this->date = $this->date('date');
        $this->number = $this->number('number');
        $this->created = $this->date('created');
    }
}