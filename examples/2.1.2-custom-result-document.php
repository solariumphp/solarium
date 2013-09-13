<?php

require(__DIR__.'/init.php');
htmlHeader();


// this is the custom result document class
class MyDoc extends Solarium\QueryType\Select\Result\Document
{
    public function getSpecialPrice()
    {
        return round(($this->price * .95), 2);
    }
}


// create a client instance
$client = new Solarium\Client($config);

// get a select query instance
$query = $client->createSelect();

// set the custom resultclass
$query->setDocumentClass('MyDoc');

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by solr
echo 'NumFound: '.$resultset->getNumFound();

// show documents using the resultset iterator
foreach ($resultset as $document) {

    echo '<hr/><table>';
    echo '<tr><th>id</th><td>' . $document->id . '</td></tr>';
    echo '<tr><th>name</th><td>' . $document->name . '</td></tr>';
    echo '<tr><th>price</th><td>' . $document->price . '</td></tr>';

    // this method is added by the custom class
    echo '<tr><th>offer price</th><td>' . $document->getSpecialPrice() . '</td></tr>';

    echo '</table>';
}

htmlFooter();
