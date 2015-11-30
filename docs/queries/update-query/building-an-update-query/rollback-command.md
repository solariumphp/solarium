A rollback command withdraws any uncommitted changes.

If you want to use this be carefull not to have the Solr autocommit feature enabled. However in most cases you should try to prevent having to rollback changes. Rollbacks can almost always be avoided by a solid update policy.

Options
-------

This command has no options.

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

// rollback any uncommitted changes on the Solr server
$update->addRollback();

// this executes the query and returns the result
$result = $client->update($update);

echo '<b>Update query executed</b><br/>';
echo 'Query status: ' . $result->getStatus(). '<br/>';
echo 'Query time: ' . $result->getQueryTime();

htmlFooter();

```
