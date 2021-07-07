<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect();
$query->setRows(0);

// distinct values aren't computed by default and have to be specified explicitly via local parameters
// this overrides the default set, explicitly add any other statistics you still want computed
$stats = $query->getStats();
$stats->createField('{!min=true max=true distinctValues=true countDistinct=true}popularity');
$stats->createField('{!min=true max=true distinctValues=true countDistinct=true}price');

// this executes the query and returns the result
$resultset = $client->select($query);
$statsResult = $resultset->getStats();

// display the stats results
foreach ($statsResult as $field) {
    echo '<h1>' . $field->getName() . '</h1>';
    echo 'Min: ' . $field->getMin() . '<br/>';
    echo 'Max: ' . $field->getMax() . '<br/>';
    echo 'Number of distinct values: ' . $field->getCountDistinct() . '<br/>';
    echo 'Distinct values: <br/>';
    echo '<ul>';
    foreach ($field->getDistinctValues() as $value) {
        echo '<li>' . $value . '</li>';
    }
    echo '</ul>';
    echo '<hr/>';
}

htmlFooter();
