<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Tests\Mocks;

use Jarzon\QueryBuilder\Entity\EntityBase;

class TestEntityMock extends EntityBase
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
