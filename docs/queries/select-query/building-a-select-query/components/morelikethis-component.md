The MoreLikeThis component can be used if you want to retrieve similar documents for your query results. This component uses MoreLikeThis in the StandardRequestHandler, not the standalone MoreLikeThisHandler. For more info see <https://solr.apache.org/guide/morelikethis.html>.

Options
-------

| Name                               | Type    | Default value | Description                                                                                                                                                                   |
|------------------------------------|---------|---------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| fields                             | string  | null          | The fields to use for similarity. NOTE: if possible, these should have a stored TermVector. Separate multiple fields with commas.                                             |
| minimumtermfrequency               | int     | null          | Minimum Term Frequency - the frequency below which terms will be ignored in the source doc.                                                                                   |
| mimimumdocumentfrequency           | int     | null          | Minimum Document Frequency - the frequency at which words will be ignored which do not occur in at least this many docs.                                                      |
| maximumdocumentfrequency           | int     | null          | Maximum Document Frequency - the frequency at which words will be ignored which occur in more than this many docs.                                                            |
| maximumdocumentfrequencypercentage | int     | null          | Maximum Document Frequency Percentage - a relative ratio at which words will be ignored which occur in more than this percentage of the docs in the index.                    |
| minimumwordlength                  | int     | null          | Minimum word length below which words will be ignored.                                                                                                                        |
| maximumwordlength                  | int     | null          | Maximum word length above which words will be ignored.                                                                                                                        |
| maximumqueryterms                  | int     | null          | Maximum number of query terms that will be included in any generated query.                                                                                                   |
| maximumnumberoftokens              | int     | null          | Maximum number of tokens to parse in each example doc field that is not stored with TermVector support.                                                                       |
| boost                              | boolean | null          | If true the query will be boosted by the interesting term relevance.                                                                                                          |
| queryfields                        | string  | null          | Query fields and their boosts using the same format as that used in DisMaxQParserPlugin. These fields must also be specified in fields. Separate multiple fields with commas. |
| count                              | int     | null          | The number of similar documents to return for each result.                                                                                                                    |
| interestingTerms                   | string  | null          | Controls how the component presents the "interesting" terms. Must be one of: none, list, details.                                                                             |
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

// add a query and morelikethis settings (using fluent interface)
$query->setQuery('apache')
      ->getMoreLikeThis()
      ->setFields('manu,cat')
      ->setMinimumDocumentFrequency(1)
      ->setMinimumTermFrequency(1)
      ->setInterestingTerms('list');

// this executes the query and returns the result
$resultset = $client->select($query);
$mlt = $resultset->getMoreLikeThis();

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

    echo '</table><br/><b>MLT results:</b><br/>';

    // MLT results can be fetched by document id (the field defined as uniquekey in this schema)
    $mltResult = $mlt->getResult($document->id);
    if ($mltResult) {
        echo 'Max score: '.$mltResult->getMaximumScore().'<br/>';
        echo 'NumFound: '.$mltResult->getNumFound().'<br/>';
        echo 'Num. fetched: '.count($mltResult).'<br/>';
        foreach ($mltResult as $mltDoc) {
            echo 'MLT result doc: '. $mltDoc->name . ' (id='. $mltDoc->id . ')<br/>';
        }
        // available since Solr 8.2 if the query wasn't distributed
        if (null !== $interestingTerms = $mlt->getInterestingTerm($document->id)) {
            echo 'MLT interesting terms: '.implode(', ', $interestingTerms).'<br/>';
        }
    } else {
        echo 'No MLT results';
    }

}

htmlFooter();

```
