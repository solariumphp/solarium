This facet type allows you to supply an arbitrary query (normal query syntax) to count the number of results of. This query is not affected by the 'main' query. Filterqueries do affect this count, but they can be excluded.

Options
-------

The options below can be set as query option values, but also by using the set/get methods. See the API docs for all available methods.

Only the facet-type specific options are listed. See [Facetset component](V3:Facetset_component "wikilink") for the option shared by all facet types.

| Name  | Type   | Default value | Description                     |
|-------|--------|---------------|---------------------------------|
| query | string | \*:\*         | The query to use for the count. |
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

// get the facetset component
$facetSet = $query->getFacetSet();

// create a facet query instance and set options
$facetSet->createFacetQuery('stock')->setQuery('inStock: true');

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by solr
echo 'NumFound: '.$resultset->getNumFound();

// display facet query count
$count = $resultset->getFacetSet()->getFacet('stock')->getValue();
echo '<hr/>Facet query count : ' . $count;

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
