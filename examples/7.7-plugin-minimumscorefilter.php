<?php
require(__DIR__.'/init.php');

htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// enable the plugin and get a query instance
$filter = $client->getPlugin('minimumscorefilter');
$query = $client->createQuery($filter::QUERY_TYPE);
$query->setQuery('a');
$query->setFields(array('id'));
$query->setFilterRatio(.5);
$query->setFilterMode($query::FILTER_MODE_MARK);

// this executes the query and returns the result
$resultset = $client->execute($query);

// display the total number of documents found by solr and the maximum score
echo 'NumFound: '.$resultset->getNumFound();
echo '<br/>MaxScore: '.$resultset->getMaxScore();

// show documents using the resultset iterator
foreach ($resultset as $document) {

    // by setting the FILTER_MARK option we get a special method to test each document
    if ($document->markedAsLowScore()) {
        echo '<hr/><b>MARKED AS LOW SCORE</b><table>';
    } else {
        echo '<hr/><table>';
    }

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
