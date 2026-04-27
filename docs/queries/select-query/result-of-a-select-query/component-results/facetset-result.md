The facetset component result object is a collection of facet results. You can fetch a specific facet result or iterate all facet results. The results of the various facet types are described below.

All facet results implement `FacetResultInterface`. The specific class is only known at runtime because it depends on the facet type(s) you requested with the query.
If you run into errors with your static analyzer because it doesn't know which methods exist on the facet result or whether it's iterable, or want to enable autocompletion in your IDE, you can add a type annotation to narrow it down. E.g.:

```php
/** @var Solarium\Component\Result\Facet\Field $facet */
$facet = $resultset->getFacetSet()->getFacet('stock');
```

When iterating facet results of different types, the `instanceof` operator can differentiate the type.

```php
foreach ($resultset->getFacetSet() as $key => $facet) {
    echo '<h1>'.$key.'</h1>';

    switch (true} {
        case $facet instanceof Solarium\Component\Result\Facet\Field:
            foreach ($facet as $value => $count) {
                echo $value.' ['.$count.']<br/>';
            }
            break;
        case $facet instanceof Solarium\Component\Result\Facet\Query:
            echo 'Facet query count : '.$facet->getValue();
            break;
        // ...
    }
```

Consult the [individual examples](https://github.com/solariumphp/solarium/tree/master/examples) in our repository for each type's specific class and how to use it. Some commonly used types are also described below.

Facet field
-----------

A facet field result has multiple counts, one for each term. You can get the counts using the `getValues()`, this will return an array with the terms as key and the counts as values.

Even easier is using the `Iterable` interface of this result. It will return terms as keys and counts as values.

You can also use the `Countable` interface to get the number of counts.

### Example

```php
<?php

require_once __DIR__.'/init.php';

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
/** @var Solarium\Component\Result\Facet\Field $facet */
$facet = $resultset->getFacetSet()->getFacet('stock');
foreach ($facet as $value => $count) {
    echo $value.' ['.$count.']<br/>';
}

// show documents using the resultset iterator
foreach ($resultset as $document) {
    echo '<hr/><table>';
    echo '<tr><th>id</th><td>'.$document->id.'</td></tr>';
    echo '<tr><th>name</th><td>'.$document->name.'</td></tr>';
    echo '<tr><th>price</th><td>'.$document->price.'</td></tr>';
    echo '</table>';
}

htmlFooter();

```

Facet query
-----------

A facet query result is really simple. It has just one value: the count. You can access it by using its `getValue()` method.

### Example

```php
<?php

require_once __DIR__.'/init.php';

htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect();

// get the facetset component
$facetSet = $query->getFacetSet();

// create a facet query instance and set options
$facetSet->createFacetQuery('stock')->setQuery('inStock: true');

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by Solr
echo 'NumFound: '.$resultset->getNumFound();

// display facet query count
/** @var Solarium\Component\Result\Facet\Query $facet */
$facet = $resultset->getFacetSet()->getFacet('stock');
echo '<hr/>Facet query count : '.$facet->getValue();

// show documents using the resultset iterator
foreach ($resultset as $document) {
    echo '<hr/><table>';
    echo '<tr><th>id</th><td>'.$document->id.'</td></tr>';
    echo '<tr><th>name</th><td>'.$document->name.'</td></tr>';
    echo '<tr><th>price</th><td>'.$document->price.'</td></tr>';
    echo '</table>';
}

htmlFooter();

```

Facet multiquery
----------------

A multiquery facet is basically a combination of multiple facet query instances. It works similar to the facet field, using query keys as keys and counts as values.

### Example

```php
<?php

require_once __DIR__.'/init.php';

htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect();

// get the facetset component
$facetSet = $query->getFacetSet();

// create a facet query instance and set options
$facet = $facetSet->createFacetMultiQuery('stock');
$facet->createQuery('stock_pricecat1', 'inStock:true AND price:[1 TO 300]');
$facet->createQuery('nostock_pricecat1', 'inStock:false AND price:[1 TO 300]');
$facet->createQuery('stock_pricecat2', 'inStock:true AND price:[300 TO *]');
$facet->createQuery('nostock_pricecat2', 'inStock:false AND price:[300 TO *]');

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by Solr
echo 'NumFound: '.$resultset->getNumFound();

// display facet counts
echo '<hr/>Multiquery facet counts:<br/>';
/** @var Solarium\Component\Result\Facet\MultiQuery $facet */
$facet = $resultset->getFacetSet()->getFacet('stock');
foreach ($facet as $key => $count) {
    echo $key.' ['.$count.']<br/>';
}

// show documents using the resultset iterator
foreach ($resultset as $document) {
    echo '<hr/><table>';
    echo '<tr><th>id</th><td>'.$document->id.'</td></tr>';
    echo '<tr><th>name</th><td>'.$document->name.'</td></tr>';
    echo '<tr><th>price</th><td>'.$document->price.'</td></tr>';
    echo '</table>';
}

htmlFooter();

```

Facet range
-----------

A range facet is also similar to a facet field, but instead of field value counts you get range counts. In addition you can get the 'before' , 'between' and 'after' count (if you specified this in the query).

### Example

```php
<?php

require_once __DIR__.'/init.php';

htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect();

// get the facetset component
$facetSet = $query->getFacetSet();

// create a facet range instance and set options
$facet = $facetSet->createFacetRange('priceranges');
$facet->setField('price');
$facet->setStart(1);
$facet->setGap(100);
$facet->setEnd(1000);

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by Solr
echo 'NumFound: '.$resultset->getNumFound();

// display facet counts
echo '<hr/>Facet ranges:<br/>';
/** @var Solarium\Component\Result\Facet\Range $facet */
$facet = $resultset->getFacetSet()->getFacet('priceranges');
foreach ($facet as $range => $count) {
    echo $range.' to '.($range + 100).' ['.$count.']<br/>';
}

// show documents using the resultset iterator
foreach ($resultset as $document) {
    echo '<hr/><table>';
    echo '<tr><th>id</th><td>'.$document->id.'</td></tr>';
    echo '<tr><th>name</th><td>'.$document->name.'</td></tr>';
    echo '<tr><th>price</th><td>'.$document->price.'</td></tr>';
    echo '</table>';
}

htmlFooter();

```
