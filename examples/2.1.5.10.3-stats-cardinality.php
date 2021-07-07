<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect();
$query->setRows(0);

// cardinality isn't computed by default and has to be specified explicitly via local parameters
// this overrides the default set, explicitly add any other statistics you still want computed
$stats = $query->getStats();
$stats->createField('{!min=true max=true countDistinct=true cardinality=0.1}popularity');
$stats->createField('{!min=true max=true countDistinct=true cardinality=0.1}price');

// this executes the query and returns the result
$resultset = $client->select($query);
$statsResult = $resultset->getStats();

// display the stats results
foreach ($statsResult as $field) {
    echo '<h1>' . $field->getName() . '</h1>';
    echo 'Min: ' . $field->getMin() . '<br/>';
    echo 'Max: ' . $field->getMax() . '<br/>';
    echo 'Exact number of distinct values: ' . $field->getCountDistinct() . '<br/>';
    echo 'Approximate number of distinct values: ' . $field->getCardinality() . '<br/>';
    echo '<hr/>';
}

htmlFooter();
