<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect()
    // Unfortunately the /select handler of the techproducts examlpe doesn't contain a spellchecker anymore.
    // Therefore we have to use the /browse handler and turn of velocity by forcing json as response writer.
    ->setHandler('browse')
    ->setResponseWriter(\Solarium\Core\Query\AbstractQuery::WT_JSON)
    // Normally we would use 'spellcheck.q'. But the /browse handler checks 'q'.
    ->setQuery('memori')
    ->setRows(0);

// add spellcheck settings
$spellcheck = $query->getSpellcheck()
    ->setCount(10)
    ->setBuild(true)
    ->setCollate(true)
    ->setExtendedResults(true)
    ->setCollateExtendedResults(true)
    ->setDictionary('default');

// this executes the query and returns the result
$resultset = $client->select($query);
$spellcheckResult = $resultset->getSpellcheck();

echo '<h1>Correctly spelled?</h1>';
if ($spellcheckResult->getCorrectlySpelled()) {
    echo 'yes';
} else {
    echo 'no';
}

echo '<h1>Suggestions</h1>';
foreach ($spellcheckResult as $suggestion) {
    echo 'NumFound: '.$suggestion->getNumFound().'<br/>';
    echo 'StartOffset: '.$suggestion->getStartOffset().'<br/>';
    echo 'EndOffset: '.$suggestion->getEndOffset().'<br/>';
    echo 'OriginalFrequency: '.$suggestion->getOriginalFrequency().'<br/>';
    foreach ($suggestion->getWords() as $word) {
        echo '-----<br/>';
        echo 'Frequency: '.$word['freq'].'<br/>';
        echo 'Word: '.$word['word'].'<br/>';
    }

    echo '<hr/>';
}

$collations = $spellcheckResult->getCollations();
echo '<h1>Collations</h1>';
foreach ($collations as $collation) {
    echo 'Query: '.$collation->getQuery().'<br/>';
    echo 'Hits: '.$collation->getHits().'<br/>';
    echo 'Corrections:<br/>';
    foreach ($collation->getCorrections() as $input => $correction) {
        echo $input . ' => ' . $correction .'<br/>';
    }
    echo '<hr/>';
}

htmlFooter();
