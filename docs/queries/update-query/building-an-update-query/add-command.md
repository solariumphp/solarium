This command commits add documents to the index. If a document with the same uniquekey (see Solr schema) already exists it will be overwritten, effectively updating the document.

You can add multiple documents in a single add command, this is also more efficient than separate add commands.

Options
-------

| Name         | Type    | Default value | Description                                                                                                     |
|--------------|---------|---------------|-----------------------------------------------------------------------------------------------------------------|
| overwrite    | boolean | null          | Newer documents will replace previously added documents with the same uniqueKey                                 |
| commitwithin | int     | null          | If the "commitWithin" attribute is present, the document will be added within that time (value in milliseconds) |
||

For all options:

-   If no value is set (null) the param will not be sent to Solr and Solr will use it's default setting.
-   See Solr documentation for details of the params

Atomic updates
--------------

Solr 4+ supports atomic updates. You can use the 'setFieldModifier' and 'setVersion' method in the document class to enable atomic updates. By default the 'old' full document update mode is used.

Examples
--------

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get an update query instance
$update = $client->createUpdate();

// create a new document for the data
$doc1 = $update->createDocument();
$doc1->id = 123;
$doc1->name = 'testdoc-1';
$doc1->price = 364;

// and a second one
$doc2 = $update->createDocument();
$doc2->id = 124;
$doc2->name = 'testdoc-2';
$doc2->price = 340;

// add the documents and a commit command to the update query
$update->addDocuments(array($doc1, $doc2));
$update->addCommit();

// this executes the query and returns the result
$result = $client->update($update);

echo '<b>Update query executed</b><br/>';
echo 'Query status: ' . $result->getStatus(). '<br/>';
echo 'Query time: ' . $result->getQueryTime();

htmlFooter();

```
