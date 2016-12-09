Plugins
=======

Solarium offers a plugin system to allow for easy extension by users. But plugins are also used by Solarium itself to keep optional features from bloating the main code base. In this section the standard plugins are documented.


BufferedAdd plugin
==================

When you need to do a lot of document inserts or updates, for instance a bulk update or initial indexing, it’s most efficient to do this in batches. This makes a lot more difference than you might think, for some benchmarks see [this_blog post](http://www.raspberry.nl/2011/04/08/solr-update-performance/).

This can be done very easily with this plugin, you can simply keep feeding documents, it will automatically create batch update queries for you.

Some notes:

-   You can set a custom buffer size. The default is 100 documents, a safe value. By increasing this you can get even better performance, but depending on your document size at some level you will run into memory or request limits. A value of 1000 has been successfully used for indexing 200k documents.
-   You can use the createDocument method with array input, but you can also manually create document instance and use the addDocument(s) method.
-   With buffer size X an update request with be sent to Solr for each X docs. You can just keep feeding docs. These buffer flushes don’t include a commit. This is done on purpose. You can add a commit when you’re done, or you can use the Solr auto commit feature.
-   When you are done with feeding the buffer you need to manually flush it one time. This is because there might still be some docs in the buffer that haven’t been flushed yet.
-   Alternatively you can end with a commit call. This will also flush the docs, and adds a commit command.
-   The buffer also has a clear method to reset the buffer contents. However documents that have already been flushed cannot be cleared.
-   Using the 'setEndpoint' method you can select which endpoint should be used. If you don't set a specific endpoint, the default endpoint of the client instance will be used.

Events
------

### solarium.bufferedAdd.addDocument

For each document that is added an 'AddDocument' event is triggered. This even has access to the document being added.

### solarium.bufferedAdd.preFlush

Triggered just before a flush. Has access to the document buffer and overwrite and commitWithin settings

### solarium.bufferedAdd.postFlush

Triggered just after a flush. Has access to the flush (update query) result

### solarium.bufferedAdd.preCommit

Triggered just before a commit. Has access to the document buffer and all commit settings

### solarium.bufferedAdd.postCommit

Triggered just after a commit. Has access to the commit (update query) result

Example usage
-------------

```php
<?php
require(__DIR__.'/init.php');

use Solarium\Plugin\BufferedAdd\Event\Events;
use Solarium\Plugin\BufferedAdd\Event\PreFlush as PreFlushEvent;

htmlHeader();

// create a client instance and autoload the buffered add plugin
$client = new Solarium\Client($config);
$buffer = $client->getPlugin('bufferedadd');
$buffer->setBufferSize(10); // this is quite low, in most cases you can use a much higher value

// also register an event hook to display what is happening
$client->getEventDispatcher()->addListener(
    Events::PRE_FLUSH,
    function (PreFlushEvent $event) {
        echo 'Flushing buffer (' . count($event->getBuffer()) . 'docs)<br/>';
    }
);

// let's insert 25 docs
for ($i=1; $i<=25; $i++) {

    // create a new document with dummy data and add it to the buffer
    $data = array(
        'id' => 'test_'.$i,
        'name' => 'test for buffered add',
        'price' => $i,
    );
    $buffer->createDocument($data);

    // alternatively you could create document instances yourself and use the addDocument(s) method
}

// At this point two flushes will already have been done by the buffer automatically (at the 10th and 20th doc), now
// manually flush the remainder. Alternatively you can use the commit method if you want to include a commit command.
$buffer->flush();

// In total 3 flushes (requests) have been sent to Solr. This should be visible in the output of the event hook.

htmlFooter();

```

CustomizeRequest plugin
=======================

Solarium has support for the most used features in Solr. But if you need to use a feature not yet supported by Solarium, or even a custom Solr extension, you can use this plugin to manually add params to the request.

This plugin allows you to manually add two settings:

-   GET params
-   HTTP headers

GET params and headers by default are only applied to the next request, but optionally you can make them persistent so they are applied to every request. For GET params you can also set an option to overwrite any existing value(s) with the same name in the original request.

Example usage
-------------

```php
<?php
require(__DIR__.'/init.php');

htmlHeader();

// create a client instance and autoload the customize request plugin
$client = new Solarium\Client($config);
$customizer = $client->getPlugin('customizerequest');

// add a persistent HTTP header (using array input values)
$customizer->createCustomization(
    array(
        'key' => 'auth',
        'type' => 'header',
        'name' => 'X-my-auth',
        'value' => 'mypassword',
        'persistent' => true
    )
);

// add a persistent GET param (using fluent interface)
$customizer->createCustomization('session')
           ->setType('param')
           ->setName('ssid')
           ->setValue('md7Nhd86adye6sad46d')
           ->setPersistent(true);

// add a GET param thats only used for a single request (the default setting is no persistence)
$customizer->createCustomization('id')
           ->setType('param')
           ->setName('id')
           ->setValue(4576);

// create a basic query to execute
$query = $client->createSelect();

// execute query (you should be able to see the extra params in the solr log file)
$resultset = $client->select($query);

// display the total number of documents found by solr
echo 'NumFound: '.$resultset->getNumFound() . '<br/>';

// execute the same query again (this time the 'id' param should no longer show up in the logs)
$resultset = $client->select($query);

// display the total number of documents found by solr
echo 'NumFound: '.$resultset->getNumFound();

htmlFooter();

```

LoadBalancer plugin
===================

This plugin is a code based loadbalancer for when you do need the redundancy and extra performance of multiple Solr servers but have not yet grown to the point where you have dedicated loadbalancers. The main features of this plugin are:

-   support for multiple servers, each with their own ‘weight’
-   ability to use a failover mode (try another server on query failure)
-   block querytypes from loadbalancing (update queries are blocked by default)
-   force a specific server for the next query

All blocked query types (updates by default) are excluded from loadbalancing so they will use the default adapter settings that point to the master. All other queries will be load balanced.

Events
------

### solarium.loadbalancer.endpointFailure

An 'EndpointFailure' event is triggered when a HTTP exception occurs on one of the backends. This event has access to the Endpoint object and the exception that occurred.

Example usage
-------------

```php
<?php
require(__DIR__.'/init.php');

htmlHeader();

// create a client instance and create endpoints
$client = new Solarium\Client($config);
$endpoint1 = $client->createEndpoint('local1'); //normally you would add endpoint specific settings...
$endpoint2 = $client->createEndpoint('local2');
$endpoint3 = $client->createEndpoint('local3');

// get loadbalancer plugin instance and add endpoints
$loadbalancer = $client->getPlugin('loadbalancer');
$loadbalancer->addEndpoint($endpoint1, 100);
$loadbalancer->addEndpoint($endpoint2, 100);
$loadbalancer->addEndpoint($endpoint3, 1);

// create a basic query to execute
$query = $client->createSelect();

// execute the query multiple times, displaying the server for each execution
for ($i = 1; $i <= 8; $i++) {
    $resultset = $client->select($query);
    echo 'Query execution #' . $i . '<br/>';
    echo 'NumFound: ' . $resultset->getNumFound(). '<br/>';
    echo 'Server: ' . $loadbalancer->getLastEndpoint() .'<hr/>';
}

// force a server for a query (normally solr 3 is extremely unlikely based on its weight)
$loadbalancer->setForcedEndpointForNextQuery('local3');

$resultset = $client->select($query);
echo 'Query execution with server forced to local3<br/>';
echo 'NumFound: ' . $resultset->getNumFound(). '<br/>';
echo 'Server: ' . $loadbalancer->getLastEndpoint() .'<hr/>';

// test a ping query
$query = $client->createPing();
$client->ping($query);
echo 'Loadbalanced ping query, should display a loadbalancing server:<br/>';
echo 'Ping server: ' . $loadbalancer->getLastEndpoint() .'<hr/>';

// exclude ping query from loadbalancing
$loadbalancer->addBlockedQueryType(Solarium\Client::QUERY_PING);
$client->ping($query);
echo 'Non-loadbalanced ping query, should not display a loadbalancing server:<br/>';
echo 'Ping server: ' . $loadbalancer->getLastEndpoint() .'<hr/>';

htmlFooter();

```


MinimumScoreFilter plugin
=========================

The MinimumScoreFilter plugin allows you to filter a resultset on a minimum score, calculated using the maxScore and a given ratio. For instance, if you set a ratio of 0.5 and the best scoring document has a score of 1.2, all documents scoring less than 0.6 will be filtered.

There are two modes of filtering, removing or just marking. In the example below the marking mode is used and the examples is pretty much self explanatory. In remove mode documents not meeting the required score are not returned at all.

Some important notes:

1.  numFound, facet counts and other Solr data is not adjusted for the filtered documents. Just the document resultset is being filtered, this is done by Solarium AFTER getting standard search results from Solr. If you want to count the number of documents after filtering you would have to do that yourself, by iterating all results after filtering.
2.  Rows and start (paging and offset) still work, but again this is not adjusted for filtering. So if you sets rows to 10, you might get less because of the filtering.
3.  While it's not strictly necessary, you should sort by score as this is much more efficient. This also fits with the expected use case of getting only the best scoring documents.
4.  Result document marking is done using a decorator, so you should still be able to use a custom document class.
5.  Be aware of the issues related to 'normalizing' scores [more info](http://wiki.apache.org/lucene-java/ScoresAsPercentages). This filter only uses score to calculate a the relevancy relative to the best result and doesn't return this calculated score, but be sure to test your results! In cases like an autocomplete or 'best-bet' type of search this filter can be very useful.

Example usage
-------------

```php
<?php
require(__DIR__.'/init.php');

htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// enable the plugin and get a query instance
$filter = $client->getPlugin('minimumscorefilter');
$query = $client->createQuery($filter::QUERY_TYPE);
$query->setQuery('a');
$query->setFields(array('id'));
$query->setFilterRatio(.5);
$query->setFilterMode($query::FILTER_MODE_MARK);

// this executes the query and returns the result
$resultset = $client->execute($query);

// display the total number of documents found by solr and the maximum score
echo 'NumFound: '.$resultset->getNumFound();
echo '<br/>MaxScore: '.$resultset->getMaxScore();

// show documents using the resultset iterator
foreach ($resultset as $document) {

    // by setting the FILTER_MARK option we get a special method to test each document
    if ($document->markedAsLowScore()) {
        echo '<hr/><b>MARKED AS LOW SCORE</b><table>';
    } else {
        echo '<hr/><table>';
    }

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


ParallelExecution plugin
========================

This plugin makes it possible to execute multiple Solr queries at the same time, instead of one after the other. For a use case where you need to execute two queries the performance gain can be very big, my tests show close to 50% improvement. This can be very useful if you need to query several Solr instances or get the results for multiple queries.

Some important notes:

-   This plugin makes use of the curl client adapter and calls curl\_multi\_exec, so you do need to have curl available in your PHP environment to be able to use it.
-   Only request execution is parallel, requests preparation and result parsing cannot be done parallel. Luckily these parts cost very little time, far more time is in the requests.
-   The execution time is limited by the slowest request. If you execute 3 queries with timings of 0.2, 0.4 and 1.2 seconds the execution time for all will be (near) 1.2 seconds.
-   If one of the requests fails the other requests will still be executed and the results parsed. In the result array the entry for the failed query will contain an exception instead of a result object. It’s your own responsibility to check the result type.
-   All query types are supported, and you can even mix query types in the same execute call.
-   For testing this plugin you can use a special Solr delay component I’ve created (and used to develop the plugin). For more info see [this blog post](http://www.raspberry.nl/2012/01/04/solr-delay-component/).
-   Add queries using the addQuery method. Supply at least a key and a query instance. Optionally you can supply a client instance as third argument. This can be used to execute queries on different cores or even servers. If omitted the plugin will use it's own client instance.

Example usage
-------------

```php
<?php
require(__DIR__.'/init.php');

htmlHeader();

// create a client instance and autoload the customize request plugin
$client = new Solarium\Client($config);
$parallel = $client->getPlugin('parallelexecution');

// Add a delay param to better show the effect, as an example Solr install with
// only a dozen documents is too fast for good testing
// This param only works with the correct Solr plugin,
// see http://www.raspberry.nl/2012/01/04/solr-delay-component/
// If you don't have to plugin the example still works, just without the delay.
$customizer = $client->getPlugin('customizerequest');
$customizer->createCustomization(
    array(
        'key' => 'delay',
        'type' => 'param',
        'name' => 'delay',
        'value' => '500',
        'persistent' => true
    )
);

// create two queries to execute in an array. Keys are important for fetching the results later!
$queryInstock = $client->createSelect()->setQuery('inStock:true');
$queryLowprice = $client->createSelect()->setQuery('price:[1 TO 300]');

// first execute the queries the normal way and time it
$start = microtime(true);
$client->execute($queryInstock);
$client->execute($queryLowprice);
echo 'Execution time for normal "serial" execution of two queries: ' . round(microtime(true)-$start, 3);


echo '<hr/>';


// now execute the two queries parallel and time it
$start = microtime(true);
$parallel->addQuery('instock', $queryInstock);
$parallel->addQuery('lowprice', $queryLowprice);
$results = $parallel->execute();
echo 'Execution time for parallel execution of two queries: ' . round(microtime(true)-$start, 3);


htmlFooter();

// Note: for this example on a default Solr index (with a tiny index) running on localhost the performance gain is
// minimal to none, sometimes even slightly slower!
// In a realworld scenario with network latency, a bigger dataset, more complex queries or multiple solr instances the
// performance gain is much more.

```


PostBigRequest plugin
=====================

If you use complex Solr queries with lots of facets and/or filterqueries the total length of your querystring can exceed the default limits of servlet containers. One solution is to alter your servlet container configuration to raise this limit. But if this is not possible or desired this plugin is another way to solve the problem.

This plugin can automatically convert query execution from using a GET request to a POST request if the querystring exceeds a length limit. Solr also accepts it’s input in the POST data and this usually has a much higher limit.

For instance in Jetty the default ‘headerBufferSize’ is 4kB. Tomcat has a similar setting ‘maxHttpHeaderSize’, also 4kB by default. This limit applies to all the combined headers of a request, so it’s not just the querystring. In comparison, the default for POST data in tomcat (‘maxPostSize’) is 4MB. Jetty uses a ‘maxFormContentSize’ setting with a lower default value of 200kB, but still way higher than the header limit and well above the length of even the most complex queries.

The plugin only uses the length of the querystring to determine the switch to a POST request. Other headers are not included in the length calculation so your limit should be somewhat lower than the limit of the servlet container to allow for room for other headers. This was done to keep the length calculation simple and fast, because the exact headers used can vary for the various client adapters available in Solarium. You can alter the maxquerystringlength by using a config setting or the API. Only GET requests are switched over to POST, if the request already uses the POST method (for instance update queries) it’s not altered.

Example usage
-------------

```php
<?php
require(__DIR__.'/init.php');

htmlHeader();

// create a client instance and autoload the postbigrequest plugin
$client = new Solarium\Client($config);
$client->getPlugin('postbigrequest');

// create a basic query to execute
$query = $client->createSelect();

// add a huge filterquery to create a very long query string
// note: normally you would use a range for this, it's just an easy way to create a very big querystring as a test
$fq = '';
for ($i = 1; $i <= 1000; $i++) {
    $fq .= ' OR price:'.$i;
}
$fq = substr($fq, 4);
$query->createFilterQuery('fq')->setQuery($fq);

// without the plugin this query would fail as it is bigger than the default servlet container header buffer
$resultset = $client->select($query);

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


PrefetchIterator plugin
=======================

This plugin can be used for iterating a big resultset. It has an iterator interface and will fetch the results from Solr when needed, in batches of a configurable size (sequential prefetching). You can even iterate all the documents in a Solr index.

It’s very easy to use. You configure a query like you normally would and pass it to the plugin. See the example code below.

Example usage
-------------

```php
<?php
require(__DIR__.'/init.php');

htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get a select query instance
$query = $client->createSelect();
$query->setFields(array('id'));

// get a plugin instance and apply settings
$prefetch = $client->getPlugin('prefetchiterator');
$prefetch->setPrefetch(2); //fetch 2 rows per query (for real world use this can be way higher)
$prefetch->setQuery($query);

// display the total number of documents found by solr
echo 'NumFound: ' . count($prefetch);

// show document IDs using the resultset iterator
foreach ($prefetch as $document) {
    echo '<hr/>ID: '. $document->id;
}

htmlFooter();

```
