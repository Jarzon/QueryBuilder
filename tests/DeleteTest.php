<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Jarzon\QueryBuilder;

class DeleteTest extends TestCase
{
    public function testSimple()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->delete()
            ->where('name', '=', 'test');

        $this->assertEquals("DELETE users WHERE name = ?", $query->getSql());
    }
}