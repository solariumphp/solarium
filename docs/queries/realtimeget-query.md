A RealtimeGet query is useful when using Solr as a NoSql store. For more info see <http://wiki.apache.org/solr/RealTimeGet>

Building a realtime query
-------------------------

See the example code below.

**Available options:**

| Name          | Type   | Default value                 | Description                                                                                                                               |
|---------------|--------|-------------------------------|-------------------------------------------------------------------------------------------------------------------------------------------|
| handler       | string | select                        | Name of the Solr request handler to use, without leading or trailing slashes                                                              |
| resultclass   | string | Solarium\_Result\_Select      | Classname for result. If you set a custom classname make sure the class is readily available (or through autoloading)                     |
| documentclass | string | Solarium\_Document\_ReadWrite | Classname for documents in the resultset. If you set a custom classname make sure the class is readily available (or through autoloading) |
||

Executing a RealtimeGet query
-----------------------------

First of all create a RealtimeGet query instance and set a single ID or multiple IDs. Use the `realtimeGet` method of the client to execute the query object.

See the example code below.

Result of a RealtimeGet query
-----------------------------

The result of a RealtimeGet query is similar to a select query,

Example
-------

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get an update query instance
$update = $client->createUpdate();

// create a new document
$id = time();
$doc1 = $update->createDocument();
$doc1->id = $id;
$doc1->name = 'realtime-get-test-'.date('Y-m-d H:i:s');

// set a very long commitWithin time and add it to solr
$update->addDocument($doc1, null, 1000000);
$client->update($update);

// try to get the document using a normal select, this should return 0 results
$query = $client->createSelect();
$query->setQuery('id:%1%', array($id));
$resultset = $client->select($query);
echo 'NumFound with standard select: '.$resultset->getNumFound().'<br/>';

// now with realtime get, this should return 1 result
$query = $client->createRealtimeGet();
$query->addId($id);
$result = $client->realtimeGet($query);
echo 'NumFound with realtime get: '.$result->getNumFound().'<br/>';

// Display the document
echo '<hr/><table>';
foreach ($result->getDocument() as $field => $value) {
    echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
}
echo '</table>';


htmlFooter();

```
