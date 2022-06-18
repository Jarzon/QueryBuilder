<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Tests\Mocks;
use \PDO;

class PdoStatementMock extends \PDOStatement
{
    public $params = [];

    public function __construct()
    {
    }

    public function execute($input_parameters = null): bool
    {
        $this->params = $input_parameters;

        return true;
    }

    public function rowCount(): int
    {
        return 0;
    }

    public function fetchAll($mode = PDO::FETCH_BOTH, ...$args): array
    {
        return [];
    }

    public function fetch($fetch_style = null, $cursor_orientation = PDO::FETCH_ORI_NEXT, $cursor_offset = 0): mixed
    {
        return '';
    }

    public function fetchColumn($column_number = 0): mixed
    {
        return '';
    }
}
