<?php

require_once __DIR__.'/init.php';
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a suggester query instance and add setting to build all suggesters
$suggester = $client->createSuggester();
$suggester->setBuildAll(true);

// don't wait unitl all suggesters have been built
$client->getPlugin('nowaitforresponserequest');

// this executes the query
$client->suggester($suggester);

htmlFooter();
