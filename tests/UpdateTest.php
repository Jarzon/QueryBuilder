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

        $this->assertEquals("UPDATE users SET users.name = :name, users.email = :email", $query->getSql());
    }

    public function testExec()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->update(['name' => 'test', 'email' => 'test@exemple.com']);

        $query->exec();

        $this->assertEquals([':name' => 'test', ':email' => 'test@exemple.com'], $query->getLastStatement()->params);
    }

    public function testWhere()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->update(['name' => 'test', 'email' => 'test@exemple.com'])
            ->where('id', '=', 1);

        $query->exec();

        $this->assertEquals("UPDATE users SET users.name = :name, users.email = :email WHERE users.id = :id", $query->getSql());

        $this->assertEquals([':name' => 'test', ':email' => 'test@exemple.com', ':id' => 1], $query->getLastStatement()->params);
    }
}