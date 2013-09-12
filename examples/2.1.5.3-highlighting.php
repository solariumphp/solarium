<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get a select query instance
$query = $client->createSelect();
$query->setQuery('memory');

// get highlighting component and apply settings
$hl = $query->getHighlighting();
$hl->setFields('name, features');
$hl->setSimplePrefix('<b>');
$hl->setSimplePostfix('</b>');

// this executes the query and returns the result
$resultset = $client->select($query);
$highlighting = $resultset->getHighlighting();
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

    echo '</table><br/><b>Highlighting results:</b><br/>';

    // highlighting results can be fetched by document id (the field defined as uniquekey in this schema)
    $highlightedDoc = $highlighting->getResult($document->id);
    if ($highlightedDoc) {
        foreach ($highlightedDoc as $field => $highlight) {
            echo implode(' (...) ', $highlight) . '<br/>';
        }
    }

}

htmlFooter();
