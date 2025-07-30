<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance and a query helper instance
$query = $client->createSelect();
$helper = $query->getHelper();

// create a filterquery with a tag
$query->createFilterQuery('budget')->setQuery($helper->rangeQuery('price', 50, 180))->addTag('budget');

// we are only interested in the facets
$query->setRows(0);

// get the facetset component
$facetSet = $query->getFacetSet();

// create a facet range instance and set options
$facet = $facetSet->createFacetRange('priceranges');
$facet->setField('price');
$facet->setStart(1);
$facet->setGap(100);
$facet->setEnd(1000);

// create a facet range instance, set options and add an exclude by filter tag
$facet = $facetSet->createFacetRange('allpriceranges');
$facet->setField('price');
$facet->setStart(1);
$facet->setGap(100);
$facet->setEnd(1000);
$facet->addExcludes(['budget']);

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by Solr
echo 'NumFound: '.$resultset->getNumFound();

// display facet counts
echo '<hr/>Facet ranges; limited to the filter "budget":<br/>';
$facet = $resultset->getFacetSet()->getFacet('priceranges');
foreach ($facet as $range => $count) {
    echo $range . ' to ' . ($range + 100) . ' [' . $count . ']<br/>';
}

// display facet counts
echo '<hr/>Facet ranges; excluding the filter "budget":<br/>';
$facet = $resultset->getFacetSet()->getFacet('allpriceranges');
foreach ($facet as $range => $count) {
    echo $range . ' to ' . ($range + 100) . ' [' . $count . ']<br/>';
}

htmlFooter();
