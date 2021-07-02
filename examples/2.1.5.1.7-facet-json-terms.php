<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect();

// get the facetset component
$facetSet = $query->getFacetSet();

// create a json terms instance and set options
$categoriesTerms = new Solarium\Component\Facet\JsonTerms(['local_key' => 'categories', 'field' => 'cat', 'limit'=>4,'numBuckets'=>true]);

// add json terms instance to the facetSet
$facetSet->addFacet($categoriesTerms);

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by Solr
echo 'NumFound: '.$resultset->getNumFound();

// display facet counts
echo '<hr/>Facet counts for field "cat":<br/>';
$facet = $resultset->getFacetSet()->getFacet('categories');
echo 'NumBuckets: '.$facet->getNumBuckets().' (total possible number of buckets, only available when \'numBuckets\'=>true in JsonTerms)<br/>';
echo 'count(): '.$facet->count().' (number of buckets returned)<br/>';

//  Note: use instanceof Solarium\Component\Result\Facet\Buckets to differentiate from standard field facets.  
foreach ($facet as $bucket) {
    echo $bucket->getValue() . ' [' . $bucket->getCount() . ']<br/>';
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
