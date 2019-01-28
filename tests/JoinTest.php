<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Jarzon\QueryBuilder;

class JoinTest extends TestCase
{
    public function testBasicLeftJoin()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder
            ->table('users')
            ->select(['id', 'name'])
            ->leftJoin('accounts', 'user_id', '=', 'id')
            ->where('date', '<', 30);

        $query->exec(30);

        $this->assertEquals('SELECT id, name FROM users LEFT JOIN accounts ON accounts.user_id = users.id WHERE date < ?', $query->getSql());
    }

    public function testComplexLeftJoin()
    {
        $queryBuilder = new QueryBuilder(new PdoMock());

        $query = $queryBuilder
            ->table('users')
            ->select(['id', 'name'])
            ->leftJoin('accounts', function ($join) {
                $join
                    ->whereRaw('account.user_id', '=', 'users.id')
                    ->where('money', '>', 100);
            })
            ->where('date', '<', 30);

        $this->assertEquals('SELECT id, name FROM users LEFT JOIN accounts ON account.user_id = users.id AND account.money > :money WHERE date < :date', $query->getSql());
    }
}