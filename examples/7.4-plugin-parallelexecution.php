<?php

require_once(__DIR__.'/init.php');

htmlHeader();

// create a client instance and autoload the customize request plugin
$client = new Solarium\Client($adapter, $eventDispatcher, $config);
$parallel = $client->getPlugin('parallelexecution');

// Add a delay param to better show the effect, as an example Solr install with
// only a dozen documents is too fast for good testing.
// This param only works with the correct Solr plugin, see
// https://web.archive.org/web/20170904162800/http://www.raspberry.nl/2012/01/04/solr-delay-component/
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

// create two queries to execute
$queryInStock = $client->createSelect()->setQuery('inStock:true');
$queryLowPrice = $client->createSelect()->setQuery('price:[1 TO 30]');

// first execute the queries the normal way and time it
echo '<h1>Serial execution</h1>';
$start = microtime(true);
$resultInStock = $client->execute($queryInStock);
$resultLowPrice = $client->execute($queryLowPrice);
echo 'Execution time for normal "serial" execution of two queries: ' . round(microtime(true)-$start, 3) . ' s';
echo '<hr/>';
echo 'In stock: ' . $resultInStock->getNumFound() . '<br/>';
echo 'Low price: ' . $resultLowPrice->getNumFound() . '<br/>';

echo '<hr/>';

// now execute the two queries parallel and time it
echo '<h1>Parallel execution</h1>';
$start = microtime(true);
// keys for each query are important for fetching the results later!
$parallel->addQuery('instock', $queryInStock);
$parallel->addQuery('lowprice', $queryLowPrice);
$results = $parallel->execute();
echo 'Execution time for parallel execution of two queries: ' . round(microtime(true)-$start, 3) . ' s';
echo '<hr/>';
echo 'In stock: ' . $results['instock']->getNumFound() . '<br/>';
echo 'Low price: ' . $results['lowprice']->getNumFound() . '<br/>';

htmlFooter();

// Note: for this example on a default Solr index (with a tiny index) running on localhost the performance gain is
// minimal to none, sometimes even slightly slower!
// In a realworld scenario with network latency, a bigger dataset, more complex queries or multiple Solr instances the
// performance gain is much more.
