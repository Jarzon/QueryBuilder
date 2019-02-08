<?php
namespace Jarzon\QueryBuilder\Tests\Mocks;

use Jarzon\QueryBuilder\Columns\Date;
use Jarzon\QueryBuilder\Columns\Number;
use Jarzon\QueryBuilder\Columns\Text;
use Jarzon\QueryBuilder\TableBase;

class TableMock extends TableBase
{
    /** @var Number $id */
    public $id;
    /** @var $name Text */
    public $name;
    /** @var $email Text */
    public $email;
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
            ->text('email')
            ->date('date')
            ->number('number')
            ->date('created');
    }
}