A MoreLikeThis (MLT) query is designed to generate information about "similar" documents using the MoreLikeThis functionality provided by Lucene. It supports faceting, paging, and filtering using CommonQueryParameters.

This query uses the [Solr MoreLikeThis Handler](https://solr.apache.org/guide/morelikethis.html) that specifically returns MLT results. Alternatively you can use the [MLT component](select-query/building-a-select-query/components/morelikethis-component.md) for the select query.

Building a MLT query
--------------------

See the example code below.

**Available options:**

| Name                               | Type    | Default value                 | Description                                                                                                                                                                      |
|------------------------------------|---------|-------------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| handler                            | string  | select                        | Name of the Solr request handler to use, without leading or trailing slashes                                                                                                     |
| resultclass                        | string  | Solarium\_Result\_Select      | Classname for result. If you set a custom classname make sure the class is readily available (or through autoloading)                                                            |
| documentclass                      | string  | Solarium\_Document\_ReadWrite | Classname for documents in the resultset. If you set a custom classname make sure the class is readily available (or through autoloading)                                        |
| query                              | string  | \*:\*                         | Query to execute                                                                                                                                                                 |
| start                              | int     | 0                             | Start position (offset) in the complete Solr query resultset, to paginate big resultsets.                                                                                        |
| rows                               | int     | 10                            | Number of rows to fetch, starting from the 'start' (offset) position. It's a limit, you might get less.                                                                          |
| fields                             | string  | \*,score                      | Comma separated list of fields to fetch from Solr. There are two special values: '\*' meaning 'all fields' and 'score' to also fetch the Solr document score value.              |
| sort                               | array   | empty array                   | Array with sort field as key and sort order as values. Multiple entries possible, they are used in the order of the array. Example: array('price' =&gt; 'asc')                   |
| stream                             | boolean | false                         | Set to true to post query content instead of using the URL param                                                                                                                 |
| matchinclude                       | boolean | false                         | Specifies whether or not the response should include the matched document. If set to false, the response will look like a normal select response.                                |
| matchoffset                        | int     | 0                             | Specifies an offset into the main query search results to locate the document on which the MoreLikeThis query should operate.                                                    |
| interestingTerms                   | string  | none                          | Controls how the handler presents the "interesting" terms. Must be one of: none, list, details.                                                                                  |
| mltfields                          | string  | null                          | The fields to use for similarity. NOTE: if possible, these should have a stored TermVector. Separate multiple fields with commas.                                                |
| minimumtermfrequency               | int     | null                          | Minimum Term Frequency - the frequency below which terms will be ignored in the source doc.                                                                                      |
| mimimumdocumentfrequency           | int     | null                          | Minimum Document Frequency - the frequency at which words will be ignored which do not occur in at least this many docs.                                                         |
| maximumdocumentfrequency           | int     | null                          | Maximum Document Frequency - the frequency at which words will be ignored which occur in more than this many docs.                                                               |
| maximumdocumentfrequencypercentage | int     | null                          | Maximum Document Frequency Percentage - a relative ratio at which words will be ignored which occur in more than this percentage of the docs in the index.                       |
| minimumwordlength                  | int     | null                          | Minimum word length below which words will be ignored.                                                                                                                           |
| maximumwordlength                  | int     | null                          | Maximum word length above which words will be ignored.                                                                                                                           |
| maximumqueryterms                  | int     | null                          | Maximum number of query terms that will be included in any generated query.                                                                                                      |
| maximumnumberoftokens              | int     | null                          | Maximum number of tokens to parse in each example doc field that is not stored with TermVector support.                                                                          |
| boost                              | boolean | null                          | If true the query will be boosted by the interesting term relevance.                                                                                                             |
| queryfields                        | string  | null                          | Query fields and their boosts using the same format as that used in DisMaxQParserPlugin. These fields must also be specified in mltfields. Separate multiple fields with commas. |
||

Executing a MLT query
---------------------

Use the `moreLikeThis` method of the client to execute the query object. See the example code below.

Result of a MLT query
---------------------

The result of a MLT query shares the features of the select query result. On top of that the following is added:

### Interestingterms

This will show what "interesting" terms (the top TF/IDF terms) are used for the MoreLikeThis query.

The format of the interesting terms depends on the value set for interestingterms in the query.

* `list`: The terms are returned as an array of strings.
* `details`: Each term is an array key associated with the boost value used by Solr.
    Unless you set boost to `true`, this will be `1.0` for every term.
* `none`: The terms aren't available with the resultset and an exception is thrown if you try and fetch them anyway.

### Match

The document used for matching MLT results. Only available if matchinclude was set to `true` in the query.

Setting up the MLT handler
--------------------------

The examples below assume an MLT handler is set up at `/mlt`. Solr's example configsets don't include one by default.

### In `solrconfig.xml`

```xml
<requestHandler name="/mlt" class="solr.MoreLikeThisHandler" />
```

### Through the Config API

```php
<?php

require_once(__DIR__.'/init.php');


$client = new Solarium\Client($adapter, $eventDispatcher, $config);

$query = $client->createApi([
    'version' => Solarium\Core\Client\Request::API_V1,
    'handler' => 'techproducts/config',
    'method' => Solarium\Core\Client\Request::METHOD_POST,
    'rawdata' => json_encode([
        'add-requesthandler' => [
            'name' => '/mlt',
            'class' => 'solr.MoreLikeThisHandler',
        ],
    ]),
]);

$client->execute($query);
```

Example
-------

```php
<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a morelikethis query instance
$query = $client->createMoreLikethis();

// query a document you want similar documents for
$query->setQuery('id:SP2514N')
    ->setMltFields('manu,cat')
    ->setMinimumDocumentFrequency(1)
    ->setMinimumTermFrequency(1)
    ->setInterestingTerms('details')
    ->setBoost(true)
    ->setMatchInclude(true)
    ->createFilterQuery('stock')->setQuery('inStock:true');

// this executes the query and returns the result
$resultset = $client->moreLikeThis($query);

echo 'Document used for matching:<br/><table>';
foreach ($resultset->getMatch() as $field => $value) {
    // this converts multivalue fields to a comma-separated string
    if (is_array($value)) {
        $value = implode(', ', $value);
    }

    echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
}
echo '</table><hr/>';

// display the total number of MLT documents found by Solr
echo 'Number of MLT matches found: '.$resultset->getNumFound().'<br/><br/>';

// display the "interesting" terms for the query
echo 'Interesting terms with the boost value used:';
echo '<ul>';
foreach ($resultset->getInterestingTerms() as $term => $boost) {
    echo '<li>'.$term.' (boost='.$boost.')</li>';
}
echo '</ul>';

echo '<b>Listing of matched docs:</b>';

// show MLT documents using the resultset iterator
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

Matching against supplied text
------------------------------

Instead of querying the index for a document to match against, you can also find
similar documents based on supplied text.

In this case, there is no document to include when matchinclude is set to `true`.

### Example

This example assumes the `/mlt` handler is already set up ([see above](#setting-up-the-mlt-handler)).

```php
<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a morelikethis query instance
$query = $client->createMoreLikeThis();

// supply text you want similar documents for
$text = <<<EOT
Samsung SpinPoint P120 SP2514N - hard drive - 250 GB - ATA-133
7200RPM, 8MB cache, IDE Ultra ATA-133, NoiseGuard, SilentSeek technology, Fluid Dynamic Bearing (FDB) motor
EOT;

$query->setQuery($text);
$query->setQueryStream(true);
$query->setMltFields('name,features');
$query->setMinimumDocumentFrequency(1);
$query->setMinimumTermFrequency(1);
$query->createFilterQuery('stock')->setQuery('inStock:true');
$query->setInterestingTerms('details');
$query->setBoost(true);

// this executes the query and returns the result
$resultset = $client->moreLikeThis($query);

// display the total number of MLT documents found by Solr
echo 'Number of MLT matches found: '.$resultset->getNumFound().'<br/><br/>';

// display the "interesting" terms for the query
echo 'Interesting terms with the boost value used:';
echo '<ul>';
foreach ($resultset->getInterestingTerms() as $term => $boost) {
    echo '<li>'.$term.' (boost='.$boost.')</li>';
}
echo '</ul>';

echo '<b>Listing of matched docs:</b>';

// show MLT documents using the resultset iterator
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
