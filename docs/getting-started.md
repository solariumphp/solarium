Getting started
===============

In this part the installation of Solarium is covered and a quick-start with some simple queries. If you intend to read the rest of the manual you might skip the example queries, they will be covered in-depth in their own chapters.


Installation
============

### Requirements

For installing Solarium a minimal PHP version 5.3 is required. While previous Solarium versions with any PHP5 version, Solarium 3 uses interfaces so 5.3+ is a hard requirement.

There is no Solr version requirement. Solr versions 1.4 and upwards have been tested with Solarium. Ofcourse, if you want to use version specific features like spatial search you need the right Solr version. For spatial search you will need at least 3.1.

### Getting Solarium

There are several ways to get Solarium. The preferred method is by using Composer. Alternatively you can download a prepacked release from GitHub, or use git. Only Composer is described here.

First of all, if you're not familiar with Composer take a look here: [<http://getcomposer.org>](http://getcomposer.org). Composer is quickly becoming the standard for handling dependencies in PHP apps and many libraries support it. As of version 3 Solarium depends on an external library, the Symfony Event Dispatcher component. Composer automatically manages this dependency.

See [<https://packagist.org>](https://packagist.org) for other packages.

- Make sure you have composer available / installed (see the getting started section on the Composer site)

- Add Solarium to your composer.json file. It should look something like this:

```json
{
    "require": {
        "solarium/solarium": "3.6.0"
    }
}
```

- Run composer install

- Make sure to use the composer autoloader, and Solarium should be available.

**Only if you don't use composer:** you need to use a PSR-0 autoloader, or the supplied autoloader: ```php

Also you need to make sure the the Symfony Event Dispatcher component is available.

### Checking the availability

To check your installation you can do a Solarium version check with the following code. If everything works you should see the version of Solarium you downloaded. To test Solr communication you can use a ping query (you might need some configuration to get the ping working, more on that later) 

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();

// check solarium version available
echo 'Solarium library version: ' . Solarium\Client::VERSION . ' - ';

// create a client instance
$client = new Solarium\Client($config);

// create a ping query
$ping = $client->createPing();

// execute the ping query
try {
    $result = $client->ping($ping);
    echo 'Ping query successful';
    echo '<br/><pre>';
    var_dump($result->getData());
    echo '</pre>';
} catch (Solarium\Exception $e) {
    echo 'Ping query failed';
}

htmlFooter();

```

### Available integrations

Some users of Solarium have been nice enough to create easy ways of integrating Solarium:

-   A Solarium bundle for Symfony2 <https://github.com/nelmio/NelmioSolariumBundle>
-   Zend Framework 2 module <https://zfmodules.com/Ewgo/SolariumModule>
-   Lithium <https://github.com/joseym/li3_solr>
-   Fuel PHP <https://github.com/bgrimes/fuelphp-solarium>
-   Yii <https://github.com/estahn/YiiSolarium>
-   Magento <https://github.com/jeroenvermeulen/magento-solarium>
-   Wordpress <https://github.com/pantheon-systems/solr-for-wordpress>

If you know of any other integrations please let it know!


Basic usage
===========

All the code display below can be found in the /examples dir of the project, where you can also easily execute the code. For more info see [Example code](V3:Example_code "wikilink").

All the examples use the init.php file. This file registers the Solarium autoloader and also loads the $config array for use in the client constructor. The $config array has the following contents: 

```php
<?php

$config = array(
    'endpoint' => array(
        'localhost' => array(
            'host' => '127.0.0.1',
            'port' => 8983,
            'path' => '/solr/',
        )
    )
);

```

### Selecting documents

This is the basic example of executing a select query and displaying the results: 

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get a select query instance
$query = $client->createQuery($client::QUERY_SELECT);

// this executes the query and returns the result
$resultset = $client->execute($query);

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

See the docs for all options.

### Facet field

This example demonstrates a facet field.

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get a select query instance
$query = $client->createSelect();

// get the facetset component
$facetSet = $query->getFacetSet();

// create a facet field instance and set options
$facetSet->createFacetField('stock')->setField('inStock');

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

### Deleting documents

Documents can be deleted with a query: 

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

Or by id 

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get an update query instance
$update = $client->createUpdate();

// add the delete id and a commit command to the update query
$update->addDeleteById(123);
$update->addCommit();

// this executes the query and returns the result
$result = $client->update($update);

echo '<b>Update query executed</b><br/>';
echo 'Query status: ' . $result->getStatus(). '<br/>';
echo 'Query time: ' . $result->getQueryTime();

htmlFooter();

```

Also, a combination of both is possible. See the docs for more info.

### Adding documents

This example adds some documents to the index: 

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

For all options (like boosting) see the docs.


Example code
============

With Solarium a set of examples is included to demonstrate the usage and to test your Solr environment. To get the examples working you need to do the following:

1.  make the /examples folder browseable on your (test)webserver.
2.  download a standard Solr release and start the included Jetty example (see the Solr example/README.txt file)
3.  load the demo data into the example index (again see the Solr example/README.txt file)
4.  open the example dir in a browser, the index.html file listing all available examples should show up

That's all! The default config file of the examples was made for the standard Solr example included with a Solr release. If you want to use a custom Solr environment you can copy the file 'config.dist.php' in the example dir to 'config.php' and correct the settings. Your environment needs to have the default Solr example schema and data for the examples to work.

If examples for some Solarium functionality are missing please request them by opening an issue in the issue tracker: [<http://github.com/solariumphp/solarium/issues>](http://github.com/solariumphp/solarium/issues)
