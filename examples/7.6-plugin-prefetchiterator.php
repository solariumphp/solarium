<?php
require(__DIR__.'/init.php');

htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get a select query instance
$query = $client->createSelect();
$query->setFields(array('id'));

// get a plugin instance and apply settings
$prefetch = $client->getPlugin('prefetchiterator');
$prefetch->setPrefetch(2); //fetch 2 rows per query (for real world use this can be way higher)
$prefetch->setQuery($query);

// display the total number of documents found by solr
echo 'NumFound: ' . count($prefetch);

// show document IDs using the resultset iterator
foreach ($prefetch as $document) {
    echo '<hr/>ID: '. $document->id;
}

htmlFooter();
