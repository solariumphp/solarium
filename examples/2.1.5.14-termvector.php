<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a select query instance
$query = $client->createSelect();
$query->setQuery($query->getHelper()->rangeQuery('includes', null, null));

// set a handler that is configured with a term vector component in solrconfig.xml (or add it to your default handler)
$query->setHandler('tvrh');

// get term vector component
$termVectorComponent = $query->getTermVector();
// return term vectors for this field
$termVectorComponent->setFields('includes');
// enable all boolean parameters (some of these can be computationally expensive!)
$termVectorComponent->setAll(true);

// this executes the query and returns the result
$resultset = $client->select($query);

$termVector = $resultset->getTermVector();
foreach ($termVector as $key => $document) {

    echo '<h1>'.$key.'</h1>';

    foreach ($document as $fieldName => $field) {

        echo '<h2>'.$fieldName.'</h2>';

        foreach ($field as $term => $termInfo) {

            echo '<hr/><h3>'.$term.'</h3><table>';

            echo '<tr><th>Term frequency</th><td>' . $termInfo['tf'] ?? '' . '</td></tr>';
            echo '<tr><th>Document frequency</th><td>' . $termInfo['df'] ?? '' . '</td></tr>';
            echo '<tr><th>TF * IDF</th><td>' . $termInfo['tf-idf'] ?? '' . '</td></tr>';

            echo '<tr><th>Positions</th><td>';
            foreach ($termInfo['positions'] ?? [] as $position) {
                echo $position . '<br/>';
            }
            echo '</td></tr>';

            echo '<tr><th>Offsets</th><td>';
            foreach ($termInfo['offsets'] ?? [] as $offset) {
                echo 'start: '.$offset['start'] . '<br/>';
                echo 'end: '.$offset['end'] . '<br/>';
            }
            echo '</td></tr>';

            echo '<tr><th>Payloads</th><td>';
            foreach ($termInfo['payloads'] ?? [] as $payload) {
                echo $payload . '<br/>';
            }
            echo '</td></tr>';

            echo '</table>';
        }
    }
}

htmlFooter();
