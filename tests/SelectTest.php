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

        $this->assertEquals('SELECT * FROM users WHERE users.date < :date', $query->getSql());
    }

    public function testAddSelect()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name'])
            ->where('date', '<', 30)
            ->addSelect('date')
            ->addSelect(['company' => 'companyName']);

        $this->assertEquals('SELECT users.id, users.name, users.date, users.company AS companyName FROM users WHERE users.date < :date', $query->getSql());
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

        $this->assertEquals('SELECT users.id, users.name AS username FROM users', $query->getSql());
    }

    public function testTableAlias()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users', 'U')
            ->select(['id', 'name' => 'username'])
            ->where('date', '<', 30)
            ->where('name', '!=', 'Root');

        $this->assertEquals("SELECT U.id, U.name AS username FROM users WHERE U.date < :date AND U.name != :name", $query->getSql());
    }

    public function testAndCondition()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->where('date', '<', 30)
            ->where('name', '!=', 'Root');

        $this->assertEquals("SELECT users.id, users.name AS username FROM users WHERE users.date < :date AND users.name != :name", $query->getSql());
    }

    public function testOrCondition()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->where('date', '<', 30)
            ->or('name', '!=', 'Root');

        $this->assertEquals("SELECT users.id, users.name AS username FROM users WHERE users.date < :date OR users.name != :name", $query->getSql());
    }

    public function testSubCondition()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->where('date', '<', '01-01-2000')
            ->where(function ($q) {
                $q->where('name', '!=', 'Root')
                    ->or('date', '<', '01-01-2000');
            });

        $this->assertEquals("SELECT users.id, users.name AS username FROM users WHERE users.date < :date1 AND ( users.name != :name OR users.date < :date2 )", $query->getSql());
    }

    public function testBetweenCondition()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->between('numberColumn', 10, 30);

        $this->assertEquals('SELECT users.id, users.name AS username FROM users WHERE users.numberColumn BETWEEN :numberColumn1 AND :numberColumn2', $query->getSql());
    }

    public function testNotBetweenCondition()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->notBetween('numberColumn', 10, 30);

        $this->assertEquals('SELECT users.id, users.name AS username FROM users WHERE users.numberColumn NOT BETWEEN :numberColumn1 AND :numberColumn2', $query->getSql());
    }

    public function testInCondition()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->in('name', ['admin', 'mod']);

        $this->assertEquals("SELECT users.id, users.name AS username FROM users WHERE users.name IN ('admin', 'mod')", $query->getSql());
    }

    public function testNotInCondition()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->notIn('name', ['admin', 'mod']);

        $this->assertEquals("SELECT users.id, users.name AS username FROM users WHERE users.name NOT IN ('admin', 'mod')", $query->getSql());
    }

    public function testIsNullCondition()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->isNull('name');

        $this->assertEquals('SELECT users.id, users.name AS username FROM users WHERE users.name IS NULL', $query->getSql());
    }

    public function testIsNotNullCondition()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->isNotNull('name');

        $this->assertEquals('SELECT users.id, users.name AS username FROM users WHERE users.name IS NOT NULL', $query->getSql());
    }

    public function testOrderBy()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->orderBy('users.id');

        $this->assertEquals('SELECT users.id, users.name AS username FROM users ORDER BY users.id', $query->getSql());
    }

    public function testOrderByDesc()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->orderBy('users.id', 'desc');

        $this->assertEquals('SELECT users.id, users.name AS username FROM users ORDER BY users.id DESC', $query->getSql());
    }

    public function testGroupBy()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->groupBy('users.id');

        $this->assertEquals('SELECT users.id, users.name AS username FROM users GROUP BY users.id', $query->getSql());
    }

    public function testLimit()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->limit(10);

        $this->assertEquals('SELECT users.id, users.name AS username FROM users LIMIT :limit1', $query->getSql());
    }

    public function testLimitOffset()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder->table('users')
            ->select(['id', 'name' => 'username'])
            ->limit(10, 20);

        $this->assertEquals('SELECT users.id, users.name AS username FROM users LIMIT :limit1, :limit2', $query->getSql());
    }
}