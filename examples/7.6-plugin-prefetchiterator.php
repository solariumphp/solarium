<?php

require_once(__DIR__.'/init.php');

htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect();
$query->setFields(array('id'));

// cursor functionality can be used for efficient deep paging (since Solr 4.7)
$query->setCursorMark('*');
// cursor functionality requires a sort containing a uniqueKey field as tie breaker on top of your desired sorts for the query
$query->addSort('id', $query::SORT_ASC);

// get a plugin instance and apply settings
$prefetch = $client->getPlugin('prefetchiterator');
$prefetch->setPrefetch(2); // fetch 2 rows per request (for real world use this can be way higher)
$prefetch->setQuery($query);

// display the total number of documents found by Solr
echo 'NumFound: ' . count($prefetch);

// show document IDs using the resultset iterator
foreach ($prefetch as $document) {
    echo '<hr/>ID: '. $document->id;
}

htmlFooter();
