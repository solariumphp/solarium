<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect();
$query->setQuery('electronics');

// get rerankquery component
$rerank = $query->getReRankQuery();

// boost documents that have a popularity of 10
$rerank->setQuery('popularity:10');

// set the "boost factor"
$rerank->setWeight(3);

// set the scale for the rerank scores
$rerank->setScale('0-1');

// set the scale for the main query scores
$rerank->setMainScale('0-1');

// multiply the original score by the re-ranked score
$rerank->setOperator($rerank::OPERATOR_MULTIPLY);

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
