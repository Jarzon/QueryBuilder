<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Jarzon\QueryBuilder as QB;

class DeleteTest extends TestCase
{
    public function testSimple()
    {
        QB::setPDO(new PdoMock());

        $query = QB::delete('users')
            ->where('name', '=', 'test');

        $this->assertEquals("DELETE users WHERE users.name = :name", $query->getSql());
    }
}