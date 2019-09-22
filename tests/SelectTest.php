<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Tests;

use PHPUnit\Framework\TestCase;
use \Jarzon\QueryBuilder\Tests\Mocks\PdoMock;
use Jarzon\QueryBuilder\Builder as QB;
use Jarzon\QueryBuilder\Tests\Mocks\EntityMock;

class SelectTest extends TestCase
{
    public function testSimpleSelect()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');

        $query = QB::select($users)
            ->where($users->date, '<', 30);

        $this->assertEquals('SELECT * FROM users U WHERE U.date < :date', $query->getSql());
    }

    public function testAddSelect()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');

        $query = QB::select($users)
            ->columns($users->id, $users->name)
            ->where($users->date, '<', 30)
            ->addColumns($users->date->alias('date2'));

        $this->assertEquals('SELECT U.id, U.name, U.date AS date2 FROM users U WHERE U.date < :date', $query->getSql());
    }

    public function testWhereColumn()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');

        $query = QB::select($users)
            ->whereRaw($users->date, '=', $users->created);

        $this->assertEquals('SELECT * FROM users U WHERE U.date = U.created', $query->getSql());
    }

    public function testComplexWhere()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');

        $query = QB::select($users)
            ->whereRaw($users->date, '>', '(U.anotherColumn - 5)');


        $this->assertEquals('SELECT * FROM users U WHERE U.date > (U.anotherColumn - 5)', $query->getSql());
    }

    public function testWithAlias()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock();

        $query = QB::select($users)
            ->columns($users->id, $users->name->alias('username'));

        $this->assertEquals('SELECT id, name AS username FROM users', $query->getSql());
    }

    public function testTableAlias()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');

        $query = QB::select($users)
            ->columns($users->id, $users->name->alias('username'))
            ->where($users->date, '<', 30)
            ->where($users->name, '!=', 'Root');

        $this->assertEquals("SELECT U.id, U.name AS username FROM users U WHERE U.date < :date AND U.name != :name", $query->getSql());
    }

    public function testAndCondition()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');

        $query = QB::select($users)
            ->where($users->date, '<', 30)
            ->where($users->name, '!=', 'Root');

        $this->assertEquals("SELECT * FROM users U WHERE U.date < :date AND U.name != :name", $query->getSql());
    }

    public function testOrCondition()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');

        $query = QB::select($users)
            ->where($users->date, '<', 30)
            ->or($users->name, '!=', 'Root');

        $this->assertEquals("SELECT * FROM users U WHERE U.date < :date OR U.name != :name", $query->getSql());
    }

    public function testSubConditions()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');

        $query = QB::select($users)
            ->where(function ($q) use($users) {
                $q->whereRaw($users->name, '!=', 'Root')
                    ->or($users->date, '<', '01-01-2000');
            })
            ->or(function ($q) use($users) {
                $q->where($users->name, '!=', 'Root')
                    ->or($users->date, '<', '01-01-2000');
            });

        $this->assertEquals("SELECT * FROM users U WHERE ( U.name != Root OR U.date < :date ) OR ( U.name != :name OR U.date < :date2 )", $query->getSql());
    }

    public function testBetweenCondition()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');

        $query = QB::select($users)
            ->between($users->number, 10, 30);

        $this->assertEquals('SELECT * FROM users U WHERE U.number BETWEEN :number AND :number2', $query->getSql());
    }

    public function testNotBetweenCondition()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');

        $query = QB::select($users)
            ->notBetween($users->number, 10, 30);

        $this->assertEquals('SELECT * FROM users U WHERE U.number NOT BETWEEN :number AND :number2', $query->getSql());
    }

    public function testInCondition()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');

        $query = QB::select($users)
            ->in($users->name, ['admin', 'mod']);

        $this->assertEquals("SELECT * FROM users U WHERE U.name IN ('admin', 'mod')", $query->getSql());
    }

    public function testNotInCondition()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');

        $query = QB::select($users)
            ->notIn($users->name, ['admin', 'mod']);

        $this->assertEquals("SELECT * FROM users U WHERE U.name NOT IN ('admin', 'mod')", $query->getSql());
    }

    public function testIsNullCondition()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');

        $query = QB::select($users)
            ->isNull($users->name);

        $this->assertEquals('SELECT * FROM users U WHERE U.name IS NULL', $query->getSql());
    }

    public function testIsNotNullCondition()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');

        $query = QB::select($users)
            ->isNotNull($users->name);

        $this->assertEquals('SELECT * FROM users U WHERE U.name IS NOT NULL', $query->getSql());
    }

    public function testOrderBy()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');

        $query = QB::select($users)
            ->orderBy($users->id);

        $this->assertEquals('SELECT * FROM users U ORDER BY U.id', $query->getSql());
    }

    public function testOrderByDesc()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');

        $query = QB::select($users)
            ->orderBy($users->id, 'desc');

        $this->assertEquals('SELECT * FROM users U ORDER BY U.id DESC', $query->getSql());
    }

    public function testGroupBy()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');

        $query = QB::select($users)
            ->groupBy($users->id);

        $this->assertEquals('SELECT * FROM users U GROUP BY U.id', $query->getSql());
    }

    public function testLimit()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');

        $query = QB::select($users)
            ->limit(10);

        $this->assertEquals('SELECT * FROM users U LIMIT :limit1', $query->getSql());
    }

    public function testLimitOffset()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');

        $query = QB::select($users)
            ->limit(10, 20);

        $this->assertEquals('SELECT * FROM users U LIMIT :limit1, :limit2', $query->getSql());
    }

    public function testMutlipleQueriesParam()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');

        $query2 = QB::select($users)
            ->where($users->id, '<', 30);

        $users = new EntityMock('U');

        $query = QB::select($users)
            ->where($users->id, '<', 30);

        $this->assertEquals($query2, $query);
    }

    public function testMutlipleQueriesColumn()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');

        $query2 = QB::select($users)
            ->columns($users->id->count()->alias('number'));

        $users = new EntityMock('U');

        $query = QB::select($users)
            ->columns($users->id);

        $this->assertNotEquals($query2, $query);
    }

    public function testMutlipleQueriesAlias()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');

        $query2 = QB::select($users)
            ->columns($users->id->alias('number'));

        $users = new EntityMock('U');

        $query = QB::select($users)
            ->columns($users->id);

        $this->assertNotEquals($query2, $query);
    }

    public function testFetch()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');

        $query = QB::select($users);

        $this->assertEquals([], $query->fetchAll());
    }
}