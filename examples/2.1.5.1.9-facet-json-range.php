<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect();

// get the facetset component
$facetSet = $query->getFacetSet();

// create a json range instance and set options
// Set the 'other' parameter to retrieve before, after, between counts.
// See https://lucene.apache.org/solr/guide/json-facet-api.html#range-facet-parameters
$priceranges = new \Solarium\Component\Facet\JsonRange(['local_key' => 'priceranges', 'field' => 'price','start'=>1 ,'end'=>300,'gap'=>100,'other'=>'all']);

// add json range instance to the facetSet
$facetSet->addFacet($priceranges);

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by solr
echo 'NumFound: '.$resultset->getNumFound();

// display facet counts
echo '<hr/>Facet ranges:<br/>';
$facet = $resultset->getFacetSet()->getFacet('priceranges');

//  Note: use instanceof Solarium\Component\Result\Facet\JsonRange to differentiate from standard field facets.  
if($facet instanceof Solarium\Component\Result\Facet\JsonRange) {
    echo 'Before ['.$facet->getBefore().']<br />';
    echo 'After ['.$facet->getAfter().']<br />';
    echo 'Between ['.$facet->getBetween().']<br />';
}

foreach ($facet as $bucket) {
    echo $bucket->getValue() . ' to ' . ($bucket->getValue() + 100) . ' [' . $bucket->getCount() . ']<br/>';
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
