This facet type allows you to count the number of occurrences of a term in a specific field.

Options
-------

The options below can be set as query option values, but also by using the set/get methods. See the API docs for all available methods.

Only the facet-type specific options are listed. See [FacetSet component](facetset-component.md) for the options shared by all facet types.

| Name        | Type   | Default value | Description                                                                                         |
|-------------|--------|---------------|-----------------------------------------------------------------------------------------------------|
| field       | string | id            | The index field for the facet.                                                                      |
| local_terms | string | null          | Limit field facet to specified terms. Specify a comma separated list. Use `\,` for a literal comma. |
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

// get the facetset component
$facetSet = $query->getFacetSet();

// create a facet field instance and set options
$facetSet->createFacetField('stock')->setField('inStock');

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by Solr
echo 'NumFound: '.$resultset->getNumFound();

// display facet counts
echo '<hr/>Facet counts for field "inStock":<br/>';
$facet = $resultset->getFacetSet()->getFacet('stock');
foreach ($facet as $value => $count) {
    echo $value . ' [' . $count . ']<br/>';
}

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
