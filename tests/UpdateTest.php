<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Jarzon\QueryBuilder;

class UpdateTest extends TestCase
{
    public function testSimple()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->update(['name', 'email'])
            ->values(['test', 'test@exemple.com']);

        $this->assertEquals("UPDATE users SET name = ?, email = ?", $query->getSql());
    }
}