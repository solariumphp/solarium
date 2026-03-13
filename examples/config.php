<?php

$config = [
    'endpoint' => [
        'localhost' => [
            'scheme' => 'http', // or https
            'host' => '127.0.0.1',
            'port' => 8983,
            'path' => '/',
            // 'context' => 'solr', // only necessary to set if not the default 'solr'
            'core' => 'techproducts',
        ],
    ],
];
