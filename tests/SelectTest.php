<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Jarzon\QueryBuilder;

class SelectTest extends TestCase
{
    public function testSimpleSelect()
    {
        $query = QueryBuilder::table('users')
            ->select()
            ->where('date', '<', 30);

        $this->assertEquals('SELECT * FROM users WHERE date < 30', $query->getSql());
    }

    public function testAddSelect()
    {
        $query = QueryBuilder::table('users')
            ->select(['id', 'name'])
            ->where('date', '<', 30)
            ->addSelect('date')
            ->addSelect(['company' => 'companyName']);

        $this->assertEquals('SELECT id, name, date, company AS companyName FROM users WHERE date < 30', $query->getSql());
    }

    public function testWhereColumn()
    {
        $query = QueryBuilder::table('users')
            ->select()
            ->where('users.column', '=', 'users.anotherColumn');

        $this->assertEquals('SELECT * FROM users WHERE users.column = users.anotherColumn', $query->getSql());
    }

    public function testComplexWhere()
    {
        $query = QueryBuilder::table('users')
            ->select()
            ->whereRaw('users.column', '>', '(users.anotherColumn - 5)');


        $this->assertEquals('SELECT * FROM users WHERE users.column > (users.anotherColumn - 5)', $query->getSql());
    }

    public function testWithAlias()
    {
        $query = QueryBuilder::table('users')
            ->select(['id', 'name' => 'username']);

        $this->assertEquals('SELECT id, name AS username FROM users', $query->getSql());
    }

    public function testAndCondition()
    {
        $query = QueryBuilder::table('users')
            ->select(['id', 'name' => 'username'])
            ->where('date', '<', 30)
            ->where('name', '!=', 'Root');

        $this->assertEquals("SELECT id, name AS username FROM users WHERE date < 30 AND name != 'Root'", $query->getSql());
    }

    public function testOrCondition()
    {
        $query = QueryBuilder::table('users')
            ->select(['id', 'name' => 'username'])
            ->where('date', '<', 30)
            ->or('name', '!=', 'Root');

        $this->assertEquals("SELECT id, name AS username FROM users WHERE date < 30 OR name != 'Root'", $query->getSql());
    }

    public function testSubCondition()
    {
        $query = QueryBuilder::table('users')
            ->select(['id', 'name' => 'username'])
            ->where('date', '<', 30)
            ->where(function ($q) {
                $q->where('name', '!=', 'Root')
                    ->or('date', '<', '01-01-2000');
            });

        $this->assertEquals("SELECT id, name AS username FROM users WHERE date < 30 AND ( name != 'Root' OR date < '01-01-2000' )", $query->getSql());
    }

    public function testBetweenCondition()
    {
        $query = QueryBuilder::table('users')
            ->select(['id', 'name' => 'username'])
            ->between('numberColumn', 10, 30);

        $this->assertEquals('SELECT id, name AS username FROM users WHERE numberColumn BETWEEN 10 AND 30', $query->getSql());
    }

    public function testNotBetweenCondition()
    {
        $query = QueryBuilder::table('users')
            ->select(['id', 'name' => 'username'])
            ->notBetween('numberColumn', 10, 30);

        $this->assertEquals('SELECT id, name AS username FROM users WHERE numberColumn NOT BETWEEN 10 AND 30', $query->getSql());
    }

    public function testInCondition()
    {
        $query = QueryBuilder::table('users')
            ->select(['id', 'name' => 'username'])
            ->in('name', ['admin', 'mod']);

        $this->assertEquals("SELECT id, name AS username FROM users WHERE name IN ('admin', 'mod')", $query->getSql());
    }

    public function testNotInCondition()
    {
        $query = QueryBuilder::table('users')
            ->select(['id', 'name' => 'username'])
            ->notIn('name', ['admin', 'mod']);

        $this->assertEquals("SELECT id, name AS username FROM users WHERE name NOT IN ('admin', 'mod')", $query->getSql());
    }

    public function testIsNullCondition()
    {
        $query = QueryBuilder::table('users')
            ->select(['id', 'name' => 'username'])
            ->isNull('name');

        $this->assertEquals('SELECT id, name AS username FROM users WHERE name IS NULL', $query->getSql());
    }

    public function testIsNotNullCondition()
    {
        $query = QueryBuilder::table('users')
            ->select(['id', 'name' => 'username'])
            ->isNotNull('name');

        $this->assertEquals('SELECT id, name AS username FROM users WHERE name IS NOT NULL', $query->getSql());
    }

    public function testOrderBy()
    {
        $query = QueryBuilder::table('users')
            ->select(['id', 'name' => 'username'])
            ->orderBy('id');

        $this->assertEquals('SELECT id, name AS username FROM users ORDER BY id', $query->getSql());
    }

    public function testOrderByDesc()
    {
        $query = QueryBuilder::table('users')
            ->select(['id', 'name' => 'username'])
            ->orderBy('id', 'desc');

        $this->assertEquals('SELECT id, name AS username FROM users ORDER BY id DESC', $query->getSql());
    }

    public function testGroupBy()
    {
        $query = QueryBuilder::table('users')
            ->select(['id', 'name' => 'username'])
            ->groupBy('id');

        $this->assertEquals('SELECT id, name AS username FROM users GROUP BY id', $query->getSql());
    }

    public function testLimit()
    {
        $query = QueryBuilder::table('users')
            ->select(['id', 'name' => 'username'])
            ->limit(10);

        $this->assertEquals('SELECT id, name AS username FROM users LIMIT 10', $query->getSql());
    }

    public function testLimitOffset()
    {
        $query = QueryBuilder::table('users')
            ->select(['id', 'name' => 'username'])
            ->limit(10, 20);

        $this->assertEquals('SELECT id, name AS username FROM users LIMIT 10, 20', $query->getSql());
    }

    public function testSubQueryAsSource()
    {
        $query = QueryBuilder::table('users')
            ->select(['id', 'name' => 'username'])
            ->limit(10, 20);

        $this->assertEquals('SELECT id, name AS username FROM (SELECT id, name FROM users)', $query->getSql());
    }

    public function testSubQueryAsConditionValue()
    {
        $query = QueryBuilder::table('users')
            ->select(['id', 'name'])
            ->where('date', '<', 30);

        $this->assertEquals('SELECT id, name FROM users WHERE date < (SELECT min(date) as lowerDate FROM users)', $query->getSql());
    }

    public function testUnion()
    {
        $query = QueryBuilder::table('users')
            ->select(['id', 'name'])
            ->where('date', '<', 30);

        $this->assertEquals('(SELECT id, name FROM users WHERE date > 30) UNION (SELECT id, name FROM users WHERE date < 100)', $query->getSql());
    }
}