<?php
namespace Tests;
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
    }
}