<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get a select query instance
$query = $client->createSelect();

// add a query and morelikethis settings (using fluent interface)
$query->setQuery('apache')
      ->getMoreLikeThis()
      ->setFields('manu,cat')
      ->setMinimumDocumentFrequency(1)
      ->setMinimumTermFrequency(1);

// this executes the query and returns the result
$resultset = $client->select($query);
$mlt = $resultset->getMoreLikeThis();

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

    echo '</table><br/><b>MLT results:</b><br/>';

    // mlt results can be fetched by document id (the field defined as uniquekey in this schema)
    $mltResult = $mlt->getResult($document->id);
    if ($mltResult) {
        echo 'Max score: '.$mltResult->getMaximumScore().'<br/>';
        echo 'NumFound: '.$mltResult->getNumFound().'<br/>';
        echo 'Num. fetched: '.count($mltResult).'<br/>';
        foreach ($mltResult as $mltDoc) {
            echo 'MLT result doc: '. $mltDoc->name . ' (id='. $mltDoc->id . ')<br/>';
        }
    } else {
        echo 'No MLT results';
    }

}

htmlFooter();
