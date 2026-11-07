<?php

require_once __DIR__.'/init.php';

htmlHeader();

echo '<h2>Note: This example assumes a JSON response format from Solr 9 or lower</h2>';
echo '<p>Refer to <a href="2.12.1-metrics-query.php">Metrics query with Prometheus response</a> for newer Solr versions.</p>';

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a metrics query instance
$query = $client->createMetrics();

// JSON response writer has to be set explicitly on a Metrics query
$query->setResponseWriter($query::WT_JSON);

// this executes the query and returns the result
$result = $client->metrics($query);

// result data for a JSON response is only accessible as an array
foreach ($result->getData()['metrics'] as $registry => $metrics) {
    echo '<h3>'.$registry.'</h3>';

    foreach ($metrics as $name => $properties) {
        echo '<h3>'.$name.'</h3>';

        if (is_scalar($properties)) {
            echo $properties.'<br/>';
        } else {
            echo '<table>';

            foreach ($properties as $property => $value) {
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }

                echo '<tr><th>'.$property.'</th><td>'.$value.'</td></tr>';
            }

            echo '<table>';
        }
    }

    echo '<hr/>';
}

htmlFooter();
