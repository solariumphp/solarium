<?php

require(__DIR__ . '/vendor/autoload.php');

// check solarium version available
echo 'Solarium library version: ' . Solarium\Client::VERSION . PHP_EOL;

$config = null;

// create a client instance
$client = new Solarium\Client($config);

// create a ping query
$ping = $client->createPing();

// execute the ping query
try {
    $result = $client->ping($ping);
    echo 'Ping query successful' . PHP_EOL;
    var_dump($result->getData());
} catch (Solarium\Exception $e) {
    echo 'Ping query failed' . PHP_EOL;
}
