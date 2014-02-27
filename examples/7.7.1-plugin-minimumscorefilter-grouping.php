<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// enable the filter plugin and get a query instance
$filter = $client->getPlugin('minimumscorefilter');
$query = $client->createQuery($filter::QUERY_TYPE);
$query->setRows(50);
$query->setFields(array('id','name','score'));
$query->setQuery('memory');
$query->setFilterRatio(.8);
$query->setFilterMode($query::FILTER_MODE_MARK);

// get grouping component and set a field to group by
$groupComponent = $query->getGrouping();
$groupComponent->addField('inStock');
// maximum number of items per group
$groupComponent->setLimit(10);
// get a group count
$groupComponent->setNumberOfGroups(true);

// this executes the query and returns the result
$resultset = $client->select($query);

$groups = $resultset->getGrouping();
foreach ($groups as $groupKey => $fieldGroup) {

    echo '<h1>'.$groupKey.'</h1>';
    echo 'Matches: '.$fieldGroup->getMatches().'<br/>';
    echo 'Number of groups: '.$fieldGroup->getNumberOfGroups();

    foreach ($fieldGroup as $valueGroup) {

        echo '<h2>'.(int)$valueGroup->getValue().'</h2>';

        foreach ($valueGroup as $document) {

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

            // by setting the FILTER_MARK option we get a special method to test each document
            if ($document->markedAsLowScore()) {
                echo '<b>MARKED AS LOW SCORE</b>';
            }
        }
    }
}

htmlFooter();
