<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect();
$query->setQuery('electronics');

// set a handler that is configured with an elevator component in solrconfig.xml (or add it to your default handler)
$query->setHandler('elevate');

// get query elevation component
$elevate = $query->getQueryElevation();

// return elevated documents first
$elevate->setForceElevation(true);

// specify documents to elevate and/or exclude if you don't use an elevation file or want to override it at request time
$elevate->setElevateIds(array('VS1GB400C3', 'VDBDB1A16'));
$elevate->setExcludeIds(array('SP2514N', '6H500F0'));

// document transformers can be omitted from the results
//$elevate->clearTransformers();

// this executes the query and returns the result
$resultset = $client->select($query);
// display the total number of documents found by Solr
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
