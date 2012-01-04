<?php

require('init.php');
htmlHeader();

// create a client instance
$client = new Solarium_Client($config);

// get a terms query instance
$query = $client->createTerms();
$query->setFields('features,name');
$query->setLowerbound('i');

// this executes the query and returns the result
$resultset = $client->terms($query);

// display terms
foreach ($resultset as $field => $terms) {
    echo '<h3>' . $field . '</h3>';
    foreach ($terms as $term => $count) {
       echo $term . ' (' . $count . ')<br/>';
    }
    echo '<hr/>';
}

htmlFooter();