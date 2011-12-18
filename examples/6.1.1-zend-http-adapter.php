<?php

require_once 'Zend/Loader/Autoloader.php';
$loader = Zend_Loader_Autoloader::getInstance();

require('init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// set the adapter to zendhttp and get a zendhttp client instance reference
$client->setAdapter('Solarium\Client\Adapter\ZendHttp');
$zendHttp = $client->getAdapter()->getZendHttp();

// you can use any of the zend_http features, like http-authentication
$zendHttp->setAuth('user', 'password!', Zend_Http_Client::AUTH_BASIC);

// get a select query instance
$query = $client->createSelect();

// this executes the query and returns the result
$resultset = $client->select($query);

// display the total number of documents found by solr
echo 'NumFound: '.$resultset->getNumFound();

// show documents using the resultset iterator
foreach ($resultset as $document) {

    echo '<hr/><table>';

    // the documents are also iterable, to get all fields
    foreach($document AS $field => $value)
    {
        // this converts multivalue fields to a comma-separated string
        if(is_array($value)) $value = implode(', ', $value);
        
        echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
    }

    echo '</table>';
}

htmlFooter();