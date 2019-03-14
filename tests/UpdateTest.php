<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Tests;

use PHPUnit\Framework\TestCase;
use \Jarzon\QueryBuilder\Tests\Mocks\PdoMock;
use Jarzon\QueryBuilder\Builder as QB;
use Jarzon\QueryBuilder\Tests\Mocks\EntityMock;

class UpdateTest extends TestCase
{
    public function testSql()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock();

        $query = QB::update($users)
            ->columns(['name' => 'test', 'email' => 'test@exemple.com']);

        $this->assertEquals("UPDATE users SET name = :name, email = :email", $query->getSql());
    }

    public function testAliasSql()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');

        $query = QB::update($users)
            ->columns(['name' => 'test', 'email' => 'test@exemple.com']);

        $this->assertEquals("UPDATE users U SET name = :name, email = :email", $query->getSql());
    }

    public function testSet()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock();

        $query = QB::update($users)
            ->set($users->name, 'test')
            ->set($users->email, 'test@exemple.com');

        $this->assertEquals("UPDATE users SET name = :name, email = :email", $query->getSql());
    }

    public function testSetRaw()
    {
        QB::setPDO(new PdoMock());

        $query = QB::update('users')
            ->columns(['name' => 'test', 'email' => 'test@exemple.com'])
            ->setRaw('updated', 'NOW()');

        $this->assertEquals("UPDATE users SET name = :name, email = :email, updated = NOW()", $query->getSql());
    }

    public function testExec()
    {
        QB::setPDO(new PdoMock());

        $query = QB::update('users')
            ->columns(['username' => 'test', 'mail' => 'test@exemple.com']);

        $query->exec();

        $this->assertEquals([':username' => 'test', ':mail' => 'test@exemple.com'], $query->getLastStatement()->params);
    }

    public function testWhere()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock();

        $query = QB::update('users')
            ->columns(['name' => 'test', 'email' => 'test@exemple.com'])
            ->where($users->id, '=', 1);

        $query->exec();

        $this->assertEquals("UPDATE users SET name = :name, email = :email WHERE id = :id", $query->getSql());

        $this->assertEquals([':name' => 'test', ':email' => 'test@exemple.com', ':id' => 1], $query->getLastStatement()->params);
    }
}