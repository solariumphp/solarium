For a description of Solr spellcheck (also referred to as 'query suggest') see <https://solr.apache.org/guide/spell-checking.html>.

The `setQuery()` method of this component supports [placeholders](V3:Placeholders "wikilink").

Options
-------

| Name                    | Type    | Default value | Description                                                                            |
|-------------------------|---------|---------------|----------------------------------------------------------------------------------------|
| query                   | string  | null          | Query to spellcheck                                                                    |
| build                   | boolean | null          | Build the spellcheck dictionary?                                                       |
| reload                  | boolean | null          | Reload the dictionary?                                                                 |
| dictionary              | string  | null          | The name of the dictionary to use                                                      |
| count                   | int     | null          | The maximum number of suggestions to return                                            |
| onlymorepopular         | boolean | null          | Only return suggestions that result in more hits for the query than the existing query |
| alternativetermcount    | int     | null          |                                                                                        |
| extendedresults         | boolean | null          |                                                                                        |
| collate                 | boolean | null          |                                                                                        |
| maxcollations           | int     | null          |                                                                                        |
| maxcollationtries       | string  | null          |                                                                                        |
| maxcollationevaluations | int     | null          |                                                                                        |
| collateextendedresults  | string  | null          |                                                                                        |
| accuracy                | float   | null          |                                                                                        |
||

Collate params
--------------

Using the API method setCollateParam($param, $value) you can set any collate params you need. For more info see <https://solr.apache.org/guide/spell-checking.html#spell-check-parameters>.

Example
-------

```php
<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect();
$query->setRows(0);

// add spellcheck settings
$spellcheck = $query->getSpellcheck();
$spellcheck->setQuery('tes');
$spellcheck->setCount(10);
$spellcheck->setBuild(true);
$spellcheck->setCollate(true);
$spellcheck->setExtendedResults(true);
$spellcheck->setCollateExtendedResults(true);

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

```
