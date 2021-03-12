<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Tests;

use PHPUnit\Framework\TestCase;
use \Jarzon\QueryBuilder\Tests\Mocks\PdoMock;
use \Jarzon\QueryBuilder\Builder as QB;
use \Jarzon\QueryBuilder\Tests\Mocks\EntityMock;

class DeleteTest extends TestCase
{
    public function testSimple()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock();

        $query = QB::delete($users)
            ->where($users->name, '=', 'test');

        $this->assertEquals("DELETE FROM users WHERE name = :name", $query->getSql());
    }
}
