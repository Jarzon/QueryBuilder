<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Jarzon\QueryBuilder;

class UpdateTest extends TestCase
{
    public function testSql()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->update(['name' => 'test', 'email' => 'test@exemple.com']);

        $this->assertEquals("UPDATE users SET name = ?, email = ?", $query->getSql());
    }

    public function testExec()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->update(['name' => 'test', 'email' => 'test@exemple.com']);

        $query->exec();

        $this->assertEquals([0 => 'test', 1 => 'test@exemple.com'], $query->getLastStatement()->params);
    }

    public function testWhere()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->update(['name' => 'test', 'email' => 'test@exemple.com'])
            ->where('id', '=', 1);

        $query->exec();

        $this->assertEquals("UPDATE users SET name = ?, email = ? WHERE id = ?", $query->getSql());

        $this->assertEquals([0 => 'test', 1 => 'test@exemple.com', 2 => 1], $query->getLastStatement()->params);
    }
}