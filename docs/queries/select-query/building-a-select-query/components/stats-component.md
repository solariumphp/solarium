For a description of the Solr StatsComponent see the [http://wiki.apache.org/solr/StatsComponent Solr wiki page](http://wiki.apache.org/solr/StatsComponent_Solr_wiki_page "wikilink").

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

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

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
