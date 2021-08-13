For a description of the Solr StatsComponent see <https://solr.apache.org/guide/the-stats-component.html>.

Options
-------

| Name  | Type    | Default value | Description                                    |
|-------|---------|---------------|------------------------------------------------|
| field | string  | null          | Field to create stats for                      |
| facet | boolean | null          | Return sub-results for values within the facet |
||

Example
-------

```php
<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect();
$query->setRows(0);

// add stats settings
$stats = $query->getStats();
$stats->addFacet('inStock');
$stats->createField('popularity');
$stats->createField('price')->addFacet('price')->addFacet('popularity');

// this executes the query and returns the result
$resultset = $client->select($query);
$statsResult = $resultset->getStats();

// display the stats results
foreach ($statsResult as $field) {
    echo '<h1>' . $field->getName() . '</h1>';
    echo 'Min: ' . $field->getMin() . '<br/>';
    echo 'Max: ' . $field->getMax() . '<br/>';
    echo 'Sum: ' . $field->getSum() . '<br/>';
    echo 'Count: ' . $field->getCount() . '<br/>';
    echo 'Missing: ' . $field->getMissing() . '<br/>';
    echo 'SumOfSquares: ' . $field->getSumOfSquares() . '<br/>';
    echo 'Mean: ' . $field->getMean() . '<br/>';
    echo 'Stddev: ' . $field->getStddev() . '<br/>';

    echo '<h2>Field facets</h2>';
    foreach ($field->getFacets() as $field => $facet) {
        echo '<h3>Facet ' . $field . '</h3>';
        foreach ($facet as $facetStats) {
            echo '<h4>Value: ' . $facetStats->getValue() . '</h4>';
            echo 'Min: ' . $facetStats->getMin() . '<br/>';
            echo 'Max: ' . $facetStats->getMax() . '<br/>';
            echo 'Sum: ' . $facetStats->getSum() . '<br/>';
            echo 'Count: ' . $facetStats->getCount() . '<br/>';
            echo 'Missing: ' . $facetStats->getMissing() . '<br/>';
            echo 'SumOfSquares: ' . $facetStats->getSumOfSquares() . '<br/>';
            echo 'Mean: ' . $facetStats->getMean() . '<br/>';
            echo 'Stddev: ' . $facetStats->getStddev() . '<br/>';
        }
    }

    echo '<hr/>';
}

htmlFooter();

```

Local parameters can be used to request a subset of the supported statistics.

```php
$stats->createField('{!min=true max=true mean=true}popularity');
```

Some statistics are not computed by default in Solr. You can request them
via local parameters.

```php
/* Percentiles */
$stats->createField('{!percentiles="99,99.9,99.99"}popularity');
// ...
$field->getPercentiles();

/* The set of all distinct values */
$stats->createField('{!distinctValues=true}popularity');
// ...
$field->getDistinctValues();

/* The exact number of distinct values */
$stats->createField('{!countDistinct=true}popularity');
// ...
$field->getCountDistinct();

/* A statistical approximation of the number of distinct values */
$stats->createField('{!cardinality=0.3}popularity');
// ...
$field->getCardinality();
```

This overrides the default set of statistics. Explicitly add any of the default
statistics you also want computed.

```php
$stats->createField('{!min=true max=true percentiles="99,99.9,99.99"}popularity');
```
