<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get an update query instance
$update = $client->createUpdate();

// create a document and set nested child documents
$doc1 = $update->createDocument();
$doc1->id = 123;
$doc1->name = 'testdoc-1';
$doc1->childdocs = array(
    array(
        'id' => 1230,
        'name' => 'childdoc-1-1',
        'price' => 465,
    ),
    array(
        'id' => 1231,
        'name' => 'childdoc-1-2',
        'price' => 545,
    ),
);

// and a second one where child documents are added one by one
$doc2 = $update->createDocument();
$doc2->setField('id', 124);
$doc2->setField('name', 'testdoc-2');
$doc2->addField('childdocs', array(
    'id' => 1240,
    'name' => 'childdoc-2-1',
    'price' => 360,
));
$doc2->addField('childdocs', array(
    'id' => 1241,
    'name' => 'childdoc-2-2',
    'price' => 398,
));

// add the documents and a commit command to the update query
$update->addDocuments(array($doc1, $doc2));
$update->addCommit();

// this executes the query and returns the result
$result = $client->update($update);

echo '<b>Update query executed</b><br/>';
echo 'Query status: ' . $result->getStatus(). '<br/>';
echo 'Query time: ' . $result->getQueryTime();

htmlFooter();
