<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use \Tests\Mocks\PdoMock;
use Jarzon\QueryBuilder as QB;
use \Tests\Mocks\TableMock;

class DeleteTest extends TestCase
{
    public function testSimple()
    {
        QB::setPDO(new PdoMock());

        $users = new TableMock();

        $query = QB::delete($users->table)
            ->where($users->name, '=', 'test');

        $this->assertEquals("DELETE users WHERE name = :name", $query->getSql());
    }
}