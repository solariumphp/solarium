Exceptions
==========

When you start using Solarium, you'll encounter exceptions. Some can be weeded out during development. Others can't be prevented from cropping up in production. It is good practice to catch these exceptions and act upon them accordingly.

An unreachable endpoint can be emulated to run the following examples "successfully"—that is, causing them to throw an exception!

```php
<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// simulate an unreachable endpoint
$config['endpoint']['localhost']['host'] = '0.0.0.0';

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// create a ping query
$ping = $client->createPing();

// execute the ping query—it'll fail!
$client->execute($ping);
```


All exceptions are created equal
--------------------------------

Every exception thrown by Solarium is a descendant of the PHP base class `Exception`. This is the simplest way to catch them.

```php
try {
    $client->execute($ping);
} catch (Exception $e) {
    echo 'Something went wrong:<br/><br/>';
    echo $e->getMessage();
}
```


Distinguish Solarium exceptions
-------------------------------

Exceptions thrown by Solarium implement a marker interface. This empty interface makes it possible to handle them separately.

```php
try {
    $client->execute($ping);
} catch (Solarium\Exception\ExceptionInterface $e) {
    echo 'Solarium ran into a problem:<br/><br/>';
    echo $e->getMessage();
} catch (Exception $e) {
    echo 'Something else went wrong:<br/><br/>';
    echo $e->getMessage();
}
```


Single out an exception
-----------------------

An exception can be singled out by catching it separately.

```php
try {
    $client->execute($ping);
} catch (Solarium\Exception\HttpException $e) {
    echo 'Solarium can\'t reach your Solr server:<br/><br/>';
    echo $e->getMessage();
} catch (Exception $e) {
    echo 'Something else went wrong:<br/><br/>';
    echo $e->getMessage();
}
```

The distinction can also be made inside the catch block.

```php
try {
    $client->execute($ping);
} catch (Exception $e) {
    echo 'Something went wrong:<br/><br/>';
    echo $e->getMessage();
    if ($e instanceof Solarium\Exception\HttpException) {
        echo '<br/><br/>Better call the network team!';
    }
}
```

