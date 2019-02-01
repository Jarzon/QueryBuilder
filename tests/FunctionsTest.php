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

        $query = QB::select('users', 'U')
            ->columns([QB::concat("U.name, ' - ', " . QB::date('date', false), 'date')]);

        $this->assertEquals("SELECT CONCAT(U.name, ' - ', DATE(U.date)) AS date FROM users U", $query->getSql());
    }

    // Numbers

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

    // MIN
    // MAX
    // SUM
    // AVG
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

    // Others

    // IF(expresion, returnIfTrue, returnIfFalse)
    // IFNULL(valueReturnIfNotNull, valueReturnedIfNull)
    // IN value IN (0, 1, 2, 3, 'a')

    // CONVERT(sourceValue, type)
}