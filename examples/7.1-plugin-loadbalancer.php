<?php

require_once(__DIR__.'/init.php');

htmlHeader();

// create a client instance and create endpoints
$client = new Solarium\Client($adapter, $eventDispatcher, $config);
// copy the default endpoint core for the demo
$core = $client->getEndpoint()->getCore();

$endpoint1 = $client->createEndpoint('local1');
$endpoint1->setCore($core); //normally you would add endpoint specific settings...
$endpoint2 = $client->createEndpoint('local2');
$endpoint2->setCore($core); //normally you would add endpoint specific settings...
$endpoint3 = $client->createEndpoint('local3');
$endpoint3->setCore($core); //normally you would add endpoint specific settings...

// get loadbalancer plugin instance and add endpoints
$loadbalancer = $client->getPlugin('loadbalancer');
$loadbalancer->addEndpoint($endpoint1, 100);
$loadbalancer->addEndpoint($endpoint2, 100);
$loadbalancer->addEndpoint($endpoint3, 1);

// you can optionally enable failover mode for unresponsive endpoints, and additionally HTTP status codes of your choosing
$loadbalancer->setFailoverEnabled(true);
$loadbalancer->setFailoverMaxRetries(3);
$loadbalancer->addFailoverStatusCode(504);

// create a basic query to execute
$query = $client->createSelect();

// execute the query multiple times, displaying the server for each execution
for ($i = 1; $i <= 8; $i++) {
    $resultset = $client->select($query);
    echo 'Query execution #' . $i . '<br/>';
    echo 'NumFound: ' . $resultset->getNumFound(). '<br/>';
    echo 'Server: ' . $loadbalancer->getLastEndpoint() .'<hr/>';
}

// force a server for a query (normally 'local3' is extremely unlikely based on its weight)
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
