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

The Http, Curl and Pecl adapter support authentication. To use this set the authentication on the request object using the setAuthentication() method. For the ZendHttp adapter you set the authentication using the ZendHttp api or config.

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


PeclHttp adapter
================

The PeclHttp adapter makes use of the [pecl\_http package](http://pecl.php.net/package/pecl_http). So to use this adapter you need to have Pecl Http installed.

The functionality offered by this adapter is the same as the default adapter, but the HTTP requests are executed using Pecl Http.

```php
<?php

require_once 'Zend/Loader/Autoloader.php';
$loader = Zend_Loader_Autoloader::getInstance();

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// set the adapter to peclhttp
$client->setAdapter('Solarium\Core\Client\Adapter\PeclHttp');

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

HttpAdapter
===========

This adapter has no dependencies on other classes or any special PHP extensions as it uses basic PHP streams. This makes it a safe choice, but it has no extra options. If you need detailed control over your request or response you should probably use another adapter, but for most standard cases it will do just fine.

```php
<?php

require_once 'Zend/Loader/Autoloader.php';
$loader = Zend_Loader_Autoloader::getInstance();

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// set the adapter to curl
$client->setAdapter('Solarium\Core\Client\Adapter\Http');

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

ZendHttp adapter
================

The ZendHttp adapter makes use of the Zend\_Http component in Zend Framework (version 1). So to use this adapter you need to have ZF available. By using Zend\_Http all the features of this component are available:

-   multiple adapter implementations
-   keepalive
-   cookies / sessions
-   redirection support
-   http authentication
-   and much more, see the [http://framework.zend.com/manual/en/zend.http.html Zend Http manual](http://framework.zend.com/manual/en/zend.http.html_Zend_Http_manual "wikilink")

The base functionality is the same as the default adapter. The only difference is that this adapter allows you to set Zend\_Http options and also offers access to the Zend\_Http instance.

```php
<?php

require_once 'Zend/Loader/Autoloader.php';
$loader = Zend_Loader_Autoloader::getInstance();

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// set the adapter to zendhttp and get a zendhttp client instance reference
$client->setAdapter('Solarium\Core\Client\Adapter\ZendHttp');
$zendHttp = $client->getAdapter()->getZendHttp();

// you can use any of the zend_http features, like http-authentication
$zendHttp->setAuth('user', 'password!', Zend_Http_Client::AUTH_BASIC);

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

Custom adapter
==============

You can also use a custom adapter, with these steps:

-   Create your custom adapter class. It should implement Solarium\\Core\\Client\\Adapter\\AdapterInterface.
-   You can take a look at the existing implementations as an example.
-   Make sure your class is available to Solarium, by including it manually or through autoloading.
-   Call the 'setAdapter' method on your Solarium client instance with your own adapters' classname as argument (or use the 'adapter' config setting)
-   Now use Solarium as you normally would, all communication to Solr will be done using your adapter. The adapter class will only be instantiated on the first communication to Solr, not directly after calling 'setAdapter' (lazy loading)

