Client and adapters
===================

Client
------

The client (class Solarium\\Client) is the main interface of Solarium, a sort of gateway. It holds config settings and has method to access all Solarium functionality. It controls the calling of many underlying Solarium classes but has very little built-in functionality itself.

This allows for a lightweight class, so that you can have it available at all times at very little cost. The Solarium\\Client class uses lazy loading where possible. By having all functionality implemented in subclasses you can also easily customize behaviour by altering the mapping to these subclasses, while still maintaining the same client API.

The name 'Client' might be somewhat confusing. It was chosen because Solarium is a Solr client library and this is the main class. But the client is not actually communicating with Solr. That part is done by the client adapters.

Adapters
--------

The adapters are the actual implementations for communication with Solr. They have a generic interface, but different implementations. They are purely for executing requests, and hold no state.

### Authentication

The Http and Curl adapter support authentication. To use this set the authentication on the request object using the setAuthentication() method.

### HTTP request timeout handling

Setting a timeout for the HTTP request handling is the responsibility of the Adapters.
The two build-in Adapters `Curl` and `Http` are implementing `TimeoutAwareInterface` and expose a `setTimeout`
method to give you control over the timeout value that is used.

If you are using any other adapter like the build-in `Psr18Adapter` you need to take care of handling
the timeouts yourself and configure the HTTP client properly that is used to perform the requests.

Endpoints
---------

An endpoint is basically a collection of settings that define a solr server or core. Each endpoint is defined with a key. For each query you execute you can (optionally) supply an endpoint or endpoint key, and the query is executed using this endpoint, using the client and adapter instance. The first endpoint you define is automatically used as the default endpoint. This makes using a single endpoint easier, as you don’t need to pass it to execute queries. Of course you can always set your own default endpoint if needed.

The endpoint class has a \_\_toString method that output all settings, this can be very useful for debugging or logging.

### Authentication

Endpoints support authentication. To use this set the authentication on the endpoint object using the setAuthentication() method.


Curl adapter
============

This is the standard Solarium adapter. It supports the most features (for instance concurrent requests) and doesn't suffer from memory issues (like the HttpAdapter in some cases). The only downside is that it depends on the PHP Curl extension, however most PHP environment have this extension. If Curl is not available and installing is not an option you should use one of the other adapters.

As this is the default adapter you don't need any settings or API calls to use it.

### Proxy support

The curl adapter support the use of a proxy. Use the adapter option `proxy` to enable this.


Http adapter
===========

This adapter has no dependencies on other classes or any special PHP extensions as it uses basic PHP streams. This makes it a safe choice, but it has no extra options. If you need detailed control over your request or response you should probably use another adapter, but for most standard cases it will do just fine.

```php
<?php

require_once 'Zend/Loader/Autoloader.php';
$loader = Zend_Loader_Autoloader::getInstance();

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client(
    new Solarium\Core\Client\Adapter\Http(), 
    new Symfony\Component\EventDispatcher\EventDispatcher(),
    $config
);

// get a select query instance
$query = $client->createSelect();

// this executes the query and returns the result
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

Psr-18 adapter
============

Since Solarium 5.2 there is also a `Psr18Adapter` which can be used with any PSR-18 compliant HTTP client.

Example:

```php
<?php

use Nyholm\Psr7\Factory\Psr17Factory;
use Solarium\Client;
use Http\Adapter\Guzzle6\Client as GuzzlePsrClient;
use Solarium\Core\Client\Adapter\Psr18Adapter;
use Symfony\Component\EventDispatcher\EventDispatcher;

$adapter = new Psr18Adapter(
    new GuzzlePsrClient(),
    new Psr17Factory(),
    new Psr17Factory()
);

$client = new Client($adapter, new EventDispatcher());
```

Custom adapter
==============

You can also use a custom adapter, with these steps:

-   Create your custom adapter class. It should implement Solarium\\Core\\Client\\Adapter\\AdapterInterface.
-   You can take a look at the existing implementations as an example.
-   Make sure your class is available to Solarium, by including it manually or through autoloading.
-   Inject your custom adapter instance into the constructor of the Client
-   Now use Solarium as you normally would, all communication to Solr will be done using your adapter. The adapter class will only be instantiated on the first communication to Solr, not directly after calling 'setAdapter' (lazy loading)
