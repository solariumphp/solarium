Results of the MoreLikeThis component are included with the query resultset, but not directly coupled to the resulting documents.
Just like in the Solr response data it is a separate dataset. However the MoreLikeThis resultset has a method to easily access
MLT results for a specific document by its id (the `uniqueKey` defined in your schema).

Starting with Solr 8.2, you can also include the "interesting" terms (the top TF/IDF terms) for the query as another separate
dataset if you set interestingterms to `list` or `details`. These too can be easily accessed by document id.

In the example code below you can see it in use. For each document the MLT result and interesting terms are fetched.

This result is an instance of `Solarium\Component\Result\MoreLikeThis\Result` and contains all similar documents for a result.
So, as also described in the Solr MLT wiki page, in this case the name MoreLikeThese might be better.

The format of the interesting terms depends on the value set for interestingterms in the MLT component.

* `list`: The terms are returned as an array of strings.
* `details`: Each term is an array key associated with the boost value used by Solr.
    Unless you set boost to `true`, this will be `1.0` for every term.
* `none`: The terms aren't available with the resultset and an exception is thrown if you try and fetch them anyway.

In order to get interesting terms with the MLT component, the query must be sent to a single shard and limited to that shard only.

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
