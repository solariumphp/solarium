A ping query can be used to check the connection to the Solr server and the health of the Solr server. It's not just a network ping, it will execute a query to check Solr health. You can set this query in the Solr config with the 'pingQuery' directive.

*It's not advisable to check Solr with a ping before every request, this can have a big performance impact. You are better of using the ping query with intervals, or as a check after a query error to see if the query was faulty or if Solr has problems.*

The search executed by a ping is configured with the Request Parameters API. For more info see <https://solr.apache.org/guide/ping.html>.

Creating a ping query
---------------------

You create a ping query using the createPing method of your client instance. This is a very simple query with only one option, the handler.

**Available options:**

| Name    | Type   | Default value | Description                                    |
|---------|--------|---------------|------------------------------------------------|
| handler | string | admin/ping    | Path to the ping handler as configured in Solr |
||

Executing a ping query
----------------------

Use the `ping` method of the client to execute the query object. See the example code below.

Result of a ping query
----------------------

The result of a ping query is just as simple as the ping query itself: it either works or not. There is no useful result data. In case of error an exception will be thrown.

Example
-------

```php
<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// check Solarium version available
echo 'Solarium library version: ' . Solarium\Client::getVersion() . ' - ';

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// create a ping query
$ping = $client->createPing();

// execute the ping query
try {
    $result = $client->ping($ping);
    echo 'Ping query successful';
    echo '<br/><pre>';
    var_dump($result->getData());
    echo '</pre>';
} catch (Exception $e) {
    echo 'Ping query failed';
}

htmlFooter();

```
