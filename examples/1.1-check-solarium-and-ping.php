<?php

require('init.php');
htmlHeader();

// check solarium version available
echo 'Solarium library version: ' . Solarium_Version::VERSION . ' - ';

// create a client instance
$client = new Solarium_Client($config);

// create a ping query
$ping = $client->createPing();

// execute the ping query
try{
    $result = $client->ping($ping);
    echo 'Ping query successful';
    echo '<br/><pre>';
    var_dump($result->getData());
}catch(Solarium_Exception $e){
    echo 'Ping query failed';
}

htmlFooter();