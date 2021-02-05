<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect();

// filter on documents that have a price field
$query->createFilterQuery('price')->setQuery('price:*');

// get the facetset component
$facetSet = $query->getFacetSet();

// create a facet range instance and set options
$facet = $facetSet->createFacetRange('priceranges');
$facet->setField('price');
$facet->setStart(1);
$facet->setGap(100);
$facet->setEnd(1000);

// include other counts
$facet->setOther($facet::OTHER_ALL); // same as $facet->setOther([$facet::OTHER_BEFORE, $facet::OTHER_BETWEEN, $facet::OTHER_AFTER])

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

// display other facet counts
echo '<hr/>Other facet counts:<br/>';
echo 'before [' . $facet->getBefore() . ']<br/>';
echo 'between [' . $facet->getBetween() . ']<br/>';
echo 'after [' . $facet->getAfter() . ']<br/>';

// show documents using the resultset iterator
foreach ($resultset as $document) {

    echo '<hr/><table>';
    echo '<tr><th>id</th><td>' . $document->id . '</td></tr>';
    echo '<tr><th>name</th><td>' . $document->name . '</td></tr>';
    echo '<tr><th>price</th><td>' . $document->price . '</td></tr>';
    echo '</table>';
}

htmlFooter();
