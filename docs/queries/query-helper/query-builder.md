Query builder
-------------

The query builder is a simple helper class to help writing and maintaining (filter) queries using Solr's query language.
While the query will only accept a single (composite) expression, the addition of filter queries can consist of multiple expressions.

Query example
-------------
```php
<?php

use Solarium\Builder\Select\QueryBuilder;

// ...

$query = $client->createSelect();

$expr = QueryBuilder::expr();
$builder = QueryBuilder::create()
    ->where($expr->andX(
        $expr->eq('foo', 'bar'),
        $expr->eq('baz', 'qux')
    ))
;

$query->setQueryFromQueryBuilder($builder);

// which would be equal to
$query->setQuery('foo:"bar" AND baz:"qux"');
```

Filter Query example
-------------
```php
<?php

use Solarium\Builder\Select\QueryBuilder;

// ...

$query = $client->createSelect();

$expr = QueryBuilder::expr();
$builder = QueryBuilder::create()
    ->where($expr->eq('foo', 'bar')),
    ->andWhere($expr->neq('baz', 'qux')
);

$query->addFilterQueriesFromQueryBuilder($builder);

// which would be equal to
$value = 'foo:"bar"';
$query->addFilterQuery(['key' => sha1($value), 'query' => $value]);
$value = '-baz:"qux"';
$query->addFilterQuery(['key' => sha1($value), 'query' => $value]);
```

Complex filter queries
----------------------
While the ``addFilterQueriesFromQueryBuilder`` method only provides in setting the facet query key and actual query, the ``QueryBuilder`` can be used in the construction of more complex facet queries.
If one, for example, need to add a tag to the filter query the following method could be used.
```php
<?php

use Solarium\Builder\Select\QueryBuilder;
use Solarium\Builder\Select\QueryExpressionVisitor;

// ...

$query = $client->createSelect();

$expr = QueryBuilder::expr();
$visitor = new QueryExpressionVisitor();

$builder = QueryBuilder::create()
    ->where($expr->eq('foo', 'bar'))
);

$query->addFilterQuery([
  'key' => 'my-key, 
  'query' => $visitor->dispatch($builder->getExpression()[0]),
  'local_tag' => 'my-tag',
]);
``` 