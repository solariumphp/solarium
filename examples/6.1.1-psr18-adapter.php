<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a PSR-18 adapter instance
$httpClient = new Http\Adapter\Guzzle6\Client();
$factory = new Nyholm\Psr7\Factory\Psr17Factory();
$adapter = new Solarium\Core\Client\Adapter\Psr18Adapter($httpClient, $factory, $factory);

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect();

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by solr
echo 'NumFound: '.$resultset->getNumFound();

// show documents using the resultset iterator
foreach ($resultset as $document) {

    echo '<hr/><table>';

    // the documents are also iterable, to get all fields
    foreach ($document as $field => $value) {
        // this converts multivalue fields to a comma-separated string
        if (is_array($value)) {
            $value = implode(', ', $value);
        }

        echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
    }

    echo '</table>';
}

htmlFooter();
