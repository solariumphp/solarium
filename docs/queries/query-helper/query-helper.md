As you could probably already guess by the name, the query helper class is there to help you create query strings. It offers various methods to simplify the complex parts of a query string into an easy to use API. This includes term/phrase escaping, range queries and (geospatial) functions.

You can get a query helper instance by calling the 'getHelper()' method of your query instance. This is available in all query classes.

Two special types of helper methods are

Helper methods for general use
------------------------------

-   rangeQuery($field, $from, $to, $inclusive = true)
-   qparser($name, $params = array())
-   functionCall($name, $params = array())
-   join($from, $to, $dereferenced = false)
-   formatDate($input)
-   cacheControl($useCache, $cost)
-   qparserTerm($field, $weight)

See the API docs (linked at the bottom of this page) for more details.

Dereferenced params
-------------------

The query helper also supports dereferenced params. See the implementation of the join() and qparser() methods. For more info also see <http://wiki.apache.org/solr/LocalParams>: Parameter dereferencing or indirection allows one to use the value of another argument rather than specifying it directly. This can be used to simplify queries, decouple user input from query parameters, or decouple front-end GUI parameters from defaults set in solrconfig.xml.

Special helper methods
----------------------

See the next pages for more info about escaping, placeholders and geospatial helpers.

Example
-------

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get a select query instance and a query helper instance
$query = $client->createSelect();
$helper = $query->getHelper();

// add a filterquery on a price range, using the helper to generate the range
$query->createFilterQuery('price')->setQuery($helper->rangeQuery('price', 10, 300));

// add a filterquery to find products in a range of 5km, using the helper to generate the 'geofilt' filter
$query->createFilterQuery('region')->setQuery($helper->geofilt(45.15, -93.85, 'store', 5));

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
