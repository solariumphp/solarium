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

Adapters support authentication. To use this set the authentication on the request object using the setAuthentication() method.

### HTTP request timeout handling

Setting a timeout for the HTTP request handling is the responsibility of the adapters. The two built-in adapters `CurlAdapter` and `HttpAdapter` are implementing `TimeoutAwareInterface` and expose a `setTimeout` method to give you control over the timeout value that is used.

If you are using any other adapter like the built-in `Psr18Adapter` you need to take care of handling the timeouts yourself and configure the HTTP client properly that is used to perform the requests.

Endpoints
---------

An endpoint is basically a collection of settings that define a Solr server or core. Each endpoint is defined with a key. For each query you execute you can (optionally) supply an endpoint or endpoint key, and the query is executed using this endpoint, using the client and adapter instance. The first endpoint you define is automatically used as the default endpoint. This makes using a single endpoint easier, as you don’t need to pass it to execute queries. Of course you can always set your own default endpoint if needed.

The endpoint class has a \_\_toString method that output all settings, this can be very useful for debugging or logging.

### Authentication

Endpoints support authentication. To use this set the authentication on the endpoint object using the setAuthentication() method.


cURL adapter
============

This is the standard Solarium adapter. It supports the most features (for instance concurrent requests) and doesn't suffer from memory issues (like the HttpAdapter in some cases). The only downside is that it depends on the PHP cURL extension, however most PHP environment have this extension. If cURL is not available and installing is not an option you should use one of the other adapters.

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();

// create an HTTP adapter instance
$adapter = new Solarium\Core\Client\Adapter\Http();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

htmlFooter();

```

### Proxy support

The cURL adapter support the use of a proxy. Use the adapter option `proxy` to enable this.


HTTP adapter
============

This adapter has no dependencies on other classes or any special PHP extensions as it uses basic PHP streams. This makes it a safe choice, but it has no extra options. If you need detailed control over your request or response you should probably use another adapter, but for most standard cases it will do just fine.

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();

// create an HTTP adapter instance
$adapter = new Solarium\Core\Client\Adapter\Http();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

htmlFooter();

```

PSR-18 adapter
==============

Since Solarium 5.2 there is also a `Psr18Adapter` which can be used with any PSR-18 compliant HTTP client.

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a PSR-18 adapter instance
$httpClient = new Http\Adapter\Guzzle6\Client();
$factory = new Nyholm\Psr7\Factory\Psr17Factory();
$adapter = new Solarium\Core\Client\Adapter\Psr18Adapter($httpClient, $factory, $factory);

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

htmlFooter();

```

Custom adapter
==============

You can also use a custom adapter, with these steps:

-   Create your custom adapter class. It should implement Solarium\\Core\\Client\\Adapter\\AdapterInterface.
-   You can take a look at the existing implementations as an example.
-   Pass an instance of your adapter as the first argument to the Solarium\\Client constructor.
-   Now use Solarium as you normally would, all communication to Solr will be done using your adapter.

