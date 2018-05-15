<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get a select query instance
$query = $client->createSelect();
$query->setQuery('ipod');

// get query elevation component
$elevate = $query->getQueryElevation();

// return elevated documents first
$elevate->setForceElevation(true);

// specify documents to elevate and/or exclude if you don't use elevate.xml or want to override it at runtime
$elevate->setElevateIds(array('doc1', 'doc2'));
$elevate->setExcludeIds(array('doc3', 'doc4'));

// document transformers can be omitted from the results
//$elevate->clearTransformers();

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

}

htmlFooter();
