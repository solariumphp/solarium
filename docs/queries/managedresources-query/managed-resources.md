Managed Resources
=================

A Managed Resources query can be used to get metadata about all ManagedResources registered through Solr's RestManager Endpoint.
For more info see <https://solr.apache.org/guide/managed-resources.html#restmanager-endpoint>.

Example
-------

```php
<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// create a managed resources query
$managedResourcesQuery = $client->createManagedResources();

// this executes the query and returns the result
$result = $client->execute($managedResourcesQuery);

// display resources
foreach ($result as $resource) {
    echo '<table>';
    echo '<tr><th>Resource ID</th><td>' . $resource->getResourceId() . '</td></tr>';
    echo '<tr><th>Number of Observers</th><td>' . $resource->getNumObservers() . '</td></tr>';
    echo '<tr><th>Class</th><td>' . $resource->getClass() . '</td></tr>';
    echo '</table><hr/>';
}

htmlFooter();

```
