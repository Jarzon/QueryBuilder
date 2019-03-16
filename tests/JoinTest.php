<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Tests;

use Jarzon\QueryBuilder\Statements\Join;
use PHPUnit\Framework\TestCase;
use \Jarzon\QueryBuilder\Tests\Mocks\PdoMock;
use Jarzon\QueryBuilder\Builder as QB;
use Jarzon\QueryBuilder\Tests\Mocks\EntityMock;
use Jarzon\QueryBuilder\Tests\Mocks\TestEntityMock;

class JoinTest extends TestCase
{
    public function testBasicLeftJoin()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');

        $query = QB::select($users)
            ->columns($users->id, $users->name)
            ->leftJoin('accounts', 'accounts.user_id', '=', $users->id)
            ->where($users->date, '<', 30);

        $this->assertEquals('SELECT U.id, U.name FROM users U LEFT JOIN accounts ON accounts.user_id = U.id WHERE U.date < :date', $query->getSql());
    }

    public function testComplexLeftJoin()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');
        $test = new TestEntityMock('T');

        $query = QB::select($users)
            ->columns($users->id, $users->name)
            ->leftJoin($test, function (Join $join) use ($users, $test) {
                $join
                    ->whereRaw($test->user_id, '=', $users->id)
                    ->where($test->text, '!=', 'something');
            })
            ->where($users->date, '<', 30);

        $this->assertEquals('SELECT U.id, U.name FROM users U LEFT JOIN test T ON T.user_id = U.id AND T.text != :text WHERE U.date < :date', $query->getSql());
    }
}