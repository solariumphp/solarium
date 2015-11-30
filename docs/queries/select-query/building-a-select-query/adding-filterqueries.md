A filterquery can be used to restrict the set of documents for your query, without affecting score. They also have the benefit of being cached separately by Solr, so reusing filterqueries is very efficient.

Options
-------

The options below can be set as query option values, but also by using the set/get methods. See the API docs for all available methods.

| Name  | Type           | Default value | Description                                                                                                                                      |
|-------|----------------|---------------|--------------------------------------------------------------------------------------------------------------------------------------------------|
| key   | string         | null          | This value is used as the key for the filterquery in the select query object. Kind of a unique-id for filterqueries.                             |
| tag   | string / array | empty array   | Tags for excluding filterqueries in facets. A single filterquery may have multiple tags and a single tag may be used for multiple filterqueries. |
| query | string         | null          | The query to use as filter on the set of documents.                                                                                              |
||

Examples
--------

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get a select query instance
$query = $client->createSelect();

// create a filterquery
$query->createFilterQuery('maxprice')->setQuery('price:[1 TO 300]');

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by solr
echo 'NumFound: '.$resultset->getNumFound();

// show documents using the resultset iterator
foreach ($resultset as $document) {

    echo '<hr/><table>';
    echo '<tr><th>id</th><td>' . $document->id . '</td></tr>';
    echo '<tr><th>name</th><td>' . $document->name . '</td></tr>';
    echo '<tr><th>price</th><td>' . $document->price . '</td></tr>';
    echo '</table>';
}

htmlFooter();

```
