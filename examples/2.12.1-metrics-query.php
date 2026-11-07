<?php

use Composer\InstalledVersions;

require_once __DIR__.'/init.php';

htmlHeader();

echo '<h2>Note: This example assumes a Prometheus response format from Solr 9 or higher</h2>';
echo '<p>Refer to <a href="2.12.2-metrics-query-json.php">Metrics query with JSON response</a> for older Solr versions.</p>';

if (!InstalledVersions::isInstalled('butschster/prometheus-parser')) {
    echo '<h2>Note: Parsing the Prometheus response format requires butschster/prometheus-parser</h2>';
}

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a metrics query instance
$query = $client->createMetrics();

// you can filter specific metrics
$query->setCategory('UPDATE');

// this executes the query and returns the result
$result = $client->metrics($query);

// without butschster/prometheus-parser installed, you can only get the raw output
echo '<h2>Raw output</h2>';
echo '<textarea rows="20" cols="200">'.htmlspecialchars($result->getData()['metrics']).'</textarea>';

// with butschster/prometheus-parser installed, you can get a result object
echo '<h2>Structured output</h2>';
echo '<p>Check the documentation at <a href="https://github.com/butschster/prometheus-parser">';
echo 'https://github.com/butschster/prometheus-parser</a> for more information on the structure ';
echo 'of the object returned by <code>$result->getMetricSet()</code>.</p>';

foreach ($result->getMetricSet()->getMetrics() as $metricFamily) {
    echo '<h3>'.$metricFamily->name.'</h3>';
    echo '<b>Description: '.$metricFamily->description.'</b><br/>';
    echo '<b>Type: '.$metricFamily->type.'</b><br/>';

    if (null !== $metricFamily->unit) {
        echo '<b>Unit: '.$metricFamily->unit.'</b><br/>';
    }

    foreach ($metricFamily->metrics as $metric) {
        echo '<hr/>';
        echo 'Labels: ';
        foreach ($metric->labels as $label) {
            echo '['.$label->name.'="'.$label->value.'"] ';
        }
        echo '<br/>';
        echo 'Value: '.$metric->value.'<br/>';
    }

    echo '<hr/>';
}

htmlFooter();
