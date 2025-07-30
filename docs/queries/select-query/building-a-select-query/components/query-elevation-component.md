Query Elevation is a Solr component that lets you configure the top results for a given query regardless of the normal Lucene scoring. Elevated query results can be configured in an external XML file or at request time. For more info see <https://solr.apache.org/guide/the-query-elevation-component.html>.

Options
-------

| Name                       | Type    | Default value | Description                                                                                                                                            |
|----------------------------|---------|---------------|--------------------------------------------------------------------------------------------------------------------------------------------------------|
| transformers               | string  | [elevated]    | Comma separated list of transformers to annotate each document. The [elevated] transformer tells whether or not the document was elevated.             |
| enableElevation            | boolean | null          | For debugging it may be useful to see results with and without elevation applied. To get results without elevation, use false.                         |
| forceElevation             | boolean | null          | By default, this component respects the requested sort parameter. To return elevated documents first, use true.                                        |
| exclusive                  | boolean | null          | You can force Solr to return only the results specified in the elevation file by using true.                                                           |
| useConfiguredElevatedOrder | boolean | null          | By default, this component sorts elevated documents in the configured order. To respect the requested sort parameter, use false.                       |
| markExcludes               | boolean | null          | You can include documents that the elevation configuration would normally exclude by using true. The [excluded] transformer is added to each document. |
| elevateIds                 | string  | null          | Comma separated list of documents to elevate. This overrides the elevations _and_ exclusions that are configured for the query in the elevation file.  |
| excludeIds                 | string  | null          | Comma separated list of documents to exclude. This overrides the elevations _and_ exclusions that are configured for the query in the elevation file.  |
| excludeTags                | string  | null          | Comma separated list of filter query tags to exclude. This excludes filter queries from being applied to elevated documents.                           |
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

// get query elevation component
$elevate = $query->getQueryElevation();

// return elevated documents first
$elevate->setForceElevation(true);

// specify documents to elevate and/or exclude if you don't use an elevation file or want to override it at request time
$elevate->setElevateIds(array('VS1GB400C3', 'VDBDB1A16'));
$elevate->setExcludeIds(array('SP2514N', '6H500F0'));

// document transformers can be omitted from the results
//$elevate->clearTransformers();

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

}

htmlFooter();

```
