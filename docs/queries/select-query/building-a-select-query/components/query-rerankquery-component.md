Query Re-Ranking allows you to run a simple query _A_ for matching documents and then re-rank the top _N_ documents using the scores from a more complex query _B_.

Since the more costly ranking from query _B_ is only applied to the top _N_ documents, it will have less impact on performance then just using the complex query _B_ by itself. The trade off is that documents which score very low using the simple query _A_ may not be considered during the re-ranking phase, even if they would score very highly using query _B_.

For more info see <https://solr.apache.org/guide/query-re-ranking.html>.

Options
-------

| Name      | Type    | Default value | Description                                                                                                                                                                                                                                          |
|-----------|---------|---------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| query     | string  | null          | The query string for your complex ranking query.                                                                                                                                                                                                     |
| docs      | integer | 200           | The number of top _N_ documents from the original query that should be re-ranked. This number will be treated as a minimum, and may be increased internally automatically in order to rank enough documents to satisfy the query (i.e., start+rows). |
| weight    | float   | 2.0           | A multiplicative factor that will be applied to the score from the reRankQuery for each of the top matching documents, before that score is added to the original score.                                                                             |
| scale     | string  | null          | Scales the rerank scores between min and max values. The format of this parameter value is `min-max` where min and max are positive integers.                                                                                                        |
| mainscale | string  | null          | Scales the main query scores between min and max values. The format of this parameter value is `min-max` where min and max are positive integers.                                                                                                    |
| operator  | string  | add           | The operator determines whether the re-ranked score is added to, multiplied by, or replaces the original score. Use one of the `OPERATOR_*` class constants as value.                                                                                |
||

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
$query->setQuery('electronics');

// get rerankquery component
$rerank = $query->getReRankQuery();

// boost documents that have a popularity of 10
$rerank->setQuery('popularity:10');

// set the "boost factor"
$rerank->setWeight(3);

// set the scale for the rerank scores
$rerank->setScale('0-1');

// set the scale for the main query scores
$rerank->setMainScale('0-1');

// multiply the original score by the re-ranked score
$rerank->setOperator($rerank::OPERATOR_MULTIPLY);

// this executes the query and returns the result
$resultset = $client->select($query);
// display the total number of documents found by Solr
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

    echo '</table>';
}

htmlFooter();

```
