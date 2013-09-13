<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get an update query instance
$update = $client->createUpdate();

// create a new document
$id = time();
$doc1 = $update->createDocument();
$doc1->id = $id;
$doc1->name = 'realtime-get-test-'.date('Y-m-d H:i:s');

// set a very long commitWithin time and add it to solr
$update->addDocument($doc1, null, 1000000);
$client->update($update);

// try to get the document using a normal select, this should return 0 results
$query = $client->createSelect();
$query->setQuery('id:%1%', array($id));
$resultset = $client->select($query);
echo 'NumFound with standard select: '.$resultset->getNumFound().'<br/>';

// now with realtime get, this should return 1 result
$query = $client->createRealtimeGet();
$query->addId($id);
$result = $client->realtimeGet($query);
echo 'NumFound with realtime get: '.$result->getNumFound().'<br/>';

// Display the document
echo '<hr/><table>';
foreach ($result->getDocument() as $field => $value) {
    echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
}
echo '</table>';


htmlFooter();
