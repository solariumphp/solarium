<?php

require('init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get a select query instance
$query = $client->createSelect();
$query->setRows(50);

// get grouping component and set a field to group by
$groupComponent = $query->getGrouping();
$groupComponent->addField('inStock');
// maximum number of items per group
$groupComponent->setLimit(3);
// get a group count
$groupComponent->setNumberOfGroups(true);

// this executes the query and returns the result
$resultset = $client->select($query);

$groups = $resultset->getGrouping();
foreach($groups AS $groupKey => $fieldGroup) {

    echo '<h1>'.$groupKey.'</h1>';
    echo 'Matches: '.$fieldGroup->getMatches().'<br/>';
    echo 'Number of groups: '.$fieldGroup->getNumberOfGroups();

    foreach($fieldGroup AS $valueGroup) {

        echo '<h2>'.(int)$valueGroup->getValue().'</h2>';

        foreach($valueGroup AS $document) {
            
            echo '<hr/><table>';

            // the documents are also iterable, to get all fields
            foreach($document AS $field => $value)
            {
                // this converts multivalue fields to a comma-separated string
                if(is_array($value)) $value = implode(', ', $value);

                echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
            }

            echo '</table>';
        }
    }
}

htmlFooter();