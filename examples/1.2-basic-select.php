<?php

require('./vendor/autoload.php');

// create a client instance
$client = new Solarium\Client($config);

// get a select query instance
$query = $client->createQuery($client::QUERY_SELECT);

// Pagination example
$resultsPerPage = 15;
$currentPage = 1;
        
// Set the number of results to return
$query->setRows($resultsPerPage);
// Set the 0-based result to start from, taking into account pagination
$query->setStart(($currentPage - 1) * $resultsPerPage);

// this executes the query and returns the result
$resultset = $client->execute($query);

// display the total number of documents found by solr
echo 'NumFound: '.$resultset->getNumFound() . PHP_EOL;

// show documents using the resultset iterator
foreach ($resultset as $document) {

    echo str_repeat('=', 50) . PHP_EOL;

    // the documents are also iterable, to get all fields
    foreach ($document as $field => $value) {
        // this converts multivalue fields to a comma-separated string
        if (is_array($value)) {
            $value = implode(', ', $value);
        }

        echo $field . ': ' . $value . PHP_EOL;
        echo str_repeat('=', 50) . PHP_EOL;
    }

}
