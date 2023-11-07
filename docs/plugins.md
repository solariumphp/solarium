Plugins
=======

Solarium offers a plugin system to allow for easy extension by users. But plugins are also used by Solarium itself to keep optional features from bloating the main code base. In this section the standard plugins are documented.


BufferedAdd plugin
------------------

When you need to do a lot of document inserts or updates, for instance a bulk update or initial indexing, it’s most efficient to do this in batches. This makes a lot more difference than you might think, for some benchmarks see [this archived blog post](https://web.archive.org/web/20170418205443/http://www.raspberry.nl/2011/04/08/solr-update-performance/).

This can be done very easily with this plugin, you can simply keep feeding documents, it will automatically create batch update queries for you.

### Some notes

-   Solarium issues JSON formatted update requests by default. If you require XML specific functionality, you can set the request format to XML on the plugin instance. XML requests are slower than JSON.
-   You can set a custom buffer size. The default is 100 documents, a safe value. By increasing this you can get even better performance, but depending on your document size at some level you will run into memory or request limits. A value of 1000 has been successfully used for indexing 200k documents.
-   You can use the createDocument method with array input, but you can also manually create document instance and use the addDocument(s) method.
-   With buffer size X an update request will be sent to Solr for each X docs. You can just keep feeding docs. These buffer flushes don’t include a commit. This is done on purpose. You can add a commit when you’re done, or you can use the Solr auto commit feature.
-   When you are done with feeding the buffer you need to manually flush it one time. This is because there might still be some docs in the buffer that haven’t been flushed yet.
-   Alternatively you can end with a commit call. This will also flush the docs, and adds a commit command.
-   The buffer also has a clear method to reset the buffer contents. However documents that have already been flushed cannot be cleared.
-   Using the 'setEndpoint' method you can select which endpoint should be used. If you don't set a specific endpoint, the default endpoint of the client instance will be used.

### Events

#### solarium.bufferedAdd.addDocument

For each document that is added an 'AddDocument' event is triggered. This event has access to the document being added.

#### solarium.bufferedAdd.preFlush

Triggered just before a flush. Has access to the document buffer and overwrite and commitWithin settings.

#### solarium.bufferedAdd.postFlush

Triggered just after a flush. Has access to the flush (update query) result.

#### solarium.bufferedAdd.preCommit

Triggered just before a commit. Has access to the document buffer and all commit settings.

#### solarium.bufferedAdd.postCommit

Triggered just after a commit. Has access to the commit (update query) result.

### Example usage

```php
<?php

require_once(__DIR__.'/init.php');

use Solarium\Plugin\BufferedAdd\Event\Events;
use Solarium\Plugin\BufferedAdd\Event\PreFlush as PreFlushEvent;
use Solarium\QueryType\Update\Query\Query;

htmlHeader();

// create a client instance and autoload the buffered add plugin
$client = new Solarium\Client($adapter, $eventDispatcher, $config);
$buffer = $client->getPlugin('bufferedadd');
$buffer->setBufferSize(10); // this is quite low, in most cases you can use a much higher value

// also register an event hook to display what is happening
$client->getEventDispatcher()->addListener(
    Events::PRE_FLUSH,
    function (PreFlushEvent $event) {
        echo 'Flushing buffer (' . count($event->getBuffer()) . ' docs)<br/>';
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

BufferedAddLite plugin
----------------------

A slightly faster version of `BufferedAdd` that doesn't trigger events.

```php
$buffer = $client->getPlugin('bufferedaddlite');
```

BufferedDelete plugin
---------------------

When you need to delete a lot of documents, this can also be done in batches.

You can feed the IDs of the documents to delete, queries to delete matching documents, or both.

The default buffer size is 100 deletes, a safe value. By increasing this you can get even better performance.
Delete commands send very little data to Solr, so you can set this to a much higher value than the document
buffer size for `BufferedAdd`.

### Events

#### solarium.bufferedDelete.addDeleteById

For each delete by ID that is added an 'AddDeleteById' event is triggered. This event has access to the document ID to delete.

#### solarium.bufferedDelete.addDeleteQuery

For each delete query that is added an 'AddDeleteQuery' event is triggered. This event has access to the query that will be used to delete matching documents.

#### solarium.bufferedDelete.preFlush

Triggered just before a flush. Has access to the delete buffer.

#### solarium.bufferedDelete.postFlush

Triggered just after a flush. Has access to the flush (update query) result.

#### solarium.bufferedDelete.preCommit

Triggered just before a commit. Has access to the delete buffer and all commit settings.

#### solarium.bufferedDelete.postCommit

Triggered just after a commit. Has access to the commit (update query) result.

### Example usage

```php
<?php

require_once(__DIR__.'/init.php');

use Solarium\Plugin\BufferedDelete\Event\Events;
use Solarium\Plugin\BufferedDelete\Event\PreFlush as PreFlushEvent;
use Solarium\QueryType\Update\Query\Query;

htmlHeader();

// create a client instance and autoload the buffered delete plugin
$client = new Solarium\Client($adapter, $eventDispatcher, $config);
$buffer = $client->getPlugin('buffereddelete');
$buffer->setBufferSize(10); // this is quite low, in most cases you can use a much higher value

// also register an event hook to display what is happening
$client->getEventDispatcher()->addListener(
    Events::PRE_FLUSH,
    function (PreFlushEvent $event) {
        echo 'Flushing buffer (' . count($event->getBuffer()) . ' deletes)<br/>';
    }
);

// let's delete 25 docs
for ($i=1; $i<=25; $i++) {
    $buffer->addDeleteById($i);
}

// you can also delete documents matching a query
$buffer->addDeleteQuery('cat:discontinued');
$buffer->addDeleteQuery('manu_id_s:acme');

// At this point two flushes will already have been done by the buffer automatically (at the 10th and 20th delete), now
// manually flush the remainder. Alternatively you can use the commit method if you want to include a commit command.
$buffer->flush();

// In total 3 flushes (requests) have been sent to Solr. This should be visible in the output of the event hook.

htmlFooter();

```

BufferedDeleteLite plugin
-------------------------

A slightly faster version of `BufferedDelete` that doesn't trigger events.

```php
$buffer = $client->getPlugin('buffereddeletelite');
```

CustomizeRequest plugin
-----------------------

Solarium has support for the most used features in Solr. But if you need to use a feature not yet supported by Solarium, or even a custom Solr extension, you can use this plugin to manually add params to the request.

This plugin allows you to manually add two settings:

-   GET params
-   HTTP headers

GET params and headers by default are only applied to the next request, but optionally you can make them persistent so they are applied to every request. For GET params you can also set an option to overwrite any existing value(s) with the same name in the original request.

### Example usage

```php
<?php

require_once(__DIR__.'/init.php');

htmlHeader();

// create a client instance and autoload the customize request plugin
$client = new Solarium\Client($adapter, $eventDispatcher, $config);
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

// execute query (you should be able to see the extra params in the Solr log file)
$resultset = $client->select($query);

// display the total number of documents found by Solr
echo 'NumFound: '.$resultset->getNumFound() . '<br/>';

// execute the same query again (this time the 'id' param should no longer show up in the logs)
$resultset = $client->select($query);

// display the total number of documents found by Solr
echo 'NumFound: '.$resultset->getNumFound();

htmlFooter();

```

LoadBalancer plugin
-------------------

This plugin is a code based loadbalancer for when you do need the redundancy and extra performance of multiple Solr servers but have not yet grown to the point where you have dedicated loadbalancers. The main features of this plugin are:

-   support for multiple servers, each with their own ‘weight’
-   ability to use a failover mode (try another server on query failure)
-   block query types from loadbalancing (`Update` and `Extract` queries are blocked by default)
-   force a specific server for the next query

All blocked query types (`Update` and `Extract` by default) are excluded from loadbalancing so they will use the default adapter settings that point to the master. All other queries will be load balanced.

If you want to load balance a blocked query type anyway (e.g. when extracting without indexing), you can unblock it.

```php
$loadbalancer = $client->getPlugin('loadbalancer');
$loadbalancer->removeBlockedQueryType($client::QUERY_EXTRACT);
```

Failover mode is disabled by default. If enabled, a query will be retried on another endpoint if a connection to the endpoint can't be established.
You can optionally specify HTTP response status codes for which you also want to failover to another endpoint. The list of failover status codes is empty by default.

### Events

#### solarium.loadbalancer.endpointFailure

An `EndpointFailure` event is triggered when an HTTP exception occurs on one of the backends. This event has access to the `Endpoint` object and the `HttpException` that occurred.

```php
// what is the key of the failing endpoint?
$event->getEndpoint()->getKey();
// what is the exception message?
$event->getException()->getMessage();
```

#### solarium.loadbalancer.statusCodeFailure

A `StatusCodeFailure` event is triggered when an HTTP response status code is encountered that is in the list of failover error codes. This event has access to the `Endpoint` object and the `Response` that was received.

```php
// what is the key of the erroring endpoint?
$event->getEndpoint()->getKey();
// what is the response status code & message?
$event->getResponse()->getStatusCode();
$event->getResponse()->getStatusMessage();
```

### Example usage

```php
<?php

require_once(__DIR__.'/init.php');

htmlHeader();

// create a client instance and create endpoints
$client = new Solarium\Client($adapter, $eventDispatcher, $config);
$endpoint1 = $client->createEndpoint('local1'); //normally you would add endpoint specific settings...
$endpoint2 = $client->createEndpoint('local2');
$endpoint3 = $client->createEndpoint('local3');

// get loadbalancer plugin instance and add endpoints
$loadbalancer = $client->getPlugin('loadbalancer');
$loadbalancer->addEndpoint($endpoint1, 100);
$loadbalancer->addEndpoint($endpoint2, 100);
$loadbalancer->addEndpoint($endpoint3, 1);

// you can optionally enable failover mode for unresponsive endpoints, and additionally HTTP status codes of your choosing
$loadbalancer->setFailoverEnabled(true);
$loadbalancer->setFailoverMaxRetries(3);
$loadbalancer->addFailoverStatusCode(504);

// create a basic query to execute
$query = $client->createSelect();

// execute the query multiple times, displaying the server for each execution
for ($i = 1; $i <= 8; $i++) {
    $resultset = $client->select($query);
    echo 'Query execution #' . $i . '<br/>';
    echo 'NumFound: ' . $resultset->getNumFound(). '<br/>';
    echo 'Server: ' . $loadbalancer->getLastEndpoint() .'<hr/>';
}

// force a server for a query (normally 'local3' is extremely unlikely based on its weight)
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
-------------------------

The MinimumScoreFilter plugin allows you to filter a resultset on a minimum score, calculated using the maxScore and a given ratio. For instance, if you set a ratio of 0.5 and the best scoring document has a score of 1.2, all documents scoring less than 0.6 will be filtered.

There are two modes of filtering, removing or just marking. In the example below the marking mode is used and the examples is pretty much self explanatory. In remove mode documents not meeting the required score are not returned at all.

### Some important notes

1.  numFound, facet counts and other Solr data is not adjusted for the filtered documents. Just the document resultset is being filtered, this is done by Solarium AFTER getting standard search results from Solr. If you want to count the number of documents after filtering you would have to do that yourself, by iterating all results after filtering.
2.  Rows and start (paging and offset) still work, but again this is not adjusted for filtering. So if you sets rows to 10, you might get less because of the filtering.
3.  While it's not strictly necessary, you should sort by score as this is much more efficient. This also fits with the expected use case of getting only the best scoring documents.
4.  Result document marking is done using a decorator, so you should still be able to use a custom document class.
5.  Be aware of the issues related to 'normalizing' scores [more info](http://wiki.apache.org/lucene-java/ScoresAsPercentages). This filter only uses score to calculate the relevancy relative to the best result and doesn't return this calculated score, but be sure to test your results! In cases like an autocomplete or 'best-bet' type of search this filter can be very useful.

### Example usage

```php
<?php

require_once(__DIR__.'/init.php');

htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// enable the plugin and get a query instance
$filter = $client->getPlugin('minimumscorefilter');
$query = $client->createQuery($filter::QUERY_TYPE);
$query->setQuery('a');
$query->setFields(array('id'));
$query->setFilterRatio(.5);
$query->setFilterMode($query::FILTER_MODE_MARK);

// this executes the query and returns the result
$resultset = $client->execute($query);

// display the total number of documents found by Solr and the maximum score
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
------------------------

This plugin makes it possible to execute multiple Solr queries at the same time, instead of one after the other. For a use case where you need to execute two queries the performance gain can be very big, my tests show close to 50% improvement. This can be very useful if you need to query several Solr instances or get the results for multiple queries.

### Some important notes

-   This plugin makes use of the [cURL client adapter](client-and-adapters.md#curl-adapter) and calls `curl_multi_exec`, so you do need to have cURL available in your PHP environment to be able to use it.
-   If you construct the client with a different adapter, this plugin will replace it with a cURL adapter. Construct the client with a properly configured cURL adapter if you need proxy support.
-   Only request execution is parallel, request preparation and result parsing cannot be done parallelly. Luckily these parts cost very little time, far more time is in the requests.
-   The execution time is limited by the slowest request. If you execute 3 queries with timings of 0.2, 0.4 and 1.2 seconds the execution time for all will be (near) 1.2 seconds.
-   If one of the requests fails the other requests will still be executed and the results parsed. In the result array the entry for the failed query will contain an `HttpException` instead of a `Result` object. It's your own responsibility to check the result type.
-   All query types are supported, and you can even mix query types in the same `execute` call.
-   For testing this plugin you can use a special Solr delay component I’ve created (and used to develop the plugin). For more info see [this archived blog post](https://web.archive.org/web/20170904162800/http://www.raspberry.nl/2012/01/04/solr-delay-component/).
-   Add queries using the `addQuery` method. Supply at least a key and a query instance. Optionally you can supply a client instance as third argument. This can be used to execute queries on different cores or even servers. If omitted the plugin will use its own client instance.
-   It's possible to fetch multiple pages of results for the same query parallelly with basic pagination using `setStart` and `setRows`.
    It's not possible to achieve this with a `cursorMark` because its value for each but the first request depends on the returned `nextCursorMark` of a previous request.

### Example usage

```php
<?php

require_once(__DIR__.'/init.php');

htmlHeader();

// create a client instance and autoload the customize request plugin
$client = new Solarium\Client($adapter, $eventDispatcher, $config);
$parallel = $client->getPlugin('parallelexecution');

// Add a delay param to better show the effect, as an example Solr install with
// only a dozen documents is too fast for good testing.
// This param only works with the correct Solr plugin, see
// https://web.archive.org/web/20170904162800/http://www.raspberry.nl/2012/01/04/solr-delay-component/
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

// create two queries to execute
$queryInStock = $client->createSelect()->setQuery('inStock:true');
$queryLowPrice = $client->createSelect()->setQuery('price:[1 TO 30]');

// first execute the queries the normal way and time it
echo '<h1>Serial execution</h1>';
$start = microtime(true);
$resultInStock = $client->execute($queryInStock);
$resultLowPrice = $client->execute($queryLowPrice);
echo 'Execution time for normal "serial" execution of two queries: ' . round(microtime(true)-$start, 3) . ' s';
echo '<hr/>';
echo 'In stock: ' . $resultInStock->getNumFound() . '<br/>';
echo 'Low price: ' . $resultLowPrice->getNumFound() . '<br/>';

echo '<hr/>';

// now execute the two queries parallel and time it
echo '<h1>Parallel execution</h1>';
$start = microtime(true);
// keys for each query are important for fetching the results later!
$parallel->addQuery('instock', $queryInStock);
$parallel->addQuery('lowprice', $queryLowPrice);
$results = $parallel->execute();
echo 'Execution time for parallel execution of two queries: ' . round(microtime(true)-$start, 3) . ' s';
echo '<hr/>';
echo 'In stock: ' . $results['instock']->getNumFound() . '<br/>';
echo 'Low price: ' . $results['lowprice']->getNumFound() . '<br/>';

htmlFooter();

// Note: for this example on a default Solr index (with a tiny index) running on localhost the performance gain is
// minimal to none, sometimes even slightly slower!
// In a realworld scenario with network latency, a bigger dataset, more complex queries or multiple Solr instances the
// performance gain is much more.

```

PostBigRequest plugin
---------------------

If you use complex Solr queries with lots of facets and/or filterqueries the total length of your querystring can exceed the default limits of servlet containers. One solution is to alter your servlet container configuration to raise this limit. But if this is not possible or desired this plugin is another way to solve the problem.

This plugin can automatically convert query execution from using a GET request to a POST request if the querystring exceeds a length limit. Solr also accepts it’s input in the POST data and this usually has a much higher limit.

For instance in Jetty the default ‘headerBufferSize’ is 4kB. Tomcat has a similar setting ‘maxHttpHeaderSize’, also 4kB by default. This limit applies to all the combined headers of a request, so it’s not just the querystring. In comparison, the default for POST data in tomcat (‘maxPostSize’) is 4MB. Jetty uses a ‘maxFormContentSize’ setting with a lower default value of 200kB, but still way higher than the header limit and well above the length of even the most complex queries.

The plugin only uses the length of the querystring to determine the switch to a POST request. Other headers are not included in the length calculation so your limit should be somewhat lower than the limit of the servlet container to allow for room for other headers. This was done to keep the length calculation simple and fast, because the exact headers used can vary for the various client adapters available in Solarium. You can alter the maxquerystringlength by using a config setting or the API. Only GET requests are switched over to POST, if the request already uses the POST method (for instance update queries) it’s not altered.

### Example usage

```php
<?php

require_once(__DIR__.'/init.php');

htmlHeader();

// create a client instance and autoload the postbigrequest plugin
$client = new Solarium\Client($adapter, $eventDispatcher, $config);
$client->getPlugin('postbigrequest');

// create a basic query to execute
$query = $client->createSelect();

// add a huge filterquery to create a very long query string
$fq = 'price:0 OR cat:'.str_repeat(implode('', range('a', 'z')), 1000);
$query->createFilterQuery('fq')->setQuery($fq);

// without the plugin this query would fail as it is bigger than the default servlet container header buffer
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

    echo '</table>';
}

htmlFooter();

```

PrefetchIterator plugin
-----------------------

This plugin can be used for iterating a big resultset. It has an `\Iterator` interface and will fetch the results from Solr when needed, in batches of a configurable size (sequential prefetching). You can even iterate all the documents in a Solr index.

It's very easy to use. You configure a query like you normally would and pass it to the plugin. See the [example code](#example-usage_7) below.

### When to use PrefetchIterator

Whether or not you should use PrefetchIterator depends on your use case.

For websites, you probably don't need PrefetchIterator. If you use classic paginated results where users can jump to any specific page, Solr's basic pagination
is the perfect match. When a user requests page `$p` with `$num` results per page, the translation to Solarium is straightforward.

```php
$query->setStart(($p - 1) * $num)->setRows($num);
```

Use cases for PrefetchIterator are more likely to involve background processes and cronjobs. It's very useful when you need to process all results for a query
(this can be `*:*` for your entire index). It deals with repeating the query every `$num` rows for you without requiring additional logic on your part to keep track of the page.

```php
$prefetch->setPrefetch($num)->setQuery($query);
```

### When to use a `cursorMark`

If you want to use a `cursorMark`, the sort for your query MUST include the `uniqueKey` field for your schema. Combining it with a sort on other
fields is fine, but it can't be omitted.

When you need a very large number of sorted results, basic pagination can be very inefficient. Cursors offer an alternative to scan through results without the performance
problems of ‘deep paging’. Regardless of index size, there is the benefit that the impact of index modifications between subsequent queries is much smaller with a cursor than
with basic pagination (see [notes](#some-notes_1) below).

For websites, you can use a `cursorMark` for an infinite scroll to load results sequentially. Instead of telling Solr ‘I want this many results starting at that position’,
you tell it ‘this is where I got to, give me the next part’ by using the `nextCursorMark` that gets returned with your result. The translation to Solarium is twofold.

```php
// first request
$query->setSorts([...])->setRows($num)->setCursorMark('*');

// subsequent requests 
$query->setSorts([...])->setRows($num)->setCursorMark($result->getNextCursorMark());
```

PrefetchIterator makes it set-and-forget by handling that logic for you when repeating the query. Because it can't know whether your sort includes the `uniqueKey` field
for your schema, you do need to set it to `*` on the query yourself in order to use a cursor instead of basic pagination.

```php
$query->setSorts([...])->setCursorMark('*');
$prefetch->setPrefetch($num)->setQuery($query);
```

### Some notes

- Index modifications that affect the order of documents can cause a document to ‘jump pages’ between subsequent requests. The same document could be returned on multiple
  pages, or be skipped entirely and never show up in your resultset.
    * With basic pagination, this can be caused by updates to the value of a sort field, as well as by adding or removing documents that match the query.
    * With a cursor, this can only be caused by updates to the value of a sort field.
    * If you want to ensure this never happens, use a `cursorMark` and sort _only_ on the `uniqueKey` field.
- If your sort includes date math relative to `NOW`, it can also mess with the order of results because the value of `NOW` will be recalculated on every request.
  This can lead to a neverending cursor that keeps returning the same documents. It's best to set a fixed value for `NOW` for your query that will be used on every request.
  <pre><code class="language-php">$query->setNow($timestamp);</code></pre>
- Every time a new page of results is fetched, the previously fetched documents are discarded. This allows for iterating very big resultsets with limited memory.
  You can set the number of rows to fetch per request as low as you like to save on memory. The trade-off is a higher number of requests to Solr.
  How high you can reasonably set it will largely be determined by the size of the returned documents.
- When you rewind the iterator (e.g. by looping over it in two consecutive `foreach` loops), all results will have to be refetched from Solr.
- PrefetchIterator can't be used in combination with [ParallelExecution](#parallelexecution-plugin). The next page of results is only ever fetched after all documents in
  the previous fetch have been consumed.

### Example usage

```php
<?php

require_once(__DIR__.'/init.php');

htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect();
$query->setFields(array('id'));

// cursor functionality can be used for efficient deep paging (since Solr 4.7)
$query->setCursorMark('*');
// cursor functionality requires a sort containing a uniqueKey field as tie breaker on top of your desired sorts for the query
$query->addSort('id', $query::SORT_ASC);

// get a plugin instance and apply settings
$prefetch = $client->getPlugin('prefetchiterator');
$prefetch->setPrefetch(2); // fetch 2 rows per request (for real world use this can be way higher)
$prefetch->setQuery($query);

// display the total number of documents found by Solr
echo 'NumFound: ' . count($prefetch);

// show document IDs using the resultset iterator
foreach ($prefetch as $document) {
    echo '<hr/>ID: '. $document->id;
}

htmlFooter();

```

### Advanced usage

A more advanced possibility is looping over the iterator with `for` or `while`. This allows for stop conditions that can end the
loop before reaching the end of the iterator.

This example gets the first 100 ‘interesting’ documents and which position they appear at (where ‘interesting’ is a property that
can't be determined in a Solr query).

```php
$n = 0;
$docs = [];

while ($n < 100 && $prefetch->valid()) {
    if (isInteresting($prefetch->current()) {
        $docs[$prefetch->key()] = $prefetch->current();
        ++$n;
    }

    $prefetch->next();
}
```

This example loops over the same iterator twice. It rewinds in between to reset the iterator to the start position before starting
the second loop. You can do this as many times as you want.

```php
while ($prefetch->valid()) {
    // do something

    $prefetch->next();
}

$prefetch->rewind();

while ($prefetch->valid()) {
    // do something else

    $prefetch->next();
}
```

The iterator functions MUST be called in the correct order.

- A ‘fresh’ PrefetchIterator is rewound by default (but it's no problem to `rewind()` it anyway).
    * When you want to loop from the start more than once, you have to call `rewind()` before every subsequent `for` or `while` loop.
    * `foreach` does this automatically (but it's no problem to do it manually anyway).
    * After rewinding, documents will be refetched from the server in the subsequent loop.
- The first call of every iteration MUST be `valid()`.
    * It will tell you if there is a document at the current position.
    * This is also the point where the next request to Solr is executed if all previously fetched documents have been consumed.
    * When part of an expression that also checks other conditions, placing it rightmost takes advantage of lazy evaluation to
      avoid an unnecessary request to Solr if the other conditions aren't met.
- The call to `current()` gets the current document from the iterator.
    * Without calling `valid()` first, it CAN fail to return a document even if the full resultset does extend beyond the current position.
    * Calling `current()` more than once without advancing the iterator returns the same document from the already fetched results.
- The call to `key()` returns the current position of the iterator.
    * It can be called before or after `current()`, more than once, or omitted.
- The call to `next()` advances the iterator to the next position.
    * It's good form to make this the last statement of an iteration.
    * You can't get the document at the new position without calling `valid()` first in the next iteration.

Another possibility is intentionally _not_ calling `rewind()` between subsequent loops. This allows for handling documents in
chunks of an arbitrary size, unrelated to the prefetch size.

This example writes the retrieved documents to a set of CSV files with 65536 rows each, including a header row with field names.
(Note: Don't use this code in an actual application as implemented here! The order of the fields isn't guaranteed across
documents and it doesn't handle mutli-valued fields properly.)

```php
$chunk = 1;

while ($prefetch->valid()) {
    $n = 0;
    $handle = fopen('file-'.$chunk.'.csv', 'w');
    fputcsv($handle, array_keys($prefetch->current()->getFields()));

    do {
        fputcsv($handle, $prefetch->current()->getFields());
        $prefetch->next();
    } while (++$n < 65535 && $prefetch->valid());

    fclose($handle);
    ++$chunk;
}
```

We avoid calling `valid()` on the first iteration of the inner loop by using `do-while` because it was already called for that
position on the outer `while`. Using an inner `while` instead is functionally equivalent, but calling `valid()` twice in
succession would cause the same documents to be fetched twice from Solr (although still processed once by the script) on common
multiples of the chunk size and prefetch size.

PostBigExtractRequest plugin
---------------------

If you use complex Solr extract queries with lots of literals to define your custom metadata the total length of your querystring can exceed the default limits of servlet containers. One solution is to alter your servlet container configuration to raise this limit. But if this is not possible or desired this plugin is another way to solve the problem.

This plugin can automatically move all parameters from querystring to the multipart body content of the request if the querystring exceeds a length limit.

For instance, in Jetty the default ‘headerBufferSize’ is 4KiB. Tomcat 10 has a similar setting ‘maxHttpHeaderSize’, 8KiB by default. This limit applies to all the combined headers of a request, so it’s not just the querystring. In comparison, the default for POST data in tomcat (‘maxPostSize’) is 2MiB. Jetty uses a ‘maxFormContentSize’ setting with a lower default value of 200kB, but still way higher than the header limit and well above the length of even the most complex queries.

The plugin only uses the length of the querystring to determine the parameters relocation. Other headers are not included in the length calculation so your limit should be somewhat lower than the limit of the servlet container to allow for room for other headers. This was done to keep the length calculation simple and fast because the exact headers used can vary for the various client adapters available in Solarium. You can alter the `maxquerystringlength` by using a config setting or the API. Only `Extract` queries are affected, other types of queries are not altered.

### Example usage

```php
<?php

require_once __DIR__.'/init.php';
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);
$postBigExtractRequest = $client->getPlugin('postbigextractrequest');
// set the maximum length to a value appropriate for your servlet container
$postBigExtractRequest->setMaxQueryStringLength(1024);

// get an extract query instance and add settings
$query = $client->createExtract();
$query->setInputEncoding('UTF-8');
$query->addFieldMapping('content', 'text');
$query->setUprefix('attr_');
$query->setFile(__DIR__.'/index.html');
$query->setCommit(true);
$query->setOmitHeader(false);

// add document
$doc = $query->createDocument();
$doc->id = 'extract-test';
$doc->some = 'more fields';
// create a very long list of literals
for ($i = 1; $i <= 500; ++$i) {
    $field_name = "field_{$i}";
    $doc->$field_name = "value $i";
}
$query->setDocument($doc);

// this executes the query and returns the result
$result = $client->extract($query);

echo '<b>Extract query executed</b><br/>';
echo 'Query status: '.$result->getStatus().'<br/>';
echo 'Query time: '.$result->getQueryTime();

htmlFooter();
```
