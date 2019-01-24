<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Jarzon\QueryBuilder;

class JoinTest extends TestCase
{
    public function testBasicLeftJoin()
    {
        $query = QueryBuilder::table('users')
            ->select(['id', 'name'])
            ->leftJoin('accounts', 'accounts.user_id', '=', 'users.id')
            ->where('date', '<', 30);

        $this->assertEquals('SELECT id, name FROM users LEFT JOIN accounts ON accounts.user_id = users.id WHERE date < 30', $query->getSql());
    }

    public function testComplexLeftJoin()
    {
        $query = QueryBuilder::table('users')
            ->select(['id', 'name'])
            ->leftJoin('accounts', function ($join) {
                $join
                    ->where('account.user_id', '=', 'users.id')
                    ->where('account.money', '>', 100);
            })
            ->where('date', '<', 30);

        $this->assertEquals('SELECT id, name FROM users LEFT JOIN accounts ON account.user_id = users.id AND account.money > 100 WHERE date < 30', $query->getSql());
    }
}