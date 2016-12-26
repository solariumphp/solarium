An update query has options and commands. These commands and options are instructions for the client classes to build and execute a request and return the correct result. In the following sections both the options and commands will be discussed in detail. You can also take a look at <http://wiki.apache.org/solr/UpdateXmlMessages> for more information about the underlying Solr update handler XML request format.

Options
-------

The update query has only two options, and it's not likely you need alter them. For select queries it's not uncommon to have a custom Solr handler, or a custom resultclass that maps to your models. But for an update query you usually just want to use the default handler and check the result. For this the default settings are just fine.

However, if you do need to customize them for a special case, you can.

### ResultClass

If you want to use a custom result class you can set the class name with this option. Any custom result class should implement the ResultInterface. It is your responsibility to make sure this class is included before use (or available through autoloading).

### Handler

The handler is used for building the Solr URL. The default value is 'update' and it's very uncommon to need to change this. But if you have a special update handler configured in your Solr core you can use this option to route update requests to this handler.

The handler value should not start or end with a slash, but may contain slashes. For instance 'admin/ping' for the ping handler is valid.

Commands
--------

Commands are the most important part of an update request. An update request may contain any combination of commands in any order. The commands will be added to the request in the exact order that you add them, and will be executed by Solr in exactly that order.

So if you were to add a 'delete' command followed by a 'rollback' command than the delete command will have no effect. (assuming there is no autocommit on the Solr server)

Examples
--------

These are two examples of update query usages. See the following sections for the details and examples of all available commands.

Add documents:

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

Delete by query:

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get an update query instance
$update = $client->createUpdate();

// add the delete query and a commit command to the update query
$update->addDeleteQuery('name:testdoc*');
$update->addCommit();

// this executes the query and returns the result
$result = $client->update($update);

echo '<b>Update query executed</b><br/>';
echo 'Query status: ' . $result->getStatus(). '<br/>';
echo 'Query time: ' . $result->getQueryTime();

htmlFooter();

```
