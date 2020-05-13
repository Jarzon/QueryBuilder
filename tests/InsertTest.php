<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Tests;

use PHPUnit\Framework\TestCase;
use \Jarzon\QueryBuilder\Tests\Mocks\PdoMock;
use Jarzon\QueryBuilder\Builder as QB;
use Jarzon\QueryBuilder\Tests\Mocks\EntityMock;

class InsertTest extends TestCase
{
    public function testSimple()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock();

        $query = QB::insert($users)
            ->columns($users->name, $users->email)
            ->values(['test', 'test@exemple.com']);

        $this->assertEquals("INSERT INTO users(name, email) VALUES (:name, :email)", $query->getSql());
    }

    public function testAddColumn()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock();

        $query = QB::insert($users)
            ->columns(['name' => 'test', 'email' => 'test@exemple.com'])
            ->addColumn($users->date, '01/01/2019');

        $this->assertEquals("INSERT INTO users(name, email, date) VALUES (:name, :email, :date)", $query->getSql());
    }

    public function testNonExistingColumn()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock();

        $query = QB::insert($users)
            ->columns(['notAColumn' => 'test', 'name' => 'test', 'email' => 'test@exemple.com']);

        $this->assertEquals("INSERT INTO users(name, email) VALUES (:name, :email)", $query->getSql());
    }

    public function testNoAliasInInserts()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('A');

        $query = QB::insert($users)
            ->columns($users->name, $users->email)
            ->values(['test', 'test@exemple.com']);

        $this->assertEquals("INSERT INTO users(name, email) VALUES (:name, :email)", $query->getSql());
    }
}
