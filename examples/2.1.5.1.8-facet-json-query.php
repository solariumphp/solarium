<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect();

// get the facetset component
$facetSet = $query->getFacetSet();

// create a json query instance and set options
$inStockFacet = new Solarium\Component\Facet\JsonQuery(['local_key' => 'stock_query', 'query' => 'inStock:true']);

// add json query instance to the facetSet
$facetSet->addFacet($inStockFacet);

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by Solr
echo 'NumFound: '.$resultset->getNumFound();

// JsonQuery returns a FacetSet, not Buckets like a JsonTerms facet
$stock_query = $resultset->getFacetSet()->getFacet('stock_query');

// A JsonQuery triggers an implicit "count" aggregation for the result of the query which is accessible just like a Facet within the FacetSet.
$count = $stock_query->getFacet('count')->getValue();

echo '<hr/>Facet "inStock" count : ' . $count;

// show documents using the resultset iterator
foreach ($resultset as $document) {

    echo '<hr/><table>';
    echo '<tr><th>id</th><td>' . $document->id . '</td></tr>';
    echo '<tr><th>name</th><td>' . $document->name . '</td></tr>';
    echo '<tr><th>price</th><td>' . $document->price . '</td></tr>';
    echo '</table>';
}

htmlFooter();
