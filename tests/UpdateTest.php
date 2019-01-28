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
            ->update(['name' => 'test', 'email' => 'test@exemple.com']);

        $this->assertEquals("UPDATE users SET name = ?, email = ?", $query->getSql());
    }

    public function testWhere()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->update(['name' => 'test', 'email' => 'test@exemple.com'])
            ->where('id', '=', 1);

        $this->assertEquals("UPDATE users SET name = ?, email = ? WHERE id = ?", $query->getSql());
    }
}