<?php

require_once __DIR__.'/init.php';

htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect();
$query->setRows(50);

// get grouping component and set a function to group by
$groupComponent = $query->getGrouping();
$groupComponent->setFunction('exists(features)');
// maximum number of items per group
$groupComponent->setLimit(3);

// this executes the query and returns the result
$resultset = $client->select($query);

$groups = $resultset->getGrouping();
foreach ($groups as $groupKey => $functionGroup) {
    echo '<h1>'.$groupKey.'</h1>';
    echo 'Matches: '.$functionGroup->getMatches().'<br/>';

    foreach ($functionGroup as $valueGroup) {
        $value = $valueGroup->getValue();
        echo '<h2>'.($value ? 'true' : 'false').'</h2>';

        foreach ($valueGroup as $document) {
            echo '<hr/><table>';

            // the documents are also iterable, to get all fields
            foreach ($document as $field => $value) {
                // this converts multivalue fields to a comma-separated string
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }

                echo '<tr><th>'.$field.'</th><td>'.$value.'</td></tr>';
            }

            echo '</table>';
        }
    }
}

htmlFooter();
