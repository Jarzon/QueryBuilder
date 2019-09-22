<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Tests;

use PHPUnit\Framework\TestCase;
use \Jarzon\QueryBuilder\Tests\Mocks\PdoMock;
use Jarzon\QueryBuilder\Builder as QB;
use \Jarzon\QueryBuilder\Tests\Mocks\EntityMock;

class FunctionsTest extends TestCase
{
    public function testFalseAlias()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');

        $query = QB::select($users)
            ->columns($users->date->date()->preAppend($users->name, "' - '"));

        $this->assertEquals("SELECT CONCAT(U.name, ' - ', DATE(U.date)) AS date FROM users U", $query->getSql());
    }

    // Numbers

    public function testMin()
    {
        QB::setPDO(new PdoMock());

        $users = new EntityMock('U');

        $query = QB::select($users)
            ->columns($users->number->min());

        $this->assertEquals("SELECT MIN(U.number) AS number FROM users U", $query->getSql());
    }

    public function testMax()
    {
        QB::setPDO(new PdoMock());
        $users = new EntityMock('U');

        $query = QB::select($users)
            ->columns($users->number->max());

        $this->assertEquals("SELECT MAX(U.number) AS number FROM users U", $query->getSql());
    }

    public function testSum()
    {
        QB::setPDO(new PdoMock());
        $users = new EntityMock('U');

        $query = QB::select($users)
            ->columns($users->number->sum());

        $this->assertEquals("SELECT SUM(U.number) AS number FROM users U", $query->getSql());
    }

    public function testAvg()
    {
        QB::setPDO(new PdoMock());
        $users = new EntityMock('U');

        $query = QB::select($users)
            ->columns($users->number->avg());

        $this->assertEquals("SELECT AVG(U.number) AS number FROM users U", $query->getSql());
    }

    public function testRound()
    {
        QB::setPDO(new PdoMock());
        $users = new EntityMock('U');

        $query = QB::select($users)
            ->columns($users->number->round());

        $this->assertEquals("SELECT ROUND(U.number, 2) AS number FROM users U", $query->getSql());
    }

    public function testCeiling()
    {
        QB::setPDO(new PdoMock());
        $users = new EntityMock('U');

        $query = QB::select($users)
            ->columns($users->number->ceiling());

        $this->assertEquals("SELECT CEILING(U.number) AS number FROM users U", $query->getSql());
    }

    public function testFloor()
    {
        QB::setPDO(new PdoMock());
        $users = new EntityMock('U');

        $query = QB::select($users)
            ->columns($users->number->floor());

        $this->assertEquals("SELECT FLOOR(U.number) AS number FROM users U", $query->getSql());
    }

    public function testCount()
    {
        QB::setPDO(new PdoMock());
        $users = new EntityMock('U');

        $query = QB::select($users)
            ->columns($users->number->count());

        $this->assertEquals("SELECT COUNT(U.number) AS number FROM users U", $query->getSql());
    }

    public function testFormat()
    {
        QB::setPDO(new PdoMock());
        $users = new EntityMock('U');

        $query = QB::select($users)
            ->columns($users->number->format());

        $this->assertEquals("SELECT FORMAT(U.number, 2, 'sv_SE') AS number FROM users U", $query->getSql());
    }

    public function testCurrency()
    {
        QB::setPDO(new PdoMock());
        $users = new EntityMock('U');

        $query = QB::select($users)
            ->columns(
                $users->number->currency()
            );

        $this->assertEquals("SELECT CONCAT(FORMAT(U.number, 2, 'sv_SE'), ' $') AS number FROM users U", $query->getSql());
    }

    // ABS
    // POWER(int, int)
    // GREATEST(...values)
    // LEAST(...values)

    // Strings

    public function testLength()
    {
        QB::setPDO(new PdoMock());
        $users = new EntityMock('U');

        $query = QB::select($users)
            ->columns($users->name->length());

        $this->assertEquals("SELECT CHAR_LENGTH(U.name) AS name FROM users U", $query->getSql());
    }

    public function testConcat()
    {
        QB::setPDO(new PdoMock());
        $users = new EntityMock('U');

        $query = QB::select($users)
            ->columns($users->number->preAppend(QB::raw('# ')));

        $this->assertEquals("SELECT CONCAT('# ', U.number) AS number FROM users U", $query->getSql());
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
        $users = new EntityMock('U');

        $query = QB::select($users)
            ->columns($users->date->date()->alias('date2'));

        $this->assertEquals("SELECT DATE(U.date) AS date2 FROM users U", $query->getSql());
    }

    public function testCurrentDate()
    {
        QB::setPDO(new PdoMock());
        $users = new EntityMock('U');

        $query = QB::select($users)
            ->columns(QB::currentDate('date'));

        $this->assertEquals("SELECT CURDATE() AS date FROM users U", $query->getSql());
    }

    public function testDateAdd()
    {
        QB::setPDO(new PdoMock());
        $users = new EntityMock('U');

        $query = QB::select($users)
            ->columns($users->date->dateAdd('1 DAY'));

        $this->assertEquals("SELECT DATE_ADD(U.date, INTERVAL 1 DAY) AS date FROM users U", $query->getSql());
    }

    public function testWhereDateAdd()
    {
        QB::setPDO(new PdoMock());
        $users = new EntityMock('U');

        $query = QB::select($users)
            ->columns($users->name)
            ->where('NOW()', '>', $users->date->dateAdd('1 MONTH'))
            ->where('NOW()', '<', $users->date->dateAdd('1 YEAR'));

        $this->assertEquals("SELECT U.name FROM users U WHERE NOW() > DATE_ADD(U.date, INTERVAL 1 MONTH) AND NOW() < DATE_ADD(U.date, INTERVAL 1 YEAR)", $query->getSql());
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

    public function testIf()
    {
        QB::setPDO(new PdoMock());
        $users = new EntityMock('U');

        $query = QB::select($users)
            ->columns($users->created->ifIsGreaterThat(0, $users->created->ifIsNull(0), "$users->date - 1")->alias('date'));

        $this->assertEquals("SELECT IF(U.created > 0, IFNULL(U.created, 0), U.date - 1) AS date FROM users U", $query->getSql());
    }

    // IN value IN (0, 1, 2, 3, 'a')
    // CASE column1
    //   WHEN 0 THEN column2
    //   WHEN 1 THEN column3
    // END AS columnAlias

    // CONVERT(sourceValue, type)
}
