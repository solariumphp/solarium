A MoreLikeThis (MLT) query is designed to generate information about "similar" documents using the MoreLikeThis functionality provided by Lucene. It supports faceting, paging, and filtering using CommonQueryParameters.

This query uses the [Solr MoreLikeThis Handler](http://wiki.apache.org/solr/MoreLikeThisHandler) that specifically returns MLT results. Alternatively you can use the [MLT component](V2:MoreLikeThis_component "wikilink") for the select query.

Building a MLT query
--------------------

See the example code below.

**Available options:**

| Name                     | Type    | Default value                 | Description                                                                                                                                                         |
|--------------------------|---------|-------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| handler                  | string  | select                        | Name of the Solr request handler to use, without leading or trailing slashes                                                                                        |
| resultclass              | string  | Solarium\_Result\_Select      | Classname for result. If you set a custom classname make sure the class is readily available (or through autoloading)                                               |
| documentclass            | string  | Solarium\_Document\_ReadWrite | Classname for documents in the resultset. If you set a custom classname make sure the class is readily available (or through autoloading)                           |
| query                    | string  | \*:\*                         | Query to execute                                                                                                                                                    |
| start                    | int     | 0                             | Start position (offset) in the complete Solr query resultset, to paginate big resultsets.                                                                           |
| rows                     | integer | 10                            | Number of rows to fetch, starting from the 'start' (offset) position. It's a limit, you might get less.                                                             |
| fields                   | string  | -   ,score                    | Comma separated list of fields to fetch from Solr. There are two special values: '\*' meaning 'all fields' and 'score' to also fetch the Solr document score value. |
| sort                     | array   | empty array                   | Array with sort field as key and sort order as values. Multiple entries possible, they are used in the order of the array. Example: array('price' =&gt; 'asc')      |
| stream                   | boolean | false                         | Set to true to post query content instead of using the URL param                                                                                                    |
| interestingTerms         | string  | none                          | Must be one of: none, list, details                                                                                                                                 |
| matchinclude             | boolean | false                         |                                                                                                                                                                     |
| mltfields                | string  | none                          | The fields to use for similarity. NOTE: if possible, these should have a stored TermVector                                                                          |
| minimumtermfrequency     | int     | none                          |                                                                                                                                                                     |
| minimumdocumentfrequency | int     | none                          |                                                                                                                                                                     |
| minimumwordlength        | int     | none                          |                                                                                                                                                                     |
| maximumwordlength        | int     | none                          |                                                                                                                                                                     |
| maximumqueryterms        | int     | none                          |                                                                                                                                                                     |
| maximumnumberoftokens    | int     | none                          |                                                                                                                                                                     |
| boost                    | boolean | none                          | If true the query will be boosted by the interesting term relevance                                                                                                 |
| queryfields              | string  | none                          | Query fields and their boosts using the same format as that used in DisMaxQParserPlugin. These fields must also be specified in fields.                             |
||

Executing a MLT query
---------------------

Use the `moreLikeThis` method of the client to execute the query object. See the example code below.

Result of a MLT query
---------------------

The result of a mlt query shares the features of the select query result. On top of that the following is added:

### Interestingterms

This will show what "interesting" terms are used for the MoreLikeThis query.

### Match

Get matched documents. Only available if matchinclude was set to true in the query.

Example
-------

```php
<?php

require(__DIR__.'/init.php');
use Solarium\Client;

htmlHeader();

// create a client instance
$client = new Client($config);

// get a morelikethis query instance
$query = $client->createMoreLikeThis();

$query->setQuery('id:SP2514N');
$query->setMltFields('manu,cat');
$query->setMinimumDocumentFrequency(1);
$query->setMinimumTermFrequency(1);
$query->createFilterQuery('stock')->setQuery('inStock:true');
$query->setInterestingTerms('details');
$query->setMatchInclude(true);

// this executes the query and returns the result
$resultset = $client->select($query);

echo 'Document used for matching:<br/><table>';
foreach ($resultset->getMatch() as $field => $value) {
    // this converts multivalue fields to a comma-separated string
    if (is_array($value)) {
        $value = implode(', ', $value);
    }

    echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
}
echo '</table><hr/>';

// display the total number of MLT documents found by solr
echo 'Number of MLT matches found: '.$resultset->getNumFound().'<br/><br/>';
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
