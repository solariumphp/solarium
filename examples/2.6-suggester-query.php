<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a suggester query instance
$query = $client->createSuggester();
$query->setQuery('c');
$query->setDictionary('mySuggester');
$query->setBuild(true);
$query->setCount(10);

// this executes the query and returns the result
$resultset = $client->suggester($query);

echo '<b>Query:</b> '.$query->getQuery().'<hr/>';

// display results for each term
foreach ($resultset as $dictionary => $terms) {
    echo '<h3>' . $dictionary . '</h3>';
    foreach ($terms as $term => $termResult) {
        echo '<h4>' . $term . '</h4>';
        echo 'NumFound: '.$termResult->getNumFound().'<br/>';
        foreach ($termResult as $result) {
            echo '- '.$result['term'].'<br/>';
        }
    }

    echo '<hr/>';
}

htmlFooter();
