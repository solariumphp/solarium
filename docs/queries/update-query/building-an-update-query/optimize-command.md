You can use this command to optimize your Solr index. Optimizing 'defragments' your index. The space taken by deleted document data is reclaimed and can merge the index into fewer segments. This can improve search performance a lot.

See this page: <http://wiki.apache.org/solr/SolrPerformanceFactors#Optimization_Considerations> for more info about optimizing.

While 'optimizing' sounds like it's always a good thing to do, you should use it with care, as it can have a negative performance impact *during the optimize process*. If possible use try to use it outside peak hours.

Options
-------

| Name         | Type    | Default value | Description                                                                                                 |
|--------------|---------|---------------|-------------------------------------------------------------------------------------------------------------|
| softcommit   | boolean | null          | Enable or disable softCommit                                                                                |
| waitsearcher | boolean | null          | Block until a new searcher is opened and registered as the main query searcher, making the changes visible. |
| maxsegments  | int     | null          | Optimizes down to at most this number of segments. (available since Solr 1.3)                               |
||

For all options:

-   If no value is set (null) the param will not be sent to Solr and Solr will use it's default setting.
-   See Solr documentation for details of the params

Example
-------

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get an update query instance
$update = $client->createUpdate();

// optimize the index
$update->addOptimize(true, false, 5);

// this executes the query and returns the result
$result = $client->update($update);

echo '<b>Update query executed</b><br/>';
echo 'Query status: ' . $result->getStatus(). '<br/>';
echo 'Query time: ' . $result->getQueryTime();

htmlFooter();

```
