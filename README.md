# Query Builder

WIP

## Install

```
composer require jarzon/querybuilder
```

## Usage

```php
<?php
$query = Jarzon\QueryBuilder::table('users')
  ->select(['name'])
  ->where('id', '<', 30)
  ->where('name', '!=', 'admin');

// $query->getSql() returns:
// SELECT name FROM users WHERE id < 30 AND name != 'admin'
```