<?php
require(__DIR__.'/init.php');

htmlHeader();

// create a client instance and autoload the customize request plugin
$client = new Solarium\Client($config);
$customizer = $client->getPlugin('customizerequest');

// add a persistent HTTP header (using array input values)
$customizer->createCustomization(
    array(
        'key' => 'auth',
        'type' => 'header',
        'name' => 'X-my-auth',
        'value' => 'mypassword',
        'persistent' => true
    )
);

// add a persistent GET param (using fluent interface)
$customizer->createCustomization('session')
           ->setType('param')
           ->setName('ssid')
           ->setValue('md7Nhd86adye6sad46d')
           ->setPersistent(true);

// add a GET param thats only used for a single request (the default setting is no persistence)
$customizer->createCustomization('id')
           ->setType('param')
           ->setName('id')
           ->setValue(4576);

// create a basic query to execute
$query = $client->createSelect();

// execute query (you should be able to see the extra params in the solr log file)
$resultset = $client->select($query);

// display the total number of documents found by solr
echo 'NumFound: '.$resultset->getNumFound() . '<br/>';

// execute the same query again (this time the 'id' param should no longer show up in the logs)
$resultset = $client->select($query);

// display the total number of documents found by solr
echo 'NumFound: '.$resultset->getNumFound();

htmlFooter();
