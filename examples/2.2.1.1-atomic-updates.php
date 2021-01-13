<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get an update query instance
$update = $client->createUpdate();

// create a new document
$doc1 = $update->createDocument();
$doc1->id = 123;
$doc1->name = 'testdoc';
$doc1->price = 364;

// add the document and a commit command to the update query
$update->addDocument($doc1);

// now we can set a field to another value without reindexing the entire document
$doc2 = $update->createDocument();
$doc2->setKey('id', 123);
$doc2->setField('name', 'Test document');
$doc2->setFieldModifier('name', $doc2::MODIFIER_SET);

// or increment a numeric value by a specific amount
$doc3 = $update->createDocument();
$doc3->setKey('id', 123);
$doc3->setField('price', 10);
$doc3->setFieldModifier('price', $doc3::MODIFIER_INC);

// add the atomic updates and a commit command to the update query
$update->addDocuments([$doc2, $doc3]);
$update->addCommit();

// this executes the query and returns the result
$result = $client->update($update);

echo '<b>Update query executed</b><br/>';
echo 'Query status: ' . $result->getStatus(). '<br/>';
echo 'Query time: ' . $result->getQueryTime();

// get a select query instance
$query = $client->createSelect();

// create a filterquery
$query->createFilterQuery('newprice')->setQuery('price:374');

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by Solr
echo '<hr/>NumFound: '.$resultset->getNumFound();

// show documents using the resultset iterator
foreach ($resultset as $document) {

    echo '<hr/><table>';
    echo '<tr><th>id</th><td>' . $document->id . '</td></tr>';
    echo '<tr><th>name</th><td>' . $document->name . '</td></tr>';
    echo '<tr><th>price</th><td>' . $document->price . '</td></tr>';
    echo '</table>';
}

htmlFooter();
