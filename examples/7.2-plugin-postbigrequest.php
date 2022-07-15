<?php

require_once(__DIR__.'/init.php');

htmlHeader();

// create a client instance and autoload the postbigrequest plugin
$client = new Solarium\Client($adapter, $eventDispatcher, $config);
$client->getPlugin('postbigrequest');

// create a basic query to execute
$query = $client->createSelect();

// add a huge filterquery to create a very long query string
$fq = 'price:0 OR cat:'.str_repeat(implode('', range('a', 'z')), 1000);
$query->createFilterQuery('fq')->setQuery($fq);

// without the plugin this query would fail as it is bigger than the default servlet container header buffer
$resultset = $client->select($query);

// display the total number of documents found by Solr
echo 'NumFound: '.$resultset->getNumFound();

// show documents using the resultset iterator
foreach ($resultset as $document) {

    echo '<hr/><table>';

    // the documents are also iterable, to get all fields
    foreach ($document as $field => $value) {
        // this converts multivalue fields to a comma-separated string
        if (is_array($value)) {
            $value = implode(', ', $value);
        }

        echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
    }

    echo '</table>';
}

htmlFooter();
