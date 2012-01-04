<?php
require('init.php');

htmlHeader();

// create a client instance and get loadbalancer plugin instance
$client = new Solarium_Client($config);
$loadbalancer = $client->getPlugin('loadbalancer');

// apply loadbalancer settings
$optionsSolrOne = array('host' => '127.0.0.1', 'port' => 8983);
$optionsSolrTwo = array('host' => '127.0.0.1', 'port' => 7574);
$loadbalancer->addServer('solr1', $optionsSolrOne, 100);
$loadbalancer->addServer('solr2', $optionsSolrTwo, 200);
$loadbalancer->addServer('solr3', $optionsSolrTwo, 1);

// create a basic query to execute
$query = $client->createSelect();

// execute the query multiple times, displaying the server for each execution
for($i=1; $i<=8; $i++) {
    $resultset = $client->select($query);
    echo 'Query execution #' . $i . '<br/>';
    echo 'NumFound: ' . $resultset->getNumFound(). '<br/>';
    echo 'Server: ' . $loadbalancer->getLastServerKey() .'<hr/>';
}

// force a server for a query (normally solr 3 is extremely unlikely based on it's weight)
$loadbalancer->setForcedServerForNextQuery('solr3');
$resultset = $client->select($query);
echo 'Query execution with server forced to solr3<br/>';
echo 'NumFound: ' . $resultset->getNumFound(). '<br/>';
echo 'Server: ' . $loadbalancer->getLastServerKey() .'<hr/>';

// test a ping query
$query = $client->createPing();
$client->ping($query);
echo 'Loadbalanced ping query, should display a loadbalancing server:<br/>';
echo 'Ping server: ' . $loadbalancer->getLastServerKey() .'<hr/>';

// exclude ping query from loadbalancing
$loadbalancer->addBlockedQueryType(Solarium_Client::QUERYTYPE_PING);
$client->ping($query);
echo 'Non-loadbalanced ping query, should not display a loadbalancing server:<br/>';
echo 'Ping server: ' . $loadbalancer->getLastServerKey() .'<hr/>';

htmlFooter();