<?php
namespace Tests\Mocks;

use Jarzon\Columns\Date;
use Jarzon\Columns\Number;
use Jarzon\Columns\Text;
use Jarzon\TableBase;

class TableMock extends TableBase
{
    public $table = '';

    /** @var Number $id */
    public $id;
    /** @var $name Text */
    public $name;
    /** @var $date Date */
    public $date;
    /** @var $number Number */
    public $number;
    /** @var $created Date */
    public $created;

    public function __construct($alias = '')
    {
        parent::__construct($alias);

        $this
            ->table('users')
            ->number('id')
            ->text('name')
            ->date('date')
            ->number('number')
            ->date('created');
    }
}