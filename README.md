# Solarium PHP Solr client library


## What is Solarium?

Solarium is a PHP Solr client library that accurately model Solr concepts. Where many other Solr libraries only handle
the communication with Solr, Solarium also relieves you of handling all the complex Solr query parameters using a
well documented API.

Please see the docs for a more detailed description.

## Requirements

Solarium only supports PHP 7.1 and up.

It's highly recommended to have Curl enabled in your PHP environment. However if you don't have Curl available you can
switch from using Curl (the default) to another client adapter. The other adapters don't support all the features of the
Curl adapter.

## Getting started

The preferred way to install Solarium is by using Composer. Solarium is available on Packagist.

Example:
```
composer require solarium/solarium
```

## Run the examples

This needs a git checkout using composer.

### Add repository section

Add or update the repository sections in your `composer.json` file.

```
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:solariumphp/solarium.git"
        }
    ]
```

### Reinstall

You need to fetch the newly configured git checkout. This will fetch the examples too.

```
composer install
```

### Make a copy of settings.dist.php

You must copy this file into your project root.

```
cp ./vendor/solarium/solarium/examples/config.dist.php ./config.php
```

Now you can change the `./config.php` if you are not using the default solr settings.

### Start webserver

Start the webserver from within the project root.

```
php -S localhost:8888
```

### Run the examples

Open http://localhost:8888/vendor/solarium/solarium/examples/

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

* [![Develop build status](https://secure.travis-ci.org/solariumphp/solarium.png?branch=master)](http://travis-ci.org/solariumphp/solarium?branch=master) [![Coverage Status](https://coveralls.io/repos/solariumphp/solarium/badge.png?branch=master)](https://coveralls.io/r/solariumphp/solarium?branch=master)
* [![SensioLabsInsight](https://insight.sensiolabs.com/projects/292e29f7-10a9-4685-b9ac-37925ebef9ae/small.png)](https://insight.sensiolabs.com/projects/292e29f7-10a9-4685-b9ac-37925ebef9ae)
* [![Total Downloads](https://poser.pugx.org/solarium/solarium/downloads.svg)](https://packagist.org/packages/solarium/solarium)

