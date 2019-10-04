<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Tests\Mocks;
class PdoMock extends \PDO
{
    public function __construct(string $dsn = '', string $username = '', string $passwd = '', array $options = []){}

    public function prepare($statement, $driver_options = null) {
        return new PdoStatementMock();
    }
}
