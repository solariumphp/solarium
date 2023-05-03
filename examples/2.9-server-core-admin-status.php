<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// create a CoreAdmin query
$coreAdminQuery = $client->createCoreAdmin();

// use the CoreAdmin query to build a Status action
$statusAction = $coreAdminQuery->createStatus();
$coreAdminQuery->setAction($statusAction);

$response = $client->coreAdmin($coreAdminQuery);
$statusResults = $response->getStatusResults();
$initFailures = $response->getInitFailureResults();

echo '<b>CoreAdmin status action execution:</b><br/>';
foreach($statusResults as $statusResult) {
    echo 'Uptime of the core ( ' .$statusResult->getCoreName(). ' ): ' . $statusResult->getUptime() . '<br/>';
}

foreach($initFailures as $initFailure) {
    echo 'Init failure ( '. $initFailure->getCoreName() .' ): ' . $initFailure->getException() . '<br/>';
}

htmlFooter();
