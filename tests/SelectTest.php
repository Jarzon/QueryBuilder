<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Jarzon\QueryBuilder;

class SelectTest extends TestCase
{
    public function testSimpleSelect()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select()
            ->where('date', '<', 30);

        $this->assertEquals('SELECT * FROM users WHERE date < ?', $query->getSql());
    }

    public function testAddSelect()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name'])
            ->where('date', '<', 30)
            ->addSelect('date')
            ->addSelect(['company' => 'companyName']);

        $this->assertEquals('SELECT id, name, date, company AS companyName FROM users WHERE date < ?', $query->getSql());
    }

    public function testWhereColumn()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select()
            ->whereRaw('users.column', '=', 'users.anotherColumn');

        $this->assertEquals('SELECT * FROM users WHERE users.column = users.anotherColumn', $query->getSql());
    }

    public function testComplexWhere()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select()
            ->whereRaw('users.column', '>', '(users.anotherColumn - 5)');


        $this->assertEquals('SELECT * FROM users WHERE users.column > (users.anotherColumn - 5)', $query->getSql());
    }

    public function testWithAlias()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username']);

        $this->assertEquals('SELECT id, name AS username FROM users', $query->getSql());
    }

    public function testAndCondition()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->where('date', '<', 30)
            ->where('name', '!=', 'Root');

        $this->assertEquals("SELECT id, name AS username FROM users WHERE date < ? AND name != ?", $query->getSql());
    }

    public function testOrCondition()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->where('date', '<', 30)
            ->or('name', '!=', 'Root');

        $this->assertEquals("SELECT id, name AS username FROM users WHERE date < ? OR name != ?", $query->getSql());
    }

    public function testSubCondition()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->where('date', '<', 30)
            ->where(function ($q) {
                $q->where('name', '!=', 'Root')
                    ->or('date', '<', '01-01-2000');
            });

        $this->assertEquals("SELECT id, name AS username FROM users WHERE date < ? AND ( name != ? OR date < ? )", $query->getSql());
    }

    public function testBetweenCondition()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->between('numberColumn', 10, 30);

        $this->assertEquals('SELECT id, name AS username FROM users WHERE numberColumn BETWEEN ? AND ?', $query->getSql());
    }

    public function testNotBetweenCondition()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->notBetween('numberColumn', 10, 30);

        $this->assertEquals('SELECT id, name AS username FROM users WHERE numberColumn NOT BETWEEN ? AND ?', $query->getSql());
    }

    public function testInCondition()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->in('name', ['admin', 'mod']);

        $this->assertEquals("SELECT id, name AS username FROM users WHERE name IN ('admin', 'mod')", $query->getSql());
    }

    public function testNotInCondition()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->notIn('name', ['admin', 'mod']);

        $this->assertEquals("SELECT id, name AS username FROM users WHERE name NOT IN ('admin', 'mod')", $query->getSql());
    }

    public function testIsNullCondition()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->isNull('name');

        $this->assertEquals('SELECT id, name AS username FROM users WHERE name IS NULL', $query->getSql());
    }

    public function testIsNotNullCondition()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->isNotNull('name');

        $this->assertEquals('SELECT id, name AS username FROM users WHERE name IS NOT NULL', $query->getSql());
    }

    public function testOrderBy()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->orderBy('id');

        $this->assertEquals('SELECT id, name AS username FROM users ORDER BY id', $query->getSql());
    }

    public function testOrderByDesc()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->orderBy('id', 'desc');

        $this->assertEquals('SELECT id, name AS username FROM users ORDER BY id DESC', $query->getSql());
    }

    public function testGroupBy()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->groupBy('id');

        $this->assertEquals('SELECT id, name AS username FROM users GROUP BY id', $query->getSql());
    }

    public function testLimit()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->limit(10);

        $this->assertEquals('SELECT id, name AS username FROM users LIMIT ?', $query->getSql());
    }

    public function testLimitOffset()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->limit(10, 20);

        $this->assertEquals('SELECT id, name AS username FROM users LIMIT ?, ?', $query->getSql());
    }

    public function testSubQueryAsSource()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->limit(10, 20);

        $query = $queryBuilder->table(function() use($queryBuilder) {
                    return $queryBuilder->table('users')
                    ->select('id', 'name');
                })
            ->select(['id', 'name']);

        $this->assertEquals('SELECT id, name AS username FROM (SELECT id, name FROM users)', $query->getSql());
    }

    public function testSubQueryAsConditionValue()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name'])
            ->where('date', '<', function() use($queryBuilder) {
                return $queryBuilder->table('users')
                    ->select(['min(date)' => 'lowerDate']);
            });

        $this->assertEquals('SELECT id, name FROM users WHERE date < (SELECT min(date) as lowerDate FROM users)', $query->getSql());
    }

    public function testUnion()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name'])
            ->where('date', '<', 30)

            ->union()

            ->table('users')
            ->select(['id', 'name'])
            ->where('date', '<', 100);

        $this->assertEquals('(SELECT id, name FROM users WHERE date > ?) UNION (SELECT id, name FROM users WHERE date < ?)', $query->getSql());
    }
}