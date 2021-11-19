The facet class supports the Solr range facet: <https://solr.apache.org/guide/faceting.html#range-faceting>.

Options
-------

The options below can be set as query option values, but also by using the set/get methods. See the API docs for all available methods.

Only the facet-type specific options are listed. See [FacetSet component](facetset-component.md) for the options shared by all facet types.

| Name    | Type           | Default value | Description                                                                                                                                             |
|---------|----------------|---------------|---------------------------------------------------------------------------------------------------------------------------------------------------------|
| field   | string         | null          | This param indicates what field to create range facets for                                                                                              |
| start   | string         | null          | The lower bound of the ranges.                                                                                                                          |
| end     | string         | null          | The upper bound of the ranges.                                                                                                                          |
| gap     | string         | null          | The size of each range expressed as a value to be added to the lower bound.                                                                             |
| hardend | string         | null          | A Boolean parameter instructing Solr what to do in the event that facet.range.gap does not divide evenly between facet.range.start and facet.range.end. |
| other   | string         | null          | This param indicates what to count in addition to the counts for each range constraint between facet.range.start and facet.range.en                     |
| include | string         | null          | Specify count bounds                                                                                                                                    |
| tag     | string         | null          | When defined, it's used as the identifier in the select query. Required when specifying pivot fields                                                    |
| pivot   | string / array | null          | One or more fields which should be used to create pivot values                                                                                          |
||

Examples
--------

```php
<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect();

// get the facetset component
$facetSet = $query->getFacetSet();

// create a facet range instance and set options
$facet = $facetSet->createFacetRange('priceranges');
$facet->setField('price');
$facet->setStart(1);
$facet->setGap(100);
$facet->setEnd(1000);

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by Solr
echo 'NumFound: '.$resultset->getNumFound();

// display facet counts
echo '<hr/>Facet ranges:<br/>';
$facet = $resultset->getFacetSet()->getFacet('priceranges');
foreach ($facet as $range => $count) {
    echo $range . ' to ' . ($range + 100) . ' [' . $count . ']<br/>';
}

// show documents using the resultset iterator
foreach ($resultset as $document) {

    echo '<hr/><table>';
    echo '<tr><th>id</th><td>' . $document->id . '</td></tr>';
    echo '<tr><th>name</th><td>' . $document->name . '</td></tr>';
    echo '<tr><th>price</th><td>' . $document->price . '</td></tr>';
    echo '</table>';
}

htmlFooter();

```
or when specifying pivot fields:

```php
<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect();

// get the facetset component
$facetSet = $query->getFacetSet();

// create a facet range instance and set options
$facet = $facetSet->createFacetRange(['key' => 'manufacturedate_dt', 'tag' => 'r1']);

$facet->setField('manufacturedate_dt');
$facet->setStart('2006-01-01T00:00:00Z');
$facet->setEnd('NOW/YEAR');
$facet->setGap('+1YEAR');
$facet->setPivot(['inStock']);

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by Solr
echo 'NumFound: '.$resultset->getNumFound();

// display pivot facet counts
echo '<hr/>Facet ranges:<br/>';
$facets = $resultset->getFacetSet()->getFacet('manufacturedate_dt');
foreach ($facets as $facet) {
    foreach ($facet->getRanges() as $range) {
        foreach ($range->getValues() as $date => $count) {
            echo $date . ' [' . $count . ']<br/>';
        }
    }
}

htmlFooter();

```