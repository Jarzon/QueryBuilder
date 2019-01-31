<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Jarzon\QueryBuilder as QB;

class FunctionsTest extends TestCase
{
    public function testDate()
    {
        QB::setPDO(new PdoMock());

        $query = QB::select('users', 'U')
            ->columns([QB::date('date', 'date2')]);

        $this->assertEquals("SELECT DATE(U.date) AS date2 FROM users U", $query->getSql());
    }

    public function testFalseAlias()
    {
        QB::setPDO(new PdoMock());

        $query = QB::select('users', 'U')
            ->columns([QB::concat(['U.name', ' - ', QB::date('date', false)], 'date')]);

        $this->assertEquals("SELECT CONCAT(U.name, ' - ', DATE(U.date)) AS date FROM users U", $query->getSql());
    }

    public function testCeiling()
    {
        QB::setPDO(new PdoMock());

        $query = QB::select('users', 'U')
            ->columns([QB::CEILING('number')]);

        $this->assertEquals("SELECT CEILING(U.number) AS number FROM users U", $query->getSql());
    }

    public function testFloor()
    {
        QB::setPDO(new PdoMock());

        $query = QB::select('users', 'U')
            ->columns([QB::floor('number')]);

        $this->assertEquals("SELECT FLOOR(U.number) AS number FROM users U", $query->getSql());
    }

    public function testCount()
    {
        QB::setPDO(new PdoMock());

        $query = QB::select('users', 'U')
            ->columns([QB::count('number')]);

        $this->assertEquals("SELECT COUNT(U.number) AS number FROM users U", $query->getSql());
    }

    public function testLength()
    {
        QB::setPDO(new PdoMock());

        $query = QB::select('users', 'U')
            ->columns([QB::length('number')]);

        $this->assertEquals("SELECT CHAR_LENGTH(U.number) AS number FROM users U", $query->getSql());
    }

    public function testConcat()
    {
        QB::setPDO(new PdoMock());

        $query = QB::select('users', 'U')
            ->columns([QB::concat(['"# "', 'U.number'], 'number')]);

        $this->assertEquals("SELECT CONCAT('# ', U.number) AS number FROM users U", $query->getSql());
    }

    public function testGroupConcat()
    {
        QB::setPDO(new PdoMock());

        $query = QB::select('users', 'U')
            ->columns([QB::groupConcat('U.number', 'number')]);

        $this->assertEquals("SELECT GROUP_CONCAT(U.number) AS number FROM users U", $query->getSql());
    }
}