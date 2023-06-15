<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect();

// set a query (all items with a price)
$query->setQuery('price:[0.01 TO *]');

// get the facetset component
$facetSet = $query->getFacetSet();

// create first facet range instance and set options
$first_range = $facetSet->createFacetRange('under100');
$first_range->setField('{!f.price.facet.range.start=0.00 f.price.facet.range.end=100.00 f.price.facet.range.gap=10}price');
$first_range->setGap(10);

// create second facet range instance and set options
$second_range = $facetSet->createFacetRange('over50');
$second_range->setField('{!f.price.facet.range.start=50.00 f.price.facet.range.end=450.00 f.price.facet.range.gap=50 f.price.facet.range.other=after}price');

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by Solr
echo 'NumFound: '.$resultset->getNumFound();

// display facet counts for first range
echo '<hr/>Facet ranges under 100:<br/>';
$facet = $resultset->getFacetSet()->getFacet('under100');
foreach ($facet as $range => $count) {
    echo $range . ' to ' . ($range + 9.99) . ' [' . $count . ']<br/>';
}

// display facet counts for second range
echo '<hr/>Facet ranges over 50:<br/>';
$facet = $resultset->getFacetSet()->getFacet('over50');
foreach ($facet as $range => $count) {
    echo $range . ' to ' . ($range + 49.99) . ' [' . $count . ']<br/>';
}
echo '450.0+ [' . $facet->getAfter() . ']<br/>';

// show documents using the resultset iterator
foreach ($resultset as $document) {

    echo '<hr/><table>';
    echo '<tr><th>id</th><td>' . $document->id . '</td></tr>';
    echo '<tr><th>name</th><td>' . $document->name . '</td></tr>';
    echo '<tr><th>price</th><td>' . $document->price . '</td></tr>';
    echo '</table>';
}

htmlFooter();
