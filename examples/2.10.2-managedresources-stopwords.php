<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

echo '<h1>Commands that operate on a stopword list</h1>';

echo '<h2>Get list</h2>';

// create a managed stopwords query
$query = $client->createManagedStopwords();

// set the name of the stopword list
$query->setName('english');

// execute the query and return the result
$result = $client->execute($query);

// display list properties
echo '<b>Case sensitive:</b> ' . ($result->isIgnoreCase() ? 'no' : 'yes') . '<br/>';
echo '<b>Initialized on:</b> ' . $result->getInitializedOn() . '<br/>';
echo '<b>Updated since init:</b> ' . $result->getUpdatedSinceInit() . '<br/><br/>';

// display stopwords
echo '<b>Number of stopwords:</b> ' . count($result) . '<br/>';
echo '<b>Stopwords:</b><br/>';
echo implode(', ', $result->getItems());
echo '<br/>';

echo '<h2>Check list existence</h2>';

// create a managed stopwords query
$query = $client->createManagedStopwords();

// create an "exists" command and set it on the query
$existsCommand = $query->createCommand($query::COMMAND_EXISTS);
$query->setCommand($existsCommand);

foreach (['english', 'dutch'] as $name) {
    // set the name of the stopword list
    $query->setName($name);

    // execute the query and return the result
    $result = $client->execute($query);

    // display the result
    echo '<b>' . $name . ':</b> ' . ($result->getWasSuccessful() ? 'exists' : 'doesn\'t exist') . '<br/>';
}

echo '<h2>Create list</h2>';

// create a managed stopwords query
$query = $client->createManagedStopwords();

// set the name of the stopword list
$query->setName('dutch');

// create a "create" command and set it on the query
$createCommand = $query->createCommand($query::COMMAND_CREATE);
$query->setCommand($createCommand);

// execute the query and return the result
$result = $client->execute($query);

// display the result
if ($result->getWasSuccessful()) {
    echo 'Stopword list was created.';
}

echo '<h2>Configure list</h2>';

// create a managed stopwords query
$query = $client->createManagedStopwords();

// set the name of the stopword list
$query->setName('dutch');

// create initialization arguments and set case sensitivity
$initArgs = $query->createInitArgs();
$initArgs->setIgnoreCase(false);

// create a "config" command, set init args on the command and set the command on the query
$configCommand = $query->createCommand($query::COMMAND_CONFIG);
$configCommand->setInitArgs($initArgs);
$query->setCommand($configCommand);

// execute the query and return the result
$result = $client->execute($query);

// display the result
if ($result->getWasSuccessful()) {
    echo 'Stopword list configuration updated.';
}

echo '<h2>Remove list</h2>';

// create a managed stopwords query
$query = $client->createManagedStopwords();

// set the name of the stopword list
$query->setName('dutch');

// create a "remove" command and set it on the query
$removeCommand = $query->createCommand($query::COMMAND_REMOVE);
$query->setCommand($removeCommand);

// execute the query and return the result
$result = $client->execute($query);

// display the result
if ($result->getWasSuccessful()) {
    echo 'Stopword list was removed.';
}

echo '<hr/><h1>Commands that operate on stopwords in a list</h1>';

echo '<h2>Get stopword</h2>';

// create a managed stopwords query
$query = $client->createManagedStopwords();

// set the name of the stopword list
$query->setName('english');

// set the term, case doesn't matter on this list
$query->setTerm('StopwordA');

// execute the query and return the result
$result = $client->execute($query);

// display stopword, there will be only one
foreach ($result as $stopword) {
    echo $stopword;
}

echo '<h2>Check stopword existence</h2>';

// create a managed stopwords query
$query = $client->createManagedStopwords();

// set the name of the stopword list
$query->setName('english');

// create an "exists" command
$existsCommand = $query->createCommand($query::COMMAND_EXISTS);

foreach (['stopwordb', 'stopwordc'] as $term) {
    // set the term on the command, and set the command on the query
    $existsCommand->setTerm($term);
    $query->setCommand($existsCommand);

    // execute the query and return the result
    $result = $client->execute($query);

    // display the result
    echo '<b>' . $term . ':</b> ' . ($result->getWasSuccessful() ? 'exists' : 'doesn\'t exist') . '<br/>';
}

echo '<h2>Add stopwords</h2>';

// create a managed stopwords query
$query = $client->createManagedStopwords();

// set the name of the stopword list
$query->setName('english');

// create an "add" command, set stopwords on the command, and set the command on the query
$addCommand = $query->createCommand($query::COMMAND_ADD);
$addCommand->setStopwords(['foo', 'bar']);
$query->setCommand($addCommand);

// execute the query and return the result
$result = $client->execute($query);

// display the result
if ($result->getWasSuccessful()) {
    echo 'Stopwords were added.';
}

echo '<h2>Delete stopwords</h2>';

// create a managed stopwords query
$query = $client->createManagedStopwords();

// set the name of the stopword list
$query->setName('english');

// create a "delete" command
$deleteCommand = $query->createCommand($query::COMMAND_DELETE);

// unlike adding, deleting has to be done term per term
foreach (['foo', 'bar'] as $term) {
    // set the term on the command, and set the command on the query
    $deleteCommand->setTerm($term);
    $query->setCommand($deleteCommand);

    // execute the query and return the result
    $result = $client->execute($query);

    // display the result
    if ($result->getWasSuccessful()) {
        echo 'Stopword was deleted.<br/>';
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
