<?php

$config = array(
    'endpoint' => array(
        'localhost' => array(
            'scheme' => 'http', # or https
            'host' => '127.0.0.1',
            'port' => 8983,
            'path' => '/',
            // 'context' => 'solr', # only necessary to set if not the default 'solr'
            'core' => 'techproducts',
        )
    )
);
