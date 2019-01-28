<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Jarzon\QueryBuilder;

class InsertTest extends TestCase
{
    public function testSimple()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->insert(['name', 'email'])
            ->values(['test', 'test@exemple.com']);

        $this->assertEquals("INSERT INTO users(name, email) VALUES (?, ?)", $query->getSql());
    }
}