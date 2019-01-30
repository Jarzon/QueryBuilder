<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Jarzon\QueryBuilder as QB;

class InsertTest extends TestCase
{
    public function testSimple()
    {
        QB::setPDO(new PdoMock());

        $query = QB::insert('users')
            ->columns(['name', 'email'])
            ->values(['test', 'test@exemple.com']);

        $this->assertEquals("INSERT INTO users(name, email) VALUES (:name, :email)", $query->getSql());
    }
}