<?php
declare(strict_types=1);

namespace Tests;

use Jarzon\Statements\Join;
use PHPUnit\Framework\TestCase;
use \Tests\Mocks\PdoMock;
use Jarzon\QueryBuilder as QB;
use Tests\Mocks\TableMock;

class JoinTest extends TestCase
{
    public function testBasicLeftJoin()
    {
        QB::setPDO(new PdoMock());

        $users = new TableMock('U');

        $query = QB::select($users->table)
            ->columns($users->id, $users->name)
            ->leftJoin('accounts', 'accounts.user_id', '=', $users->id)
            ->where($users->date, '<', 30);

        $query->exec(30);

        $this->assertEquals('SELECT U.id, U.name FROM users U LEFT JOIN accounts ON accounts.user_id = U.id WHERE U.date < :date', $query->getSql());
    }

    public function testComplexLeftJoin()
    {
        QB::setPDO(new PdoMock());

        $users = new TableMock('U');

        $query = QB::select($users->table)
            ->columns($users->id, $users->name)
            ->leftJoin('accounts', function (Join $join) use ($users) {
                $join
                    ->whereRaw('accounts.user_id', '=', $users->id)
                    ->where('accounts.money', '>', 100);
            })
            ->where($users->date, '<', 30);

        $this->assertEquals('SELECT U.id, U.name FROM users U LEFT JOIN accounts ON accounts.user_id = U.id AND accounts.money > :money WHERE U.date < :date', $query->getSql());
    }
}