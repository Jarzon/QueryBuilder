<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use \Tests\Mocks\PdoMock;
use Jarzon\QueryBuilder as QB;
use Tests\Mocks\TableMock;

class InsertTest extends TestCase
{
    public function testSimple()
    {
        QB::setPDO(new PdoMock());

        $users = new TableMock();

        $query = QB::insert($users) // Unify column and values
            ->columns($users->name, $users->email)
            ->values(['test', 'test@exemple.com']);

        $this->assertEquals("INSERT INTO users(name, email) VALUES (:name, :email)", $query->getSql());
    }
}