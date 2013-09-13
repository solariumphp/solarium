<?php
require(__DIR__.'/init.php');

htmlHeader();

// create a client instance and autoload the customize request plugin
$client = new Solarium\Client($config);
$parallel = $client->getPlugin('parallelexecution');

// Add a delay param to better show the effect, as an example Solr install with
// only a dozen documents is too fast for good testing
// This param only works with the correct Solr plugin,
// see http://www.raspberry.nl/2012/01/04/solr-delay-component/
// If you don't have to plugin the example still works, just without the delay.
$customizer = $client->getPlugin('customizerequest');
$customizer->createCustomization(
    array(
        'key' => 'delay',
        'type' => 'param',
        'name' => 'delay',
        'value' => '500',
        'persistent' => true
    )
);

// create two queries to execute in an array. Keys are important for fetching the results later!
$queryInstock = $client->createSelect()->setQuery('inStock:true');
$queryLowprice = $client->createSelect()->setQuery('price:[1 TO 300]');

// first execute the queries the normal way and time it
$start = microtime(true);
$client->execute($queryInstock);
$client->execute($queryLowprice);
echo 'Execution time for normal "serial" execution of two queries: ' . round(microtime(true)-$start, 3);


echo '<hr/>';


// now execute the two queries parallel and time it
$start = microtime(true);
$parallel->addQuery('instock', $queryInstock);
$parallel->addQuery('lowprice', $queryLowprice);
$results = $parallel->execute();
echo 'Execution time for parallel execution of two queries: ' . round(microtime(true)-$start, 3);


htmlFooter();

// Note: for this example on a default Solr index (with a tiny index) running on localhost the performance gain is
// minimal to none, sometimes even slightly slower!
// In a realworld scenario with network latency, a bigger dataset, more complex queries or multiple solr instances the
// performance gain is much more.
