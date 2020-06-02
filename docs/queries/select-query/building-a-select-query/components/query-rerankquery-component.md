Query Re-Ranking allows you to run a simple query (A) for matching documents and then re-rank the top N documents using the scores from a more complex query (B).

Since the more costly ranking from query B is only applied to the top N documents, it will have less impact on performance then just using the complex query B by itself. The trade off is that documents which score very low using the simple query A may not be considered during the re-ranking phase, even if they would score very highly using query B.

For more info see <https://lucene.apache.org/solr/guide/query-re-ranking.html>.

Options
-------

| Name   | Type    | Default value | Description                                                                                                                                                                                                                                        |
|--------|---------|---------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| query  | string  | null          | The query string for your complex ranking query.                                                                                                                                                                                                   |
| docs   | integer | 200           | The number of top N documents from the original query that should be re-ranked. This number will be treated as a minimum, and may be increased internally automatically in order to rank enough documents to satisfy the query (i.e., start+rows). |
| weight | float   | 2.0           | A multiplicative factor that will be applied to the score from the reRankQuery for each of the top matching documents, before that score is added to the original score.                                                                           |
||

Example
-------

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect();
$query->setQuery('electronics');

// get rerankquery component
$rerank = $query->getReRankQuery();

// boost documents that have a popularity of 10
$rerank->setQuery('popularity:10');

// set the "boost factor"
$rerank->setWeight(3);

// this executes the query and returns the result
$resultset = $client->select($query);
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

}

htmlFooter();

```
