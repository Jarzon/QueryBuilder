<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Jarzon\QueryBuilder as QB;

class JoinTest extends TestCase
{
    public function testBasicLeftJoin()
    {
        QB::setPDO(new PdoMock());

        $query = QB::select('users')
            ->columns(['id', 'name'])
            ->leftJoin('accounts', 'accounts.user_id', '=', 'users.id')
            ->where('date', '<', 30);

        $query->exec(30);

        $this->assertEquals('SELECT users.id, users.name FROM users LEFT JOIN accounts ON accounts.user_id = users.id WHERE users.date < :date', $query->getSql());
    }

    public function testComplexLeftJoin()
    {
        QB::setPDO(new PdoMock());

        $query = QB::select('users')
            ->columns(['id', 'name'])
            ->leftJoin('accounts', function ($join) {
                $join
                    ->whereRaw('accounts.user_id', '=', 'users.id')
                    ->where('money', '>', 100);
            })
            ->where('date', '<', 30);

        $this->assertEquals('SELECT users.id, users.name FROM users LEFT JOIN accounts ON accounts.user_id = users.id AND accounts.money > :money WHERE users.date < :date', $query->getSql());
    }
}