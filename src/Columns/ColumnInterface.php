<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Columns;

interface ColumnInterface
{
    public function getOutput();
    public function getColumnName();
    public function getColumnParamName();
    public function getColumnReference();
    public function getColumnSelect();
}