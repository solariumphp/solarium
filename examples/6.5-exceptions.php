<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// This example shows different ways to catch exceptions thrown by Solarium.

// simulate an unreachable endpoint
$config['endpoint']['localhost']['host'] = '0.0.0.0';

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// create a ping query
$ping = $client->createPing();

// cause an exception and catch it
try {
    $client->execute($ping);
} catch (Exception $e) {
    echo 'Something went wrong:<br/><br/>';
    echo $e->getMessage();
}

echo '<hr/>';

// distinguish exceptions thrown by Solarium
try {
    $client->execute($ping);
} catch (Solarium\Exception\ExceptionInterface $e) {
    echo 'Solarium ran into a problem:<br/><br/>';
    echo $e->getMessage();
} catch (Exception $e) {
    echo 'Something else went wrong:<br/><br/>';
    echo $e->getMessage();
}

echo '<hr/>';

// distinguish exceptions of particular interest
try {
    $client->execute($ping);
} catch (Solarium\Exception\HttpException $e) {
    echo 'Solarium can\'t reach your Solr server:<br/><br/>';
    echo $e->getMessage();
} catch (Exception $e) {
    echo 'Something else went wrong:<br/><br/>';
    echo $e->getMessage();
}

echo '<hr/>';

// distinguish after catching
try {
    $client->execute($ping);
} catch (Exception $e) {
    echo 'Something went wrong:<br/><br/>';
    echo $e->getMessage();
    if ($e instanceof Solarium\Exception\HttpException) {
        echo '<br/><br/>Better call the network team!';
    }
}

htmlFooter();
