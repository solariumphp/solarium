<?php

require_once(__DIR__.'/init.php');

use Solarium\QueryType\Update\Query\Query;

$weight = 'lite';
$requestFormat = Query::REQUEST_FORMAT_XML;

require(__DIR__.'/7.5.3-plugin-bufferedupdate-benchmarks.php');
