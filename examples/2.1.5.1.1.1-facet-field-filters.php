<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect();

// we are only interested in the facets
$query->setRows(0);

// get the facetset component
$facetSet = $query->getFacetSet();

// create a facet field to show all possible terms
$facetSet->createFacetField('category')->setField('cat');

// setPrefix will only return facets starting with this string
$facetSet->createFacetField('prefixed')->setField('cat')->setPrefix('s');

// setContains will only return facets matchig this string, by default it is case sensitive
// use setContainsIgnoreCase(true) to make it case insensitive
$facetSet->createFacetField('electronics')->setField('cat')->setContains('electronics');

// setMatches takes a java regular expression as a string and only matching terms will be returned
$facetSet->createFacetField('electronicsAndMore')->setField('cat')->setMatches('electronics.+');

// setExcludeTerms takes a comma separated list of terms to exclude from the facet
// escape the comma for a literal match e.g. 'yes\, this term will be excluded'
$facetSet->createFacetField('electronicsExclude')->setField('cat')->setExcludeTerms('electronics,music');

// all three restriction types can also be used on the facetset as a whole and will affect all (non json) facets
// e.g. $facetSet->setExcludeTerms('search');

// setTerms takes a comma separated list of terms to exclude from the facet
// escape the comma for a literal match e.g. 'yes\, this term will be included'
$facetSet->createFacetField('electronicsTerms')->setField('cat')->setTerms('electronics,music');

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by Solr
echo 'NumFound: '.$resultset->getNumFound();

// display facet counts
echo '<hr/>Facet counts for field "cat":<br/>';
$facet = $resultset->getFacetSet()->getFacet('category');
foreach ($facet as $value => $count) {
    echo $value . ' [' . $count . ']<br/>';
}

// display facet counts
echo '<hr/>Facet counts for field "cat"; terms prefixed with "s":<br/>';
$facet = $resultset->getFacetSet()->getFacet('prefixed');
foreach ($facet as $value => $count) {
    echo $value . ' [' . $count . ']<br/>';
}

// display facet counts
echo '<hr/>Facet counts for field "cat"; terms containing "electronics":<br/>';
$facet = $resultset->getFacetSet()->getFacet('electronics');
foreach ($facet as $value => $count) {
    echo $value . ' [' . $count . ']<br/>';
}

// display facet counts
echo '<hr/>Facet counts for field "cat"; terms matching regex "electronics.+":<br/>';
$facet = $resultset->getFacetSet()->getFacet('electronicsAndMore');
foreach ($facet as $value => $count) {
    echo $value . ' [' . $count . ']<br/>';
}

// display facet counts
echo '<hr/>Facet counts for field "cat"; terms excluding "electronics" and "music":<br/>';
$facet = $resultset->getFacetSet()->getFacet('electronicsExclude');
foreach ($facet as $value => $count) {
    echo $value . ' [' . $count . ']<br/>';
}

// display facet counts
echo '<hr/>Facet counts for field "cat"; terms limited to "electronics" and "music":<br/>';
$facet = $resultset->getFacetSet()->getFacet('electronicsTerms');
foreach ($facet as $value => $count) {
    echo $value . ' [' . $count . ']<br/>';
}

htmlFooter();
