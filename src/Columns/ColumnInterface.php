<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Columns;

interface ColumnInterface
{
    public function getOutput();
    public function getColumnOutput();
    public function getColumnName(): string;
    public function getColumnParamName(): string;
    public function getColumnReference(): string;
    public function getColumnSelect(): string;
}
