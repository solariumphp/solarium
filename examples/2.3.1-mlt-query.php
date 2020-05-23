<?php

require(__DIR__.'/init.php');
use Solarium\Client;

htmlHeader();

// create a client instance
$client = new Client($adapter, $eventDispatcher, $config);

// get a morelikethis query instance
$query = $client->createSelect()
    // Unfortunately the /mlt handler of the techproducts examlpe doesn't exist anymore.
    // Therefore we have to use the /browse handler and turn of velocity by forcing json as response writer.
    ->setHandler('browse')
    ->setResponseWriter(\Solarium\Core\Query\AbstractQuery::WT_JSON);

$query->getMoreLikeThis()
    ->setFields('manu,cat')
    ->setMinimumDocumentFrequency(1)
    ->setMinimumTermFrequency(1)
    ->setInterestingTerms('details')
    ->setMatchInclude(true);

$query->setQuery('id:SP2514N')
    ->createFilterQuery('stock')->setQuery('inStock:true');

// this executes the query and returns the result
$resultset = $client->select($query);

echo 'Documents used for matching:<br/>';
// show documents using the resultset iterator
foreach ($resultset as $document) {

    echo '<table>';

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

echo '<hr/>';

$mlt = $resultset->getMoreLikeThis();

// display the total number of MLT documents found by solr
echo 'Number of MLT matches found: '.$resultset->getNumFound().'<br/><br/>';
echo '<b>Listing of matched docs:</b>';

// show MLT documents using the resultset iterator
foreach ($mlt as $results) {
    foreach ($results as $document) {
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
}

htmlFooter();
