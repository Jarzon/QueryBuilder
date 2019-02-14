<?php
namespace Jarzon\QueryBuilder\Tests\Mocks;

use Jarzon\QueryBuilder\TableBase;

class TableMock extends TableBase
{
    public $id;
    public $name;
    public $email;
    public $date;
    public $number;
    public $created;

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