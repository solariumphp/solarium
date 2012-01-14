<?php
require('init.php');

htmlHeader();

// create a client instance and autoload the customize request plugin
$client = new Solarium\Client\Client($config);
$parallel = $client->getPlugin('parallelexecution');

// create two queries to execute in an array. Keys are important for fetching the results later!
$queries = array(
    'instock' => $client->createSelect()->setQuery('inStock:true'),
    'lowprice' => $client->createSelect()->setQuery('price:[1 TO 300]'),
);


// first execute the queries the normal way and time it
$start = microtime(true);
$client->execute($queries['instock']);
$client->execute($queries['lowprice']);
echo 'Execution time for normal "serial" execution of two queries: ' . round(microtime(true)-$start, 3);


echo '<hr/>';


// now execute the two queries parallel and time it
$start = microtime(true);
$results = $parallel->execute($queries);
echo 'Execution time for parallel execution of two queries: ' . round(microtime(true)-$start, 3);


htmlFooter();

// Note: for this example on a default Solr index (with a tiny index) the performance gain is minimal to none.
// With a bigger dataset, more complex queries or multiple solr instances the performance gain is much more.
// For testing you can use a Solr delay component (see https://github.com/basdenooijer/raspberry-solr-plugins) to
// artificially slow Solr down by an exact amount of time.