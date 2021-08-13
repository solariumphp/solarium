For a description of the Solr Analytics Component see the [Solr Ref Guide](https://solr.apache.org/guide/analytics.html).

Options
-------

| Name        | Type    | Default value | Description                                                                   |
|-------------|---------|---------------|-------------------------------------------------------------------------------|
| functions   | array   | [ ]           | One or more Variable Functions to be used throughout the rest of the request. |
| expressions | array   | [ ]           | A list of calculations to perform over the entire result set.                 |
| groupings   | array   | [ ]           | The list of Groupings to calculate in addition to the expressions.            |
||

Example
-------

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get a select query instance
$query = $client->createSelect();
$query->setRows(0);

// add analytics settings
$analytics = $query->getAnalytics();
$analytics
    ->addFunction('sale()', 'mult(price,quantity)')
    ->addExpression('max_sale', 'max(sale())')
    ->addExpression('med_sale', 'median(sale())')
    ->addGrouping([
        'key' => 'sales',
        'expressions' => [
            'min_price' => 'min(price)',
        ],
        'facets' => [
            [
                'key' => 'category',
                'type' => AbstractFacet::TYPE_VALUE,
                'expression' => 'fill_missing(category, \'No Category\')',
                'sort' => [
                    'criteria' => [
                        [
                            'type' => Criterion::TYPE_EXPRESSION,
                            'expression' => 'min_price',
                            'direction' => 'ascending',
                        ],
                    ],
                    'limit' => 10,
                ],
            ],
        ],
    ]);

// this executes the query and returns the result
$result = $client->select($query);
$analytics = $result->getAnalytics();
```
