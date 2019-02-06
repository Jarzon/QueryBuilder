<?php
namespace Tests\Mocks;

use Jarzon\Columns\Date;
use Jarzon\Columns\Number;
use Jarzon\Columns\Text;
use Jarzon\TableBase;

class TestTableMock extends TableBase
{
    /** @var Number $id */
    public $id;
    /** @var $text Text */
    public $text;
    /** @var $user_id Number */
    public $user_id;
    /** @var $created Date */
    public $created;

    public function __construct($alias = '')
    {
        parent::__construct($alias);

        $this
            ->table('test')

            ->number('id')
            ->text('text')
            ->number('user_id')
            ->date('created');
    }
}