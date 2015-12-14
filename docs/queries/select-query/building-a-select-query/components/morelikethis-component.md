The morelikethis component can be used if you want to retrieve similar documents for your query results. This component uses morelikethis in the standardrequesthandler, not the standalone morelikethis handler. For more info see <http://wiki.apache.org/solr/MoreLikeThis>

Options
-------

| Name                     | Type    | Default value | Description                                                                                                                                                                  |
|--------------------------|---------|---------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| fields                   | string  | null          | The fields to use for similarity. NOTE: if possible, these should have a stored TermVector. Separate multiple fields with commas.                                            |
| minimumtermfrequency     | int     | null          | Minimum Term Frequency - the frequency below which terms will be ignored in the source doc.                                                                                  |
| mimimumdocumentfrequency | int     | null          | Minimum Document Frequency - the frequency at which words will be ignored which do not occur in at least this many docs.                                                     |
| minimumwordlength        | int     | null          | Minimum word length below which words will be ignored.                                                                                                                       |
| maximumwordlength        | int     | null          | Maximum word length above which words will be ignored.                                                                                                                       |
| maximumqueryterms        | int     | null          | Maximum number of query terms that will be included in any generated query.                                                                                                  |
| maximumnumberoftokens    | int     | null          | Maximum number of tokens to parse in each example doc field that is not stored with TermVector support.                                                                      |
| boost                    | boolean | null          | If true the query will be boosted by the interesting term relevance.                                                                                                         |
| queryfields              | string  | null          | Query fields and their boosts using the same format as that used in DisMaxQParserPlugin. These fields must also be specified in fields.Separate multiple fields with commas. |
| count                    | int     | null          | The number of similar documents to return for each result                                                                                                                    |
||

Example
-------

```php
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

```
