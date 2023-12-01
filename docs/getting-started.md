Getting started
===============

In this part the installation of Solarium is covered and a quick-start with some simple queries. If you intend to read the rest of the manual you might skip the example queries, they will be covered in-depth in their own chapters.


Installation
------------

### Requirements

For installing Solarium a minimal PHP version 8.0 is required. It's highly recommended to always use an [actively supported PHP version](https://www.php.net/supported-versions.php) in production.

There is no Solr version requirement. But Solarium development is only actively tested against Solr 7.7 and higher. It's highly recommended to always use an [actively supported Solr version](https://solr.apache.org/downloads.html#about-versions-and-support).

### Getting Solarium

There are several ways to get Solarium. The preferred method is by using Composer. Alternatively you can download a prepacked release from GitHub, or use git. Only Composer is described here.

First of all, if you're not familiar with Composer take a look here: [<http://getcomposer.org>](http://getcomposer.org). Composer has become the de facto standard for handling dependencies in PHP apps and many libraries support it.

See [<https://packagist.org>](https://packagist.org) for other packages.

- Make sure you have Composer available / installed (see the getting started section on the Composer site).

- Add Solarium to your `composer.json` file. It should look something like this:

```json
{
    "require": {
        "solarium/solarium": "~6.3"
    }
}
```

- Run `composer install`.

- Make sure to use the Composer autoloader, and Solarium should be available.

**Only if you don't use Composer:** you need to use a PSR-0 autoloader, or the supplied autoloader.

Also you need to make sure that a PSR-14 compatible event dispatcher is available, for instance, the Symfony EventDispatcher:

```json
{
    "require": {
        "solarium/solarium": "~6.3",
        "symfony/event-dispatcher": "^5.0 || ^6.0"
    }
}
```

### Checking the availability

To check your installation you can do a Solarium version check with the following code. If everything works you should see the version of Solarium you downloaded. To test Solr communication you can use a ping query (you might need some configuration to get the ping working, more on that later) 

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

    echo 'Ping query successful<br/><br/>';
    echo 'Ping status: ' . $result->getPingStatus() . '<br/>';
    echo 'Query time: ' . $result->getQueryTime() . ' ms';
} catch (Exception $e) {
    echo 'Ping query failed';
}

htmlFooter();

```


Upgrading
---------

When upgrading from an earlier version, you should be aware of a number of pitfalls.

### Pitfall when upgrading to 6.3.2

Support for PHP 7 was removed in Solarium 6.3.2. Upgrade to PHP 8 first to use the latest Solarium version.

### Pitfall when upgrading to 6.3

With Solarium 6.3 update queries use the JSON request format by default.

If you do require XML specific functionality, set the request format to XML explicitly.

```php
// get an update query instance
$update = $client->createUpdate();

// set XML request format
$update->setRequestFormat($update::REQUEST_FORMAT_XML);
```

### Pitfalls when upgrading from 3.x or 4.x or 5.x

#### Setting a timeout

Setting "timeout" as "option" in the HTTP Client Adapter is deprecated since Solarium 5.2.0 because not all adapters
could handle it. The adapters which can handle it now implement the `TimeoutAwareInterface` and you need to set the
timeout using the `setTimeout()` function after creating the adapter instance.

#### `Solarium\Client()` constructor

With Solarium 6 you need to pass an adapter instance and an event dispatcher instance to the `Solarium\Client()`
constructor as the first and second parameter. An optional options array can now be passed as the third parameter.
Previous versions used the `Curl` adapter and the Symfony EventDispatcher by default. Solarium 5.2
already informed you about the deprecation of calling this constructor with the old signature.

Solarium 5:
```php
$options = [
    // ...
];

$client = new Solarium\Client($options);
```

Solarium 6:
```php
$adapter = new Solarium\Core\Client\Adapter\Curl();
$eventDispatcher = new Symfony\Component\EventDispatcher\EventDispatcher();
$options = [
    // ...
];

