For a description of the Solr Spatial Search see the [https://cwiki.apache.org/confluence/display/solr/Spatial Search Solr wiki page](https://cwiki.apache.org/confluence/display/solr/Spatial+Search "wikilink").

Example
-------

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get a select query instance
$query = $client->createSelect();

// initiate spatial component and set options
$spatial = $query->getSpatial();
$spatial->setDistance($distance);
$spatial->setField('latlon');
$spatial->setPoint($lat. ',' . $lon);

// apply 'geofilt' or 'bbox' filtering
$query->createFilterQuery('latlon')->setQuery('{!geofilt}');

// add distance to results fields
$query->addField('_distance_:geodist()');

// apply sorting by distance
$query->addSort('geodist()', 'ASC');

// this executes the query and returns the result
$resultset = $client->select($query);

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
