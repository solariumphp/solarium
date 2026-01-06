<?php

use Solarium\QueryType\Update\Query\Query;

require_once __DIR__.'/init.php';

$weight = '';
$addRequestFormat = Query::REQUEST_FORMAT_JSON;
$delRequestFormat = Query::REQUEST_FORMAT_JSON;

require __DIR__.'/7.5.3-plugin-bufferedupdate-benchmarks.php';
