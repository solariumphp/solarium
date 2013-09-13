<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance and get a select query instance
$client = new Solarium\Client($config);




// first an example manually defining dereferenced params
$query = $client->createSelect();
$helper = $query->getHelper();

$join = $helper->qparser('join', array('from' => 'manu_id', 'to' => 'id'), true);
$queryString = $join . 'id:1';
$query->setQuery($queryString);
$request = $client->createRequest($query);

// output resulting url with dereferenced params
echo urldecode($request->getUri()) . '<hr/>';




// this second example gives the exact same result, using the special join helper
$query = $client->createSelect();
$helper = $query->getHelper();

$join = $helper->join('manu_id', 'id', true);
$queryString = $join . 'id:1';
$query->setQuery($queryString);
$request = $client->createRequest($query);

echo urldecode($request->getUri());

htmlFooter();
