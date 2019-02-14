<?php
namespace Jarzon\QueryBuilder\Tests\Mocks;

use Jarzon\QueryBuilder\TableBase;

class TestTableMock extends TableBase
{
    public $id;
    public $text;
    public $user_id;
    public $created;

    public function __construct($alias = '')
    {
        parent::__construct($alias);

        $this->table('test');

        $this->id = $this->number('id');
        $this->text = $this->text('text');
        $this->user_id = $this->number('user_id');
        $this->created = $this->date('created');
    }
}