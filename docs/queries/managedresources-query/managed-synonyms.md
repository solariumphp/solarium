Managed Synonyms
================

A Managed Synonyms query can be used for CRUD operations against Solr's managed resources REST API endpoint.
For more info see <https://solr.apache.org/guide/managed-resources.html>.

The default operation of a query is reading a synonym list or a single synonym mapping in a list.
Other operations require an explicit command be set on the query.

When you create a command, you get an object that extends `AbstractCommand`. Due to the way command creation works the return type isn't more specific than that.
If you run into "method not found" errors with your static analyzer, or want to enable autocompletion in your IDE, you can add a type annotation to narrow it down. E.g.:

```php
/** @var Solarium\QueryType\ManagedResources\Query\Command\Synonyms\Add $addCommand */
$addCommand = $query->createCommand($query::COMMAND_ADD);
```

The specific class of the result is only known at runtime because it depends on whether or not a command was set on the query. This too can be narrowed with a type annotation.

```php
// without a command set on the query
/** @var Solarium\QueryType\ManagedResources\Result\Synonyms\SynonymMappings $result */
$result = $client->execute($query);

// with a command set on the query
/** @var Solarium\QueryType\ManagedResources\Result\Command $result */
$result = $client->execute($query);
```

Changes are not applied to the active Solr components until the core or collection is reloaded.

Examples
--------

```php
<?php

use Solarium\QueryType\ManagedResources\Query\Synonyms\Synonyms;

require_once __DIR__.'/init.php';

htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

echo '<h1>Commands that operate on a synonym list</h1>';

echo '<h2>Get list</h2>';

// create a managed synonyms query
$query = $client->createManagedSynonyms();

// set the name of the synonym list
$query->setName('english');

// execute the query and return the result
/** @var Solarium\QueryType\ManagedResources\Result\Synonyms\SynonymMappings $result */
$result = $client->execute($query);

// display list properties
echo '<b>Case sensitive:</b> '.($result->isIgnoreCase() ? 'no' : 'yes').'<br/>';
echo '<b>Format:</b> '.$result->getFormat().'<br/>';
echo '<b>Initialized on:</b> '.$result->getInitializedOn().'<br/>';
echo '<b>Updated since init:</b> '.$result->getUpdatedSinceInit().'<br/><br/>';

// display synonyms
echo '<b>Number of synonym mappings:</b> '.count($result).'<br/>';
echo '<b>Synonym mappings:</b><br/>';
echo '<table>';

foreach ($result as $synonym) {
    echo '<tr><th>'.$synonym->getTerm().'</th><td>'.implode(', ', $synonym->getSynonyms()).'</td></tr>';
}

echo '</table>';

echo '<h2>Check list existence</h2>';

// create a managed synonyms query
$query = $client->createManagedSynonyms();

// create an "exists" command and set it on the query
$existsCommand = $query->createCommand($query::COMMAND_EXISTS);
$query->setCommand($existsCommand);

foreach (['english', 'dutch'] as $name) {
    // set the name of the synonym list
    $query->setName($name);

    // execute the query and return the result
    /** @var Solarium\QueryType\ManagedResources\Result\Command $result */
    $result = $client->execute($query);

    // display the result
    echo '<b>'.$name.':</b> '.($result->getWasSuccessful() ? 'exists' : 'doesn\'t exist').'<br/>';
}

echo '<h2>Create list</h2>';

// create a managed synonyms query
$query = $client->createManagedSynonyms();

// set the name of the synonym list
$query->setName('dutch');

// create a "create" command and set it on the query
/** @var Solarium\QueryType\ManagedResources\Query\Command\Synonyms\Create $createCommand */
$createCommand = $query->createCommand($query::COMMAND_CREATE);
$query->setCommand($createCommand);

// execute the query and return the result
/** @var Solarium\QueryType\ManagedResources\Result\Command $result */
$result = $client->execute($query);

// display the result
if ($result->getWasSuccessful()) {
    echo 'Synonym list was created.';
}

echo '<h2>Configure list</h2>';

// create a managed synonyms query
$query = $client->createManagedSynonyms();

// set the name of the synonym list
$query->setName('dutch');

// create initialization arguments and set case sensitivity and format
$initArgs = $query->createInitArgs();
$initArgs->setIgnoreCase(false);
$initArgs->setFormat($initArgs::FORMAT_SOLR);

// create a "config" command, set init args on the command and set the command on the query
/** @var Solarium\QueryType\ManagedResources\Query\Command\Config $configCommand */
$configCommand = $query->createCommand($query::COMMAND_CONFIG);
$configCommand->setInitArgs($initArgs);
$query->setCommand($configCommand);

// execute the query and return the result
/** @var Solarium\QueryType\ManagedResources\Result\Command $result */
$result = $client->execute($query);

// display the result
if ($result->getWasSuccessful()) {
    echo 'Synonym list configuration updated.';
}

echo '<h2>Remove list</h2>';

// create a managed synonyms query
$query = $client->createManagedSynonyms();

// set the name of the synonym list
$query->setName('dutch');

// create a "remove" command and set it on the query
/** @var Solarium\QueryType\ManagedResources\Query\Command\Remove $removeCommand */
$removeCommand = $query->createCommand($query::COMMAND_REMOVE);
$query->setCommand($removeCommand);

// execute the query and return the result
/** @var Solarium\QueryType\ManagedResources\Result\Command $result */
$result = $client->execute($query);

// display the result
if ($result->getWasSuccessful()) {
    echo 'Synonym list was removed.';
}

echo '<hr/><h1>Commands that operate on synonym mappings in a list</h1>';

echo '<h2>Get synonym mapping</h2>';

// create a managed synonyms query
$query = $client->createManagedSynonyms();

// set the name of the synonym list
$query->setName('english');

// set the term, case doesn't matter on this list
$query->setTerm('gb');

// execute the query and return the result
/** @var Solarium\QueryType\ManagedResources\Result\Synonyms\SynonymMappings $result */
$result = $client->execute($query);

// display synonym, there will be only one
foreach ($result as $synonym) {
    echo '<b>'.$synonym->getTerm().'</b>: '.implode(', ', $synonym->getSynonyms()).'<br/>';
}

echo '<h2>Check synonym mapping existence</h2>';

// create a managed synonyms query
$query = $client->createManagedSynonyms();

// set the name of the synonym list
$query->setName('english');

// create an "exists" command
/** @var Solarium\QueryType\ManagedResources\Query\Command\Exists $existsCommand */
$existsCommand = $query->createCommand($query::COMMAND_EXISTS);

foreach (['tv', 'radio'] as $term) {
    // set the term on the command, and set the command on the query
    $existsCommand->setTerm($term);
    $query->setCommand($existsCommand);

    // execute the query and return the result
    /** @var Solarium\QueryType\ManagedResources\Result\Command $result */
    $result = $client->execute($query);

    // display the result
    echo '<b>'.$term.':</b> '.($result->getWasSuccessful() ? 'exists' : 'doesn\'t exist').'<br/>';
}

echo '<h2>Add single synonym mapping</h2>';

// create a managed synonyms query
$query = $client->createManagedSynonyms();

// set the name of the synonym list
$query->setName('english');

// create a synonym mapping
$synonyms = new Synonyms();
$synonyms->setTerm('mad');
$synonyms->setSynonyms(['angry', 'upset']);

// create an "add" command, set synonym mapping on the command, and set the command on the query
/** @var Solarium\QueryType\ManagedResources\Query\Command\Synonyms\Add $addCommand */
$addCommand = $query->createCommand($query::COMMAND_ADD);
$addCommand->setSynonyms($synonyms);
$query->setCommand($addCommand);

// execute the query and return the result
/** @var Solarium\QueryType\ManagedResources\Result\Command $result */
$result = $client->execute($query);

// display the result
if ($result->getWasSuccessful()) {
    echo 'Synonym mapping was added.';
}

echo '<h2>Delete single synonym mapping</h2>';

// create a managed synonyms query
$query = $client->createManagedSynonyms();

// set the name of the synonym list
$query->setName('english');

// create a "delete" command, set the term on the command, and set the command on the query
/** @var Solarium\QueryType\ManagedResources\Query\Command\Delete $deleteCommand */
$deleteCommand = $query->createCommand($query::COMMAND_DELETE);
$deleteCommand->setTerm('mad');
$query->setCommand($deleteCommand);

// execute the query and return the result
/** @var Solarium\QueryType\ManagedResources\Result\Command $result */
$result = $client->execute($query);

// display the result
if ($result->getWasSuccessful()) {
    echo 'Synonym mapping was deleted.<br/>';
}

echo '<h2>Add symmetric synonyms</h2>';

// create a managed synonyms query
$query = $client->createManagedSynonyms();

// set the name of the synonym list
$query->setName('english');

// create a synonym mapping but don't set an explicit term, synonyms will be expanded into a mapping for each listed term
$synonyms = new Synonyms();
$synonyms->setSynonyms(['funny', 'entertaining', 'whimiscal', 'jocular']);

// create an "add" command, set synonym mapping on the command, and set the command on the query
/** @var Solarium\QueryType\ManagedResources\Query\Command\Synonyms\Add $addCommand */
$addCommand = $query->createCommand($query::COMMAND_ADD);
$addCommand->setSynonyms($synonyms);
$query->setCommand($addCommand);

// execute the query and return the result
/** @var Solarium\QueryType\ManagedResources\Result\Command $result */
$result = $client->execute($query);

// display the result
if ($result->getWasSuccessful()) {
    echo 'Synonym mappings were added.';
}

echo '<h2>Delete expanded synonym mappings</h2>';

// create a managed synonyms query
$query = $client->createManagedSynonyms();

// set the name of the synonym list
$query->setName('english');

// create a "delete" command
/** @var Solarium\QueryType\ManagedResources\Query\Command\Delete $deleteCommand */
$deleteCommand = $query->createCommand($query::COMMAND_DELETE);

// unlike adding, deleting has to be done term per term
foreach (['funny', 'entertaining', 'whimiscal', 'jocular'] as $term) {
    // set the term on the command, and set the command on the query
    $deleteCommand->setTerm($term);
    $query->setCommand($deleteCommand);

    // execute the query and return the result
    /** @var Solarium\QueryType\ManagedResources\Result\Command $result */
    $result = $client->execute($query);

    // display the result
    if ($result->getWasSuccessful()) {
        echo 'Synonym mapping was deleted.<br/>';
    }
}

echo '<hr/><h1>Apply changes</h1>';

// create a core admin query
$query = $client->createCoreAdmin();

// create a "reload" action
$reloadAction = $query->createReload();

// set the core on the action, and set the action on the query
$reloadAction->setCore($client->getEndpoint()->getCore());
$query->setAction($reloadAction);

// execute the query and return the result
$result = $client->coreAdmin($query);

// display the result
if ($result->getWasSuccessful()) {
    echo 'Core was reloaded.<br/>';
}

htmlFooter();

```

