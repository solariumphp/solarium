<?php

require_once(__DIR__.'/init.php');
htmlHeader();

$select = array(
    'query'         => '*:*',
    'start'         => 2,
    'rows'          => 20,
    'fields'        => array('id','name','price'),
    'sort'          => array('price' => 'asc'),
    'filterquery' => array(
        'maxprice' => array(
            'query' => 'price:[1 TO 300]'
        ),
    ),
    'component' => array(
        'facetset' => array(
            'facet' => array(
                // notice this config uses an inline key value under 'local_key', instead of array key like the filterquery
                array('type' => 'field', 'local_key' => 'stock', 'field' => 'inStock'),
            )
        ),
    ),
);

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance based on the config
$query = $client->createSelect($select);

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by Solr
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
