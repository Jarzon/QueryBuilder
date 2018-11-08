<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Jarzon\QueryBuilder;

class SelectTest extends TestCase
{
    public function testSimpleSelect()
    {
        $db = new QueryBuilder();

        $db
            ->select('users')
            ->columns(['id', 'name'])
            ->where('date', '<', 30);

        $this->assertEquals('SELECT id, name FROM users WHERE date < 30', $db->getSql());
    }

    public function testWithAlias()
    {
        $db = new QueryBuilder();

        $db
            ->select('users')
            ->columns(['id', 'name' => 'username']);

        $this->assertEquals('SELECT id, name AS username FROM users', $db->getSql());
    }

    public function testAndCondition()
    {
        $db = new QueryBuilder();

        $db
            ->select('users')
            ->columns(['id', 'name' => 'username'])
            ->where('date', '<', 30)
            ->where('name', '!=', 'Root');

        $this->assertEquals("SELECT id, name AS username FROM users WHERE date < 30 AND name != 'Root'", $db->getSql());
    }

    public function testOrCondition()
    {
        $db = new QueryBuilder();

        $db
            ->select('users')
            ->columns(['id', 'name' => 'username'])
            ->where('date', '<', 30)
            ->or('name', '!=', 'Root');

        $this->assertEquals("SELECT id, name AS username FROM users WHERE date < 30 OR name != 'Root'", $db->getSql());
    }

    public function testSubCondition()
    {
        $db = new QueryBuilder();

        $db
            ->select('users')
            ->columns(['id', 'name' => 'username'])
            ->where('date', '<', 30)
            ->where(function ($q) {
                $q->where('name', '!=', 'Root')
                    ->or('date', '<', '01-01-2000');
            });

        $this->assertEquals("SELECT id, name AS username FROM users WHERE date < 30 AND ( name != 'Root' OR date < '01-01-2000' )", $db->getSql());
    }

    public function testBetweenCondition()
    {
        $db = new QueryBuilder();

        $db
            ->select('users')
            ->columns(['id', 'name' => 'username'])
            ->between('numberColumn', 10, 30);

        $this->assertEquals('SELECT id, name AS username FROM users WHERE numberColumn BETWEEN 10 AND 30', $db->getSql());
    }

    public function testNotBetweenCondition()
    {
        $db = new QueryBuilder();

        $db
            ->select('users')
            ->columns(['id', 'name' => 'username'])
            ->notBetween('numberColumn', 10, 30);

        $this->assertEquals('SELECT id, name AS username FROM users WHERE numberColumn NOT BETWEEN 10 AND 30', $db->getSql());
    }

    public function testInCondition()
    {
        $db = new QueryBuilder();

        $db
            ->select('users')
            ->columns(['id', 'name' => 'username'])
            ->in('name', ['admin', 'mod']);

        $this->assertEquals("SELECT id, name AS username FROM users WHERE name IN ('admin', 'mod')", $db->getSql());
    }

    public function testNotInCondition()
    {
        $db = new QueryBuilder();

        $db
            ->select('users')
            ->columns(['id', 'name' => 'username'])
            ->notIn('name', ['admin', 'mod']);

        $this->assertEquals("SELECT id, name AS username FROM users WHERE name NOT IN ('admin', 'mod')", $db->getSql());
    }

    public function testIsNullCondition()
    {
        $db = new QueryBuilder();

        $db
            ->select('users')
            ->columns(['id', 'name' => 'username'])
            ->isNull('name');

        $this->assertEquals('SELECT id, name AS username FROM users WHERE name IS NULL', $db->getSql());
    }

    public function testIsNotNullCondition()
    {
        $db = new QueryBuilder();

        $db
            ->select('users')
            ->columns(['id', 'name' => 'username'])
            ->isNotNull('name');

        $this->assertEquals('SELECT id, name AS username FROM users WHERE name IS NOT NULL', $db->getSql());
    }

    public function testOrderBy()
    {
        $db = new QueryBuilder();

        $db
            ->select('users')
            ->columns(['id', 'name' => 'username'])
            ->orderBy('id');

        $this->assertEquals('SELECT id, name AS username FROM users ORDER BY id', $db->getSql());
    }

    public function testOrderByDesc()
    {
        $db = new QueryBuilder();

        $db
            ->select('users')
            ->columns(['id', 'name' => 'username'])
            ->orderBy('id', 'desc');

        $this->assertEquals('SELECT id, name AS username FROM users ORDER BY id DESC', $db->getSql());
    }

    public function testGroupBy()
    {
        $db = new QueryBuilder();

        $db
            ->select('users')
            ->columns(['id', 'name' => 'username'])
            ->groupBy('id');

        $this->assertEquals('SELECT id, name AS username FROM users GROUP BY id', $db->getSql());
    }

    public function testLimit()
    {
        $db = new QueryBuilder();

        $db
            ->select('users')
            ->columns(['id', 'name' => 'username'])
            ->limit(10);

        $this->assertEquals('SELECT id, name AS username FROM users LIMIT 10', $db->getSql());
    }

    public function testLimitOffset()
    {
        $db = new QueryBuilder();

        $db
            ->select('users')
            ->columns(['id', 'name' => 'username'])
            ->limit(10, 20);

        $this->assertEquals('SELECT id, name AS username FROM users LIMIT 10, 20', $db->getSql());
    }
}