This command commits any buffered commands to the index. This buffering is done by the Solr server, depending on the configuration. It's a global buffer, so a commit command might also commit commands that where buffered from a previous update request that had no commit command.

If you use the autoCommit feature on the Solr core you probably don't want to use the commit command, because that would trigger a commit in between the autoCommit interval. In all other cases it makes sense to add a commit to your update (or at least to the last update request if you issue multiple) to apply your update to the Solr index.

This command starts the commit process on the server, bit the actual commit process might take a while. If your update command with a commit returns successfully this only means Solr received the commands and is processing them, the result might not be visible for a while. The time this takes depends on many factors: hardware, index size, number of index segments, the commands to execute, the search load, etcetera. It can vary between milliseconds and minutes.

Options
-------

| Name           | Type    | Default value | Description                                                                                                 |
|----------------|---------|---------------|-------------------------------------------------------------------------------------------------------------|
| softcommit     | boolean | null          | Enable or disable softCommit                                                                                |
| waitsearcher   | boolean | null          | Block until a new searcher is opened and registered as the main query searcher, making the changes visible. |
| expungedeletes | boolean | null          | Merge segments with deletes away (available since solr 1.4)                                                 |
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

// add the delete id and a commit command to the update query
$update->addDeleteById(123);
$update->addCommit();

// this executes the query and returns the result
$result = $client->update($update);

echo '<b>Update query executed</b><br/>';
echo 'Query status: ' . $result->getStatus(). '<br/>';
echo 'Query time: ' . $result->getQueryTime();

htmlFooter();

```
