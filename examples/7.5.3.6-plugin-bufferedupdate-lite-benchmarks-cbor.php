<?php

use Solarium\QueryType\Update\Query\Query;

require_once __DIR__.'/init.php';

$weight = 'lite';
// CBOR can only be used to add documents (SOLR-17510)
$addRequestFormat = Query::REQUEST_FORMAT_CBOR;
$delRequestFormat = Query::REQUEST_FORMAT_JSON;

require __DIR__.'/7.5.3-plugin-bufferedupdate-benchmarks.php';
