<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Jarzon\QueryBuilder as QB;

class FunctionsTest extends TestCase
{
    public function testFalseAlias()
    {
        QB::setPDO(new PdoMock());
/*
        // Using a class to allow auto complete
        $users = new Users();

        $query = QB::select($users)
            ->columns([
                $users::id,
                QB::concat([$users::name, ' - ', QB::date($users::date, false)], 'date')
            ]);

        // Static class
        $query = QB::select(U)
            ->columns([
                U::id,
                QB::concat([U::name, ' - ', QB::date(U::date, false)], 'date')
            ]);

        // If there is two version of the same DB then us set alias*/

        $query = QB::select('users', 'U')
            ->columns([QB::concat("U.name, ' - ', " . QB::date('date', false), 'date')]);

        $this->assertEquals("SELECT CONCAT(U.name, ' - ', DATE(U.date)) AS date FROM users U", $query->getSql());
    }

    // Numbers

    public function testMin()
    {
        QB::setPDO(new PdoMock());

        $query = QB::select('users', 'U')
            ->columns([QB::min('number')]);

        $this->assertEquals("SELECT MIN(U.number) AS number FROM users U", $query->getSql());
    }

    public function testMax()
    {
        QB::setPDO(new PdoMock());

        $query = QB::select('users', 'U')
            ->columns([QB::max('number')]);

        $this->assertEquals("SELECT MAX(U.number) AS number FROM users U", $query->getSql());
    }

    public function testSum()
    {
        QB::setPDO(new PdoMock());

        $query = QB::select('users', 'U')
            ->columns([QB::sum('number')]);

        $this->assertEquals("SELECT SUM(U.number) AS number FROM users U", $query->getSql());
    }

    public function testAvg()
    {
        QB::setPDO(new PdoMock());

        $query = QB::select('users', 'U')
            ->columns([QB::avg('number')]);

        $this->assertEquals("SELECT AVG(U.number) AS number FROM users U", $query->getSql());
    }

    public function testCeiling()
    {
        QB::setPDO(new PdoMock());

        $query = QB::select('users', 'U')
            ->columns([QB::ceiling('number')]);

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

    public function testFormat()
    {
        QB::setPDO(new PdoMock());

        $query = QB::select('users', 'U')
            ->columns([QB::format('number')]);

        $this->assertEquals("SELECT FORMAT(U.number, 2, 'fr_CA') AS number FROM users U", $query->getSql());
    }

    public function testCurrency()
    {
        QB::setPDO(new PdoMock());

        $query = QB::select('users', 'U')
            ->columns([
                QB::currency('number')
            ]);

        $this->assertEquals("SELECT CONCAT(FORMAT(U.number, 2, 'fr_CA'), ' $') AS number FROM users U", $query->getSql());
    }

    // ABS
    // POWER(int, int)
    // GREATEST(...values)
    // LEAST(...values)

    // Strings

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
            ->columns([QB::concat(["'# '", 'U.number'], 'number')]);

        $this->assertEquals("SELECT CONCAT('# ', U.number) AS number FROM users U", $query->getSql());
    }

    public function testGroupConcat()
    {
        QB::setPDO(new PdoMock());

        $query = QB::select('users', 'U')
            ->columns([QB::groupConcat('U.number', 'number')]);

        $this->assertEquals("SELECT GROUP_CONCAT(U.number) AS number FROM users U", $query->getSql());
    }

    // REPLACE(baseString, search, replace)
    // LOWER
    // UPPER
    // LEFT(string, limit)
    // RIGHT(string, limit)

    // Dates

    public function testDate()
    {
        QB::setPDO(new PdoMock());

        $query = QB::select('users', 'U')
            ->columns([QB::date('date', 'date2')]);

        $this->assertEquals("SELECT DATE(U.date) AS date2 FROM users U", $query->getSql());
    }

    public function testCurrentDate()
    {
        QB::setPDO(new PdoMock());

        $query = QB::select('users', 'U')
            ->columns([QB::currentDate('date')]);

        $this->assertEquals("SELECT CURDATE() AS date FROM users U", $query->getSql());
    }

    public function testDateAdd()
    {
        QB::setPDO(new PdoMock());

        $query = QB::select('users', 'U')
            ->columns([QB::dateAdd('date', 'INTERVAL 1 DAY')]);

        $this->assertEquals("SELECT DATE_ADD(U.date, INTERVAL 1 DAY) AS date FROM users U", $query->getSql());
    }

    // STR_TO_DATE(string, dateFormat)
    // DATE_SUB
    // DATE_FORMAT(date, format) [format: %Y-%m-%d]
    // DATE_DIFF(date1, date2)
    // DAY
    // MOUTH
    // YEAR

    // Time

    // TIME_FORMAT
    // TIME_ADD
    // HOUR
    // MINUTE

    // Others

    // IF(expresion, returnIfTrue, returnIfFalse)
    // IFNULL(valueReturnIfNotNull, valueReturnedIfNull)
    // IN value IN (0, 1, 2, 3, 'a')

    // CONVERT(sourceValue, type)
}