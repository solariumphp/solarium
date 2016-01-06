Results of the Highlighting component are included with the query resultset, but not directly coupled to the resulting documents. Just like in the Solr response data it is a separate dataset. However the Highlighting resultset has a method to easily access HLT results for a specific document by id (the id depends on your schema).

In the example code below you can see it in use. For each document the HLT result is fetched. This result has an array of highlighted fields (the fields you chose to highlight in your query that also had a match for this doc).

Example
-------

```php
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

```
