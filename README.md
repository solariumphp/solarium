# Solarium PHP Solr Client Library

## What is Solarium?

Solarium is a PHP Solr client library that accurately models Solr concepts. Where many other Solr libraries only handle
the communication with Solr, Solarium also relieves you of handling all the complex Solr query parameters using a
well documented API.

Please see the [docs](http://solarium.readthedocs.io/en/stable/) for a more detailed description.

## Requirements

Solarium 6.3.2 and up only supports PHP 8.0 and up.

It's highly recommended to have cURL enabled in your PHP environment. However if you don't have cURL available you can
switch from using cURL (the default) to a pure PHP based HTTP client adapter which works for the essential stuff but
doesn't support things like parallel query execution.

Alternatively you can inject any PSR-18 compatible HTTP Client using the `Psr18Adapter`.

## Getting started

The preferred way to install Solarium is by using Composer. Solarium is available on
[Packagist](https://packagist.org/packages/solarium/solarium).

Example:
```sh
composer require solarium/solarium
```

## Pitfalls when upgrading from earlier versions

When upgrading from an earlier version, you should be aware of a number of pitfalls.

* [Pitfall when upgrading to 6.3.6](https://solarium.readthedocs.io/en/stable/getting-started/#pitfall-when-upgrading-to-636)
* [Pitfall when upgrading to 6.3.2](https://solarium.readthedocs.io/en/stable/getting-started/#pitfall-when-upgrading-to-632)
* [Pitfall when upgrading to 6.3](https://solarium.readthedocs.io/en/stable/getting-started/#pitfall-when-upgrading-to-63)
* [Pitfalls when upgrading from 3.x or 4.x or 5.x](https://solarium.readthedocs.io/en/stable/getting-started/#pitfalls-when-upgrading-from-3x-or-4x-or-5x)
* [Pitfall when upgrading from 3.x or 4.x](https://solarium.readthedocs.io/en/stable/getting-started/#pitfall-when-upgrading-from-3x-or-4x)

## Run the examples

To run the examples read through the
[Example code](https://solarium.readthedocs.io/en/stable/getting-started/#example-code)
section of the documentation.

## Run the tests

The phpunit tests contain some integration tests that require a running Solr instance. And this Solr instance requires
some special configuration.
Have a look at `.github/workflows/run-tests.yml` to see how to start a well configured Solr docker container locally.
If you just want to run the unit tests, just ensure that there's no other Solr server listening on the standard port
8983 and the integration tests will be skipped.

You can run the tests in a Windows environment. For all of them to pass, you must make sure to
[checkout with `LF` line endings](https://docs.github.com/en/github/using-git/configuring-git-to-handle-line-endings).

## More information

* Docs
  http://solarium.readthedocs.io/en/stable/

* Issue tracker
  http://github.com/solariumphp/solarium/issues

* Contributors
  https://github.com/solariumphp/solarium/contributors

* License
  See the COPYING file or view online:
  https://github.com/solariumphp/solarium/blob/master/COPYING

## Continuous Integration status

* [![Run Tests](https://github.com/solariumphp/solarium/workflows/Run%20Tests/badge.svg)](https://github.com/solariumphp/solarium/actions)
* [![codecov](https://codecov.io/gh/solariumphp/solarium/branch/master/graph/badge.svg)](https://codecov.io/gh/solariumphp/solarium)
* [![Total Downloads](https://poser.pugx.org/solarium/solarium/downloads.svg)](https://packagist.org/packages/solarium/solarium)
