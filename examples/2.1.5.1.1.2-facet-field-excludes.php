<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect();

// create a filterquery with a tag
$query->createFilterQuery('category')->setQuery('cat:electronics')->addTag('electronics');

// we are only interested in the facets
$query->setRows(0);

// get the facetset component
$facetSet = $query->getFacetSet();

// create a facet field to show all possible terms for the query result
$facetSet->createFacetField('category')->setField('cat');

// addExcludes will exclude filters by tag
$facetSet->createFacetField('unfiltered')->setField('cat')->addExcludes(['electronics']);

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by Solr
echo 'NumFound: '.$resultset->getNumFound();

// display facet counts
echo '<hr/>Facet counts for field "cat"; limited to the filter "electronics":<br/>';
$facet = $resultset->getFacetSet()->getFacet('category');
foreach ($facet as $value => $count) {
    echo $value . ' [' . $count . ']<br/>';
}

// display facet counts
echo '<hr/>Facet counts for field "cat"; excluding the filter "electronics":<br/>';
$facet = $resultset->getFacetSet()->getFacet('unfiltered');
foreach ($facet as $value => $count) {
    echo $value . ' [' . $count . ']<br/>';
}

htmlFooter();