$client = new Solarium\Client($adapter, $eventDispatcher, $options);
```

The Symfony EventDispatcher is also no longer automatically available for autoloading.
If you want to keep using it, you can add it to your project's `composer.json`.
Alternatively you can use any [PSR-14](https://www.php-fig.org/psr/psr-14/) compatible event dispatcher.

```json
{
    "require": {
        "solarium/solarium": "~6.3",
        "symfony/event-dispatcher": "^5.0 || ^6.0"
    }
}
```

#### Adapters

The `Zend2HttpAdapter`, `GuzzleAdapter`, and `Guzzle3Adapter` were removed in Solarium 6.
You can use the `Psr18Adapter` with any [PSR-18](https://www.php-fig.org/psr/psr-18/) compliant HTTP client instead.

Example:
```sh
composer require php-http/guzzle7-adapter
composer require nyholm/psr7
```

```php
$httpClient = new Http\Adapter\Guzzle7\Client();
$factory = new Nyholm\Psr7\Factory\Psr17Factory();
$adapter = new Solarium\Core\Client\Adapter\Psr18Adapter($httpClient, $factory, $factory);
```

#### Local parameter names

In order to fix some issues with complex queries using local parameters Solarium 6 distinguishes between query parameters
and local parameters to be embedded in a query. Solarium 5.2 already informed you about the deprecation of some
parameter names which are in fact local parameters. Solarium doen't convert them magically anymore.
Local parameter names now have to be prefixed with `local_` if set as option of a constructor.

Solarium 5:
```php
$categoriesTerms = new Solarium\Component\Facet\JsonTerms([
    'key' => 'categories',
    'field' => 'cat',
    'limit' => 4,
    'numBuckets' => true,
]);
```

Solarium 6:
```php
$categoriesTerms = new Solarium\Component\Facet\JsonTerms([
    'local_key' => 'categories',
    'field' => 'cat',
    'limit' => 4,
    'numBuckets' => true,
]);
```

See https://solr.apache.org/guide/local-parameters-in-queries.html for an introduction about local parameters.

### Pitfall when upgrading from 3.x or 4.x

In the past, the V1 API endpoint `solr` was not added automatically, so most users set it as path on the endpoint.
This bug was discovered with the addition of V2 API support. In almost every setup, the path has to be set to `/`
instead of `/solr` with this release!

For the same reason it is a must to explicitly configure the _core_ or _collection_.

So an old setting like
```
'path' => '/solr/xxxx/'
```
has to be changed to something like
```
'path' => '/',
'collection' => 'xxxx',
```

This led to a problem if the endpoint _isn't_ the default `solr`. Since 6.2.1, a different context can be configured.

An old settings like
```
'path' => '/index/xxxx/'
```
can be changed to something like
```
'path' => '/',
'context' => 'index',
'collection' => 'xxxx',
```

This works for SolrCloud instances with a non-default `hostContext` and Solr instances behind a reverse proxy.


Available integrations
----------------------

Some users of Solarium have been nice enough to create easy ways of integrating Solarium:

-   A Solarium bundle for Symfony2 <https://github.com/nelmio/NelmioSolariumBundle>
-   Zend Framework 2 module <https://github.com/Ewgo/SolariumModule>
-   Lithium <https://github.com/joseym/li3_solr>
-   Fuel PHP <https://github.com/bgrimes/fuelphp-solarium>
-   Yii <https://github.com/estahn/YiiSolarium>
-   Drupal <https://www.drupal.org/project/search_api_solr>
-   Typo3 <https://extensions.typo3.org/extension/solr/>
-   Magento <https://github.com/jeroenvermeulen/magento-solarium>
-   Wordpress <https://github.com/pantheon-systems/solr-for-wordpress>
-   SilverStripe <https://github.com/firesphere/silverstripe-solr-search>

If you know of any other integrations please let us know!


Basic usage
-----------

All the code displayed below can be found in the /examples dir of the project, where you can also easily execute the code. For more info see [Example code](V3:Example_code "wikilink").

All the examples use the init.php file. This file registers the Solarium autoloader, sets up a client adapter and event dispatcher, and also loads the `$config` array for use in the client constructor.

A client adapter and event dispatcher can be set up like this:

```php
<?php

$adapter = new Solarium\Core\Client\Adapter\Curl(); // or any other adapter implementing AdapterInterface
$eventDispatcher = new Symfony\Component\EventDispatcher\EventDispatcher();
```

The `$config` array has the following contents: 

```php
<?php

$config = array(
    'endpoint' => array(
        'localhost' => array(
            'host' => '127.0.0.1',
            'port' => 8983,
            'path' => '/',
            'core' => 'techproducts',
            // For SolrCloud you need to provide a collection instead of core:
            // 'collection' => 'techproducts',
            // Set the `hostContext` for the Solr web application if it's not the default 'solr':
            // 'context' => 'solr',
        )
    )
);
```

### Selecting documents

This is the basic example of executing a select query and displaying the results: 

```php
<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createQuery($client::QUERY_SELECT);

// this executes the query and returns the result
$resultset = $client->execute($query);

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

See the docs for all options.

### Facet field

This example demonstrates a facet field.

```php
<?php

require_once(__DIR__.'/init.php');
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

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

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

Or by id: 

```php
<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

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

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

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

### Status and query time

As shown in the update query examples above, you can get the status and query time from a query result.
The normal Solr status code for succes is `0`.
The query time is the execution time in milliseconds as reported by Solr and doesn't include network transfer times.

This is possible for every query type if a response header is returned from Solr alongside the result. Solarium
requests this header by default for [Ping](queries/ping-query.md) and [Update](queries/update-query/update-query.md)
queries, you have to set it explicitly on the query if you want this returned for other query types.

```php
$query = $client->createSelect();
$query->setOmitHeader(false);

$result = $client->select($query);
echo 'Query status: ' . $result->getStatus();
echo 'Query time: ' . $result->getQueryTime();
```


Example code
------------

With Solarium a set of examples is available to demonstrate the usage and to test your Solr environment. But since the
examples are not included in the distribution you need a git checkout of Solarium and install the dependencies:
```
git clone https://github.com/solariumphp/solarium.git
cd solarium
composer install
```

Afterwards you need to configure a web server to use the `examples` folder as docroot. But the easiest way is to use the
built-in web server of PHP. To do so continue like this:
```
cd examples
php -S localhost:8888
```

Now open `http://localhost:8888/examples` in your browser and follow the instructions.