A note on percent-encoding reserved characters
----------------------------------------------

If the name of a synonym map or a synonym itself contains characters that are not
[unreserved characters as defined by RFC 3986](https://www.rfc-editor.org/rfc/rfc3986#section-2.3),
they must be percent-encoded when appearing as part of a URL. Solarium handles this for you.

However, if you're using a Solr version prior to Solr 10 and map names or synonyms
that contain [reserved characters](https://datatracker.ietf.org/doc/html/rfc3986#section-2.2),
you will be affected by [SOLR-6853](https://issues.apache.org/jira/browse/SOLR-6853). You can
instruct Solarium to double up on the percent-encoding as a workaround.

```php
$query = $client->createManagedSynonyms(['useDoubleEncoding' => true]);
```

Keep in mind that Solr may not be able to handle some of these reserved characters regardless.

A note on `HEAD` requests
-------------------------

The "exists" command executes `GET` requests by default because multiple Solr versions
have bugs in the handling of `HEAD` requests. You can choose to execute `HEAD` requests
instead if you know that your Solr version isn't affected by
[SOLR-15116](https://issues.apache.org/jira/browse/SOLR-15116) or
[SOLR-16274](https://issues.apache.org/jira/browse/SOLR-16274).

```php
$existsCommand = $query->createCommand($query::COMMAND_EXISTS, ['useHeadRequest' => true]);
```
