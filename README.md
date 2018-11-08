# Query Builder

## Install

```
composer require jarzon/query-builder
```

## Usage

```php
<?php
$builder = new Jarzon\QueryBuilder();

$builder
  ->select('users')
  ->columns(['name'])
  ->where('id', '<', 30)
    ->where('name', '!=', 'admin');

// Output:
// SELECT name FROM users WHERE id < 30 AND name != 'admin'
```