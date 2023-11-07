Exceptions
==========

When you start using Solarium, you'll encounter exceptions. Some can be weeded out during development. Others can't be prevented from cropping up in production. It is good practice to catch these exceptions and act upon them accordingly.

Catching Solarium exceptions
----------------------------

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


### All exceptions are created equal

Every exception thrown by Solarium is a descendant of the PHP base class `Exception`. This is the simplest way to catch them.

```php
try {
    $client->execute($ping);
} catch (Exception $e) {
    echo 'Something went wrong:<br/><br/>';
    echo $e->getMessage();
}
```


### Distinguish Solarium exceptions

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


### Single out an exception

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

The distinction can also be made inside the catch block for a generic `Exception`.

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


Overview of Solarium exceptions
-------------------------------

### Logic exceptions

Logic exceptions represent errors in the program logic. You should prevent them from happening with proper input validation in your code.

Solarium can throw two types of logic exceptions. Both extend their SPL counterpart to implement `Solarium\Exception\ExceptionInterface` and `Solarium\Exception\LogicExceptionInterface`.

| Exception                                     | Extends                    |
| --------------------------------------------- | -------------------------- |
| `Solarium\Exception\DomainException`          | `DomainException`          |
| `Solarium\Exception\InvalidArgumentException` | `InvalidArgumentException` |


### Runtime exceptions

Runtime exceptions represent errors that can only be found on runtime.

Solarium can throw a number of runtime exceptions. All of them implement `Solarium\Exception\ExceptionInterface` and `Solarium\Exception\RuntimeExceptionInterface`.

| Exception                                     | Extends                    |
| --------------------------------------------- | -------------------------- |
| `Solarium\Exception\HttpException`            | `RuntimeException`         |
| `Solarium\Exception\OutOfBoundsException`     | `OutOfBoundsException`     |
| `Solarium\Exception\RuntimeException`         | `RuntimeException`         |
| `Solarium\Exception\StreamException`          | `UnexpectedValueException` |
| `Solarium\Exception\UnexpectedValueException` | `UnexpectedValueException` |

Those that extend their SPL counterparts do so to implement the marker interfaces. Solarium also introduces two exceptions that are more specific.


#### `HttpException`

This exception indicates that a problem occurred in the communication with the Solr server. You should catch this (or a more generic exception) for every request that is executed.

When using the [ParallelExecution plugin](plugins.md#parallelexecution-plugin), this exception isn't thrown. Instead it's added to the result array and you have to check the type in your code.


#### `StreamException`

This exception indicates that a problem occurred with a streaming expression. You should catch this (or a more generic exception) for every streaming expression request that is executed.
