Solarium documentation
=================


Solarium is a Solr client library for PHP. It is developed with these goals in mind:

-  Relieve developers of the ‘raw communication’ with Solr, ie. setting params, building strings, hiding all this with an easy to use API, allowing you to focus on business logic.
-  Allow for reuse, for instance a query can be extended to modify it
-  Be flexible. For instance the query and result models are not tied to a specific Solr client implementation. There are multiple Solr Client adapters for Solr communication. All models can be extended by your own implementation if needed and a plugin system is available.
-  Be usable in any PHP application. No dependencies on other frameworks. Solarium tries to follow the Symfony 2 standard and integrates nicely with SF2, but doesn’t in rely on it. You can use Solarium just as easily in Zend Framework or any other PHP framework.
-  Accurately model Solr. For instance the updating of a Solr index. Most clients have separate add, delete and commit methods that also issue separate requests. But Solr actually has an update handler that supports all those actions in a single request. The model should reflect this while keeping it easy to use.
-  Find a good balance between nice and feature-rich code and performance. A library/framework for general use will never be as fast as a custom implementation that only contains the bare minimum for your use case. But the performance difference between the two should be at a reasonable level. And because of the dynamic nature of PHP the models can’t be too extensive, yet they should not be over-simplified.
-  Only implement basic functionality in the standard models. All additional functionality should be in separate code that only gets loaded when used. This benefits performance, but also helps to prevent classes with huge APIs. The query components and plugins are a good example.

Example code
------------

This is a basic example that executes a simple select query with one facet and displays the results:

```php

$client = new Solarium\Client($config);
$query = $client->createSelect();

$facetSet = $query->getFacetSet();
$facetSet->createFacetField('stock')->setField('inStock');

$resultset = $client->select($query);
echo 'NumFound: '.$resultset->getNumFound() . PHP_EOL;

$facet = $resultset->getFacetSet()->getFacet('stock');
foreach ($facet as $value => $count) {
    echo $value . ' [' . $count . ']' . PHP_EOL;
}

foreach ($resultset as $document) {
    echo $document->id . PHP_EOL;
    echo $document->name . PHP_EOL;
}
```
