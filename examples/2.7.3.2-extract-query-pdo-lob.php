<?php

require_once(__DIR__.'/init.php');
htmlHeader();

echo "<h2>Note: This example doesn't work in PHP &lt; 8.1.0!</h2>";
echo "<h2>Note: This example requires the PDO_SQLITE PDO driver (enabled by default in PHP)</h2>";

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get an extract query instance and add settings
$query = $client->createExtract();
$query->addFieldMapping('content', 'text');
$query->setUprefix('attr_');
$query->setCommit(true);
$query->setOmitHeader(false);

// create a database & store content as an example
$db = new PDO('sqlite::memory:');
$db->exec("CREATE TABLE test (id INT, content TEXT)");
$insert = $db->prepare("INSERT INTO test (id, content) VALUES (:id, :content)");
$insert->execute(['id' => 1, 'content' => file_get_contents(__DIR__.'/index.html')]);

// get content from the database and map it as a stream
$select = $db->prepare("SELECT content FROM test WHERE id = :id");
$select->execute(['id' => 1]);
$select->bindColumn(1, $content, PDO::PARAM_LOB);
$select->fetch(PDO::FETCH_BOUND);

// add content as a stream resource
$query->setFile($content);

// add document
$doc = $query->createDocument();
$doc->id = 'extract-test';
$doc->some = 'more fields';
$query->setDocument($doc);

// this executes the query and returns the result
$result = $client->extract($query);

echo '<b>Extract query executed</b><br/>';
echo 'Query status: ' . $result->getStatus(). '<br/>';
echo 'Query time: ' . $result->getQueryTime();

htmlFooter();
