<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get an update query instance
$update = $client->createUpdate();

// optimize the index
$update->addOptimize(true, false, 5);

// this executes the query and returns the result
$result = $client->update($update);

echo '<b>Update query executed</b><br/>';
echo 'Query status: ' . $result->getStatus(). '<br/>';
echo 'Query time: ' . $result->getQueryTime();

htmlFooter();
