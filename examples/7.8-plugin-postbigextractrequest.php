<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);
$plugin_PostBigExtractRequest = $client->getPlugin('postbigextractrequest');
$plugin_PostBigExtractRequest->setCharset( "UTF-8" );  //deafult value
$plugin_PostBigExtractRequest->setMaxQueryStringLength(1024); //deafult value

                        

// get an extract query instance and add settings
$query = $client->createExtract();
$query->addFieldMapping('content', 'text');
$query->setUprefix('attr_');
$query->setFile(__DIR__.'/index.html');
$query->setCommit(true);
$query->setOmitHeader(false);

// add document
$doc = $query->createDocument();
$doc->id = 'extract-test';
$doc->some = 'more fields';
// create a very long list of literals
for ($i = 1; $i <= 500; ++$i) {
    $field_name = "field_{$i}";
    $doc->$field_name = "value $i";
}
$query->setDocument($doc);

// this executes the query and returns the result
$result = $client->extract($query);

echo '<b>Extract query executed</b><br/>';
echo 'Query status: ' . $result->getStatus(). '<br/>';
echo 'Query time: ' . $result->getQueryTime();

htmlFooter();
