<?php

require_once __DIR__.'/init.php';

htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect();

// get the facetset component
$facetSet = $query->getFacetSet();

// create two facet pivot instances
$facet = $facetSet->createFacetPivot('cat-popularity-instock');
$facet->addFields('cat,popularity,inStock');
$facet->setPivotMinCount(0);

$facet = $facetSet->createFacetPivot('popularity-cat');
$facet->addFields('popularity,cat');

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by Solr
echo 'NumFound: '.$resultset->getNumFound();

// display facet results
/** @var Solarium\Component\Result\Facet\Pivot\Pivot $facetResult */
$facetResult = $resultset->getFacetSet()->getFacet('cat-popularity-instock');
echo '<h3>cat &raquo; popularity &raquo; instock</h3>';
foreach ($facetResult as $pivot) {
    displayPivotFacet($pivot);
}

/** @var Solarium\Component\Result\Facet\Pivot\Pivot $facetResult */
$facetResult = $resultset->getFacetSet()->getFacet('popularity-cat');
echo '<h3>popularity &raquo; cat</h3>';
foreach ($facetResult as $pivot) {
    displayPivotFacet($pivot);
}

htmlFooter();

/**
 * Recursively render pivot facets.
 *
 * @param Solarium\Component\Result\Facet\Pivot\PivotItem $pivot
 */
function displayPivotFacet(Solarium\Component\Result\Facet\Pivot\PivotItem $pivot): void
{
    echo '<ul>';
    echo '<li>Field: '.$pivot->getField().'</li>';
    echo '<li>Value: '.$pivot->getValue().'</li>';
    echo '<li>Count: '.$pivot->getCount().'</li>';
    foreach ($pivot->getPivot() as $nextPivot) {
        displayPivotFacet($nextPivot);
    }
    echo '</ul>';
}
