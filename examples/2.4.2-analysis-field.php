<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get an analysis document query
$query = $client->createAnalysisField();

$query->setShowMatch(true);
$query->setFieldName('cat,title');
$query->setFieldType('text_general');
$query->setFieldValue('Apple 60 GB iPod with Video Playback Black');
$query->setQuery('ipod');

// this executes the query and returns the result
$results = $client->analyze($query);

// show the results
foreach ($results as $result) {

    echo '<hr><h2>Result list: ' . $result->getName() . '</h2>';

    foreach ($result as $item) {

        echo '<h3>Item: ' . $item->getName() . '</h3>';

        $indexAnalysis = $item->getIndexAnalysis();
        if (!empty($indexAnalysis)) {
            echo '<h4>Index Analysis</h4>';
            foreach ($indexAnalysis as $classes) {

                echo '<h5>'.$classes->getName().'</h5>';

                foreach ($classes as $result) {
                    echo 'Text: ' . $result->getText() . '<br/>';
                    echo 'Raw text: ' . $result->getRawText() . '<br/>';
                    echo 'Start: ' . $result->getStart() . '<br/>';
                    echo 'End: ' . $result->getEnd() . '<br/>';
                    echo 'Position: ' . $result->getPosition() . '<br/>';
                    echo 'Position history: ' . implode(', ', $result->getPositionHistory()) . '<br/>';
                    echo 'Type: ' . htmlspecialchars($result->getType()) . '<br/>';
                    echo 'Match: ' . var_export($result->getMatch(), true) . '<br/>';
                    echo '-----------<br/>';
                }
            }
        }

        $queryAnalysis = $item->getQueryAnalysis();
        if (!empty($queryAnalysis)) {
            echo '<h4>Query Analysis</h4>';
            foreach ($queryAnalysis as $classes) {

                echo '<h5>'.$classes->getName().'</h5>';

                foreach ($classes as $result) {
                    echo 'Text: ' . $result->getText() . '<br/>';
                    echo 'Raw text: ' . $result->getRawText() . '<br/>';
                    echo 'Start: ' . $result->getStart() . '<br/>';
                    echo 'End: ' . $result->getEnd() . '<br/>';
                    echo 'Position: ' . $result->getPosition() . '<br/>';
                    echo 'Position history: ' . implode(', ', $result->getPositionHistory()) . '<br/>';
                    echo 'Type: ' . htmlspecialchars($result->getType()) . '<br/>';
                    echo 'Match: ' . var_export($result->getMatch(), true) . '<br/>';
                    echo '-----------<br/>';
                }
            }
        }
    }
}

htmlFooter();
