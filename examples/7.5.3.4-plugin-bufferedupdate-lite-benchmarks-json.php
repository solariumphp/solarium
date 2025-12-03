<?php

require_once(__DIR__.'/init.php');

use Solarium\QueryType\Update\Query\Query;

$weight = 'lite';
$addRequestFormat = Query::REQUEST_FORMAT_JSON;
$delRequestFormat = Query::REQUEST_FORMAT_JSON;

require(__DIR__.'/7.5.3-plugin-bufferedupdate-benchmarks.php');
