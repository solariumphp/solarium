<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a spellcheck query instance
$query = $client->createSpellcheck();
$query->setQuery('cort');
$query->setOnlyMorePopular(true);
$query->setCount(10);
$query->setCollate(true);

// this executes the query and returns the result
$resultset = $client->spellcheck($query);

echo '<b>Query:</b> '.$query->getQuery().'<hr/>';

// display results for each term
foreach ($resultset as $term => $termResult) {
    echo '<h3>' . $term . '</h3>';
    echo 'NumFound: '.$termResult->getNumFound().'<br/>';
    echo 'StartOffset: '.$termResult->getStartOffset().'<br/>';
    echo 'EndOffset: '.$termResult->getEndOffset().'<br/>';
    echo 'Suggestions:<br/>';
    foreach ($termResult as $result) {
        echo '- '.$result['word'].'<br/>';
    }

    echo '<hr/>';
}

// display collation
echo 'Collation: '.$resultset->getCollation();

htmlFooter();
