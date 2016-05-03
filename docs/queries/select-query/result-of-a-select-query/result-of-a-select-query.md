The result of an update query is an instance of `Solarium_Result_Select`

Data
----

### Status

Solr status code. This is not the HTTP status code! The normal value for success is 0. In case of an error an exception will be thrown. Only available if your Solr server sends headers (omitHeader=false)

### Querytime

Solr index query time. This doesn't include things like the HTTP response time. Only available if your Solr server sends headers (omitHeader=false)

### NumFound

Total number of documents that matched the query. This is not necessarily the same as the number of document in the resultset, depending on you query settings!

### Documents

The documents returned be Solr, parsed into documentClass instances. If your query has a limit of 0 or returned no results this can be an empty set.

### Components

For component results see the next sections of this manual.

Interfaces
----------

This resultclass implements the `Iterator` and `Countable` interfaces.

The iterator iterates the documentset. So you can easily loop all documents by using a `foreach`.

The countable interface returns the number of documents in this resultset. This is only the number of fetched documents! If you want the query result count you must use the numFound value.

Example
-------

A basic usage example: 
```php
<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get a select query instance
$query = $client->createQuery($client::QUERY_SELECT);

// this executes the query and returns the result
$resultset = $client->execute($query);

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

    echo '</table>';
}

htmlFooter();

```
