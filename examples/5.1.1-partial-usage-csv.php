<?php

require_once __DIR__.'/init.php';

htmlHeader();

// This example shows how to manually execute the query flow to use Solr's CSV response writer.

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// create a select query instance
$query = $client->createSelect();

// you can build your query as usual
$query->setQuery('electronics');
$query->setFields(['id', 'manu', 'name', 'price', 'cat', 'inStock']);
$query->addSort('id', $query::SORT_ASC);

// set up (0-based) pagination, in real life you can set this to a higher number
$resultsPerPage = 5;
$currentPage = 0;
$query->setRows($resultsPerPage);
$query->setStart($currentPage * $resultsPerPage);

// tell Solr to use the CSV response writer
$query->setResponseWriter('csv');

// other options for the CSV response can be set as raw parameters
$query->addParam('csv.mv.separator', '|');

// in real life you can open a stream to append the output to
echo '<textarea rows="20" cols="200">';

do {
    // manually create a request for the query
    $request = $client->createRequest($query);

    // execute the request and get a 'raw' response object
    $response = $client->executeRequest($request);

    // get the response body
    $data = $response->getBody();

    // "append" the output to our "stream"
    echo $data;

    // increment the page for the next iteration
    $query->setStart(++$currentPage * $resultsPerPage);

    // this loop would never terminate if further requests always return the column headers
    $query->addParam('csv.header', false);
} while ('' !== $data);

// close the "stream"
echo '</textarea>';

htmlFooter();
