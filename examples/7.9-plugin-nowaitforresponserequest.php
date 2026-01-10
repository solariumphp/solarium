<?php

require_once __DIR__.'/init.php';

htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a suggester query instance and add setting to build all suggesters
$suggester = $client->createSuggester();
$suggester->setBuildAll(true);

// don't wait until all suggesters have been built
$plugin = $client->getPlugin('nowaitforresponserequest');

// this executes the query without waiting for the response
$client->suggester($suggester);

// don't forget to remove the plugin again if you do need the response from further requests
$client->removePlugin($plugin);

htmlFooter();
