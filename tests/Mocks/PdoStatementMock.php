<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Tests\Mocks;
use PDO;

class PdoStatementMock extends \PDOStatement
{
    public $params = [];

    public function __construct()
    {
    }

    public function execute($input_parameters = null)
    {
        $this->params = $input_parameters;
    }

    public function fetchAll($fetch_style = null, $fetch_argument = null, $ctor_args = array())
    {
        return [];
    }

    public function fetch($fetch_style = null, $cursor_orientation = PDO::FETCH_ORI_NEXT, $cursor_offset = 0)
    {
        return '';
    }

    public function fetchColumn($column_number = 0)
    {
        return '';
    }
}