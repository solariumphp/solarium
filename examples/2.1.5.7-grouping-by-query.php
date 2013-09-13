<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get a select query instance
$query = $client->createSelect();

// get grouping component and create two query groups
$groupComponent = $query->getGrouping();
$groupComponent->addQuery('price:[0 TO 99.99]');
$groupComponent->addQuery('price:[100 TO *]');
// sorting inside groups
$groupComponent->setSort('price desc');
// maximum number of items per group
$groupComponent->setLimit(5);

// this executes the query and returns the result
$resultset = $client->select($query);

$groups = $resultset->getGrouping();
foreach ($groups as $groupKey => $group) {

    echo '<h1>'.$groupKey.'</h1>';

    foreach ($group as $document) {
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
}

htmlFooter();
