<?php

require_once(__DIR__.'/init.php');
htmlHeader();

echo "<h2>Note: The techproducts isn't distributed by default!</h2>";

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect();

// add distributed search settings
// see https://solr.apache.org/guide/distributed-search-with-index-sharding.html#testing-index-sharding-on-two-local-servers for setting up two Solr instances
$distributedSearch = $query->getDistributedSearch();
$distributedSearch->addShard('shard1', 'localhost:8983/solr');
$distributedSearch->addShard('shard2', 'localhost:7574/solr');

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by Solr
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
