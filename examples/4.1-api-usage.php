<?php

require(__DIR__.'/init.php');

htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get a select query instance
$query = $client->createSelect();

// apply settings using the API
$query->setQuery('*:*');
$query->setStart(2)->setRows(20);
$query->setFields(array('id','name','price'));
$query->addSort('price', $query::SORT_ASC);

// create a filterquery using the API
$fq = $query->createFilterQuery('maxprice')->setQuery('price:[1 TO 300]');

// create a facet field instance and set options using the API
$facetSet = $query->getFacetSet();
$facet = $facetSet->createFacetField('stock')->setField('inStock');

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by solr
echo 'NumFound: '.$resultset->getNumFound();

// display facet counts
echo '<hr/>Facet counts for field "inStock":<br/>';
$facet = $resultset->getFacetSet()->getFacet('stock');
foreach ($facet as $value => $count) {
    echo $value . ' [' . $count . ']<br/>';
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
