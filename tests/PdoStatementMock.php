<?php
namespace Tests;
class PdoStatementMock extends \PDOStatement
{
    public function __construct()
    {
    }

    public function fetchAll($fetch_style = null, $fetch_argument = null, $ctor_args = array())
    {
    }
}