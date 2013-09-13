<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get a suggester query instance
$query = $client->createSuggester();
$query->setQuery('ap ip v'); //multiple terms
$query->setDictionary('suggest');
$query->setOnlyMorePopular(true);
$query->setCount(10);
$query->setCollate(true);

// this executes the query and returns the result
$resultset = $client->suggester($query);

echo '<b>Query:</b> '.$query->getQuery().'<hr/>';

// display results for each term
foreach ($resultset as $term => $termResult) {
    echo '<h3>' . $term . '</h3>';
    echo 'NumFound: '.$termResult->getNumFound().'<br/>';
    echo 'StartOffset: '.$termResult->getStartOffset().'<br/>';
    echo 'EndOffset: '.$termResult->getEndOffset().'<br/>';
    echo 'Suggestions:<br/>';
    foreach ($termResult as $result) {
        echo '- '.$result.'<br/>';
    }

    echo '<hr/>';
}

// display collation
echo 'Collation: '.$resultset->getCollation();

htmlFooter();
