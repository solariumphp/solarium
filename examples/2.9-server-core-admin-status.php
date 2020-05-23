<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// create a core admin query
$coreAdminQuery = $client->createCoreAdmin();

// use the core admin query to build a Status action
$statusAction = $coreAdminQuery->createStatus();
$coreAdminQuery->setAction($statusAction);

$response = $client->coreAdmin($coreAdminQuery);
$statusResults = $response->getStatusResults();

echo '<b>Core admin status action execution:</b><br/>';
foreach($statusResults as $statusResult) {
    echo 'Uptime of the core ( ' .$statusResult->getCoreName(). ' ): ' . $statusResult->getUptime();
}

htmlFooter();