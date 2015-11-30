An extract query can be used to index files in Solr. For more info see <http://wiki.apache.org/solr/ExtractingRequestHandler>

Building an extract query
-------------------------

See the example code below.

**Available options:**

| Name          | Type    | Default value                 | Description                                                                                                                               |
|---------------|---------|-------------------------------|-------------------------------------------------------------------------------------------------------------------------------------------|
| handler       | string  | select                        | Name of the Solr request handler to use, without leading or trailing slashes                                                              |
| resultclass   | string  | Solarium\_Result\_Select      | Classname for result. If you set a custom classname make sure the class is readily available (or through autoloading)                     |
| documentclass | string  | Solarium\_Document\_ReadWrite | Classname for documents in the resultset. If you set a custom classname make sure the class is readily available (or through autoloading) |
| omitheader    | boolean | true                          | Disable Solr headers (saves some overhead, as the values aren't actually used in most cases)                                              |
||

Executing an Extract query
--------------------------

First of all create an Extract query instance and set the options, a file and document. Use the `extract` method of the client to execute the query object.

See the example code below.

Result of an extract query
--------------------------

The result of an extract query is similar to an update query,

Example
-------

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get an extract query instance and add settings
$query = $client->createExtract();
$query->addFieldMapping('content', 'text');
$query->setUprefix('attr_');
$query->setFile(__DIR__.'/index.html');
$query->setCommit(true);
$query->setOmitHeader(false);

// add document
$doc = $query->createDocument();
$doc->id = 'extract-test';
$doc->some = 'more fields';
$query->setDocument($doc);

// this executes the query and returns the result
$result = $client->extract($query);

echo '<b>Extract query executed</b><br/>';
echo 'Query status: ' . $result->getStatus(). '<br/>';
echo 'Query time: ' . $result->getQueryTime();

htmlFooter();

```
