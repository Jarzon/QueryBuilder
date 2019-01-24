<?php
namespace Tests;
class PdoMock extends \PDO
{
    public function __construct(string $dsn = '', string $username = '', string $passwd = '', array $options = [])
    {
        //parent::__construct($dsn, $username, $passwd, $options);
    }
}