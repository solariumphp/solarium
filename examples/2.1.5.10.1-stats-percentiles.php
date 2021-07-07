<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect();
$query->setRows(0);

// percentiles aren't computed by default and have to be specified explicitly via local parameters
// this overrides the default set, explicitly add any other statistics you still want computed
$stats = $query->getStats();
$stats->createField('{!min=true max=true percentiles="1,10,90,99"}popularity');
$stats->createField('{!min=true max=true percentiles="99,99.9,99.99"}price');

// this executes the query and returns the result
$resultset = $client->select($query);
$statsResult = $resultset->getStats();

// display the stats results
foreach ($statsResult as $field) {
    echo '<h1>' . $field->getName() . '</h1>';
    echo 'Min: ' . $field->getMin() . '<br/>';
    echo 'Max: ' . $field->getMax() . '<br/>';
    echo 'Percentiles: <br/>';
    echo '<table>';
    foreach ($field->getPercentiles() as $percentile => $value) {
        echo '<tr><th>' . $percentile . '</th><td>' . $value . '</td></tr>';
    }
    echo '</table>';
    echo '<hr/>';
}

htmlFooter();
