Solarium concepts
=================

This section explains the concepts used by Solarium. It's not necessary to know this to use Solarium, but it will give you a better understanding of all the parts involved in handling a request. Especially useful if you want to work on the Solarium code, write an extension, or want to debug some issues you might encounter.


Usage modes
===========

Solarium allows for three main modes of usage of the library: programmatically, by extending or by configuration. They all have there own pros and cons, the most optimal solution depends on your use case and also your personal preference. You can even mix the different modes if you want to, for instance creating a select query based on a config and programmatically changing it.

Currently only the programmatic and extending modes support all features, the configuration mode doesn't support some complex cases. This might be improved over time but there will always be limits to what is possible with configuration only, without creating very complex configurations.

The configuration mode supports an array as input or an object that implements the `toArray()` method (this is also compatible with the Zend Framework `Zend_Config` component).

The three modes apply to all Solarium classes that extend `Solarium\Core\Configurable`. This includes all Solarium classes that are intended for direct usage, e.g. the query classes, filterqueries, components etcetera. You can check to API for a class to see if it supports config mode.

As an example the three modes are demonstrated, all creating an identical Solr client instance:

API example
-----------

```php
<?php

require(__DIR__.'/init.php');

htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get a select query instance
$query = $client->createSelect();

// apply settings using the API
$query->setQuery('*:*');
$query->setStart(2)->setRows(20);
$query->setFields(array('id','name','price'));
$query->addSort('price', $query::SORT_ASC);

// create a filterquery using the API
$fq = $query->createFilterQuery('maxprice')->setQuery('price:[1 TO 300]');

// create a facet field instance and set options using the API
$facetSet = $query->getFacetSet();
$facet = $facetSet->createFacetField('stock')->setField('inStock');

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by solr
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

Configuration example
---------------------

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();


// In this case an array is used for configuration to keep the example simple.
// For an easier to use config file you are probably better of with another format, like Zend_Config_Ini
// See the documentation for more info about this.
$select = array(
    'query'         => '*:*',
    'start'         => 2,
    'rows'          => 20,
    'fields'        => array('id','name','price'),
    'sort'          => array('price' => 'asc'),
    'filterquery' => array(
        'maxprice' => array(
            'query' => 'price:[1 TO 300]'
        ),
    ),
    'component' => array(
        'facetset' => array(
            'facet' => array(
                // notice this config uses an inline key value, instead of array key like the filterquery
                array('type' => 'field', 'key' => 'stock', 'field' => 'inStock'),
            )
        ),
    ),
);

// create a client instance
$client = new Solarium\Client($config);

// get a select query instance based on the config
$query = $client->createSelect($select);

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by solr
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

Extending example
-----------------

```php
<?php

require(__DIR__.'/init.php');
use Solarium\Client;
use Solarium\QueryType\Select\Query\Query as Select;

htmlHeader();

// In most cases using the API or config is advisable, however in some cases it can make sense to extend classes.
// This makes it possible to create 'query inheritance' like in this example
class ProductQuery extends Select
{
    protected function init()
    {
        parent::init();

        // basic params
        $this->setQuery('*:*');
        $this->setStart(2)->setRows(20);
        $this->setFields(array('id','name','price'));
        $this->addSort('price', self::SORT_ASC);

        // create a facet field instance and set options
        $facetSet = $this->getFacetSet();
        $facetSet->createFacetField('stock')->setField('inStock');
    }
}

// This query inherits all of the query params of its parent (using parent::init) and adds some more
// Ofcourse it could also alter or remove settings
class ProductPriceLimitedQuery extends ProductQuery
{
    protected function init()
    {
        parent::init();

        // create a filterquery
        $this->createFilterQuery('maxprice')->setQuery('price:[1 TO 300]');
    }
}

// create a client instance
$client = new Client($config);

// create a query instance
$query = new ProductPriceLimitedQuery;

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by solr
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

Structure and queryflow
=======================

TODO

Best practices
==============

In the following sections of this manual various parts of Solarium are described in detail, including best practices specific for those parts. This page lists some best practices for Solarium in general.

### Lazy loading

Solarium tries to use lazy-loading as much as possible. For instance a select query component is only added to a query when you actually use it. And a Solr result is only parsed at the moment you access the data. In your application you will usually build a layer on top of Solarium. Make sure this layer is also lazy-loading, else you will lose the benefits. Some examples:

-   don't add all components you *might need* to the query. Only add them on actual use.
-   only construct a query if you are actually going to use it
-   don't copy the data in the result objects but use the iterators or access methods. This prevents disabling the lazy-loading mechanism and also prevents data duplication.

One exception can be the Solarium client instance itself. Instantiating a Solarium\\Client instance has been made lightweight so you can have it ready at all times by adding it to a bootstrap, similar to a database access layer. However, there is still a small overhead so if you only use Solr on a small number of pages you might still be better of only loading it on those pages.

### Create methods

There are create methods for almost all Solarium classes. Instead of creating an instance of a specific (long) classname you can use a create method. For instance to create a select query:

`$select = $client->createSelect();`

Besides ease-of-use there is another important reason for these 'create' methods. The create methods use a class mapping. Plugins have the ability to customize class mappings, however this won't work if you directly instantiate classes yourself.

So while it is still possible to create class instances manually, it is advisable to use the create methods. Ideally you should only manually create an instance of 'Solarium\\Client', and use create methods for all other Solarium object instances (for instance filterqueries, facets, update commands and documents).

### Customizing Solarium

If you want to customize Solarium please read the docs on this first. While you can simply extend classes that's in most cases not the best way to go.

### Response parser format

Solarium supports two Solr responsewriters: json and phps. The ‘phps’ responsewriter returns data as serialized PHP. This can be more efficient to decode than json, especially for large responses. For a benchmark see [http://www.raspberry.nl/2012/02/28/benchmarking-php-solr-response-data-handling/ this blogpost](http://www.raspberry.nl/2012/02/28/benchmarking-php-solr-response-data-handling/_this_blogpost "wikilink") (but be sure to test for your own use-case)

However this comes at the cost of a possible security risk in PHP deserialization. As long as you use a trusted Solr server this should be no issue, but to be safe the default is still JSON.

You can switch to the phps responseparser by setting a query option:

`$query = $client->createQuery($client::QUERY_SELECT, array('responsewriter' => 'phps'));`
