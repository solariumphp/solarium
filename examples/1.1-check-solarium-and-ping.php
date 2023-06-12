<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// check Solarium version available
echo 'Solarium library version: ' . Solarium\Client::getVersion() . ' - ';

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// create a ping query
$ping = $client->createPing();

// execute the ping query
try {
    $result = $client->ping($ping);

    echo 'Ping query successful<br/><br/>';
    echo 'Ping status: ' . $result->getPingStatus() . '<br/>';
    echo 'Query time: ' . $result->getQueryTime() . ' ms<br/>';

    // only relevant for distributed requests
    if (null !== $zkConnected = $result->getZkConnected()) {
        echo 'ZooKeeper connected: ' . ($zkConnected ? 'yes' : 'no');
    }
} catch (Exception $e) {
    echo 'Ping query failed';
}

htmlFooter();
