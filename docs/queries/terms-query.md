A terms query provides access to the indexed terms in a field. For details see: <http://wiki.apache.org/solr/TermsComponent>

Building a terms query
----------------------

See the example code below.

**Available options:**

| Name              | Type    | Default value | Description                                   |
|-------------------|---------|---------------|-----------------------------------------------|
| fields            | string  | null          | Comma separated list of fields                |
| lowerbound        | string  | null          | Set the lowerbound term to start at           |
| lowerboundinclude | boolean | null          |                                               |
| mincount          | int     | null          |                                               |
| maxcount          | int     | null          |                                               |
| prefix            | string  | null          | Set prefix for terms                          |
| regex             | string  | null          | Set regex to restrict terms                   |
| regexflags        | string  | null          | Comma separated list of regex flags           |
| limit             | int     | null          | If &lt;0 all terms are included               |
| upperbound        | string  | null          |                                               |
| upperboundinclude | boolean | null          |                                               |
| raw               | boolean | null          | Return raw characters of the term             |
| sort              | string  | null          | Set sorting of the terms ('count' or 'index') |
||

Executing a terms query
-----------------------

Use the `terms` method of the client to execute the query object. See the example code below.

Result of a terms query
-----------------------

The result of a terms query offers direct access to the resulting terms, and can also be iterated.

Example
-------

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get a terms query instance
$query = $client->createTerms();
$query->setFields('features,name');
$query->setLowerbound('i');

// this executes the query and returns the result
$resultset = $client->terms($query);

// display terms
foreach ($resultset as $field => $terms) {
    echo '<h3>' . $field . '</h3>';
    foreach ($terms as $term => $count) {
        echo $term . ' (' . $count . ')<br/>';
    }
    echo '<hr/>';
}

htmlFooter();

```
