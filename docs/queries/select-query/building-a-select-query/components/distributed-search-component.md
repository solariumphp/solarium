For a description of Solr distributed search (also referred to as 'shards' or 'sharding') see the [http://wiki.apache.org/solr/DistributedSearch\#Distributed\_Search\_Example Solr wiki page](http://wiki.apache.org/solr/DistributedSearch#Distributed_Search_Example_Solr_wiki_page "wikilink").

Options
-------

| Name         | Type   | Default value | Description                                                                |
|--------------|--------|---------------|----------------------------------------------------------------------------|
| shards       | string | null          | Shards to use for request                                                  |
| shardhandler | string | null          | Request handler to use                                                     |
| collections  | string | null          | A list of collections, for use with SolrCloud (available in Solarium 3.1+) |
| replicas     | string | null          | A list of replicas, for use with SolrCloud (available in Solarium 3.1+)    |
||

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

// add distributed search settings
// see http://wiki.apache.org/solr/DistributedSearch#Distributed_Search_Example for setting up two solr instances
$distributedSearch = $query->getDistributedSearch();
$distributedSearch->addShard('shard1', 'localhost:8983/solr');
$distributedSearch->addShard('shard2', 'localhost:7574/solr');

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
