<?php
require(__DIR__.'/init.php');

htmlHeader();

// create a client instance and create endpoints
$client = new Solarium\Client($config);
$endpoint1 = $client->createEndpoint('local1'); //normally you would add endpoint specific settings...
$endpoint2 = $client->createEndpoint('local2');
$endpoint3 = $client->createEndpoint('local3');

// get loadbalancer plugin instance and add endpoints
$loadbalancer = $client->getPlugin('loadbalancer');
$loadbalancer->addEndpoint($endpoint1, 100);
$loadbalancer->addEndpoint($endpoint2, 100);
$loadbalancer->addEndpoint($endpoint3, 1);

// create a basic query to execute
$query = $client->createSelect();

// execute the query multiple times, displaying the server for each execution
for ($i = 1; $i <= 8; $i++) {
    $resultset = $client->select($query);
    echo 'Query execution #' . $i . '<br/>';
    echo 'NumFound: ' . $resultset->getNumFound(). '<br/>';
    echo 'Server: ' . $loadbalancer->getLastEndpoint() .'<hr/>';
}

// force a server for a query (normally solr 3 is extremely unlikely based on its weight)
$loadbalancer->setForcedEndpointForNextQuery('local3');

$resultset = $client->select($query);
echo 'Query execution with server forced to local3<br/>';
echo 'NumFound: ' . $resultset->getNumFound(). '<br/>';
echo 'Server: ' . $loadbalancer->getLastEndpoint() .'<hr/>';

// test a ping query
$query = $client->createPing();
$client->ping($query);
echo 'Loadbalanced ping query, should display a loadbalancing server:<br/>';
echo 'Ping server: ' . $loadbalancer->getLastEndpoint() .'<hr/>';

// exclude ping query from loadbalancing
$loadbalancer->addBlockedQueryType(Solarium\Client::QUERY_PING);
$client->ping($query);
echo 'Non-loadbalanced ping query, should not display a loadbalancing server:<br/>';
echo 'Ping server: ' . $loadbalancer->getLastEndpoint() .'<hr/>';

htmlFooter();
