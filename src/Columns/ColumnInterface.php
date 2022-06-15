<?php
declare(strict_types=1);

namespace Jarzon\QueryBuilder\Columns;

use Jarzon\QueryBuilder\Raw;

interface ColumnInterface
{
    public function getOutput(): string|Raw;
    public function getColumnOutput(): string|Raw;
    public function getColumnName(): string;
    public function getColumnParamName(): string;
    public function getColumnReference(): string;
    public function getColumnSelect(): string;
}
