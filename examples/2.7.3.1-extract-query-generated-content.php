<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get an extract query instance and add settings
$query = $client->createExtract();
$query->addFieldMapping('content', 'text');
$query->setUprefix('attr_');
$query->setCommit(true);
$query->setOmitHeader(false);

// open a file pointer resource and add it to the query
$file = tmpfile();
$query->setFile($file);

// write generated content to the file pointer
ob_start();
phpcredits();
fwrite($file, ob_get_contents());
ob_end_clean();

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

// don't forget to close your file pointer!
fclose($file);

htmlFooter();
