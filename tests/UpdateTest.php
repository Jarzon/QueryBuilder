<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use \Tests\Mocks\PdoMock;
use Jarzon\QueryBuilder as QB;

class UpdateTest extends TestCase
{
    public function testSql()
    {
        QB::setPDO(new PdoMock());

        $query = QB::update('users')
            ->columns(['name' => 'test', 'email' => 'test@exemple.com']);

        $this->assertEquals("UPDATE users SET users.name = :name, users.email = :email", $query->getSql());
    }

    public function testSet()
    {
        QB::setPDO(new PdoMock());

        $query = QB::update('users')
            ->set('name', 'test')
            ->set('email', 'test@exemple.com');

        $this->assertEquals("UPDATE users SET users.name = :name, users.email = :email", $query->getSql());
    }

    public function testSetRaw()
    {
        QB::setPDO(new PdoMock());

        $query = QB::update('users')
            ->columns(['name' => 'test', 'email' => 'test@exemple.com'])
            ->setRaw('updated', 'NOW()');

        $this->assertEquals("UPDATE users SET users.name = :name, users.email = :email, users.updated = NOW()", $query->getSql());
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

        $query = QB::update('users')
            ->columns(['name' => 'test', 'email' => 'test@exemple.com'])
            ->where('id', '=', 1);

        $query->exec();

        $this->assertEquals("UPDATE users SET users.name = :name, users.email = :email WHERE users.id = :id", $query->getSql());

        $this->assertEquals([':name' => 'test', ':email' => 'test@exemple.com', ':id' => 1], $query->getLastStatement()->params);
    }
}