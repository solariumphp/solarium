This querytype accepts one or more values that can be analyzed for fieldtypes and/or fieldnames.

Building an analysis query
--------------------------

See the example code below.

**Available options:**

| Name        | Type    | Default value                                | Description                          |
|-------------|---------|----------------------------------------------|--------------------------------------|
| query       | string  | null                                         | Query to use for query-time analysis |
| showmatch   | boolean | null                                         |                                      |
| handler     | string  | analysis/field                               |                                      |
| resultclass | string  | Solarium\\QueryType\\Analysis\\Result\\Field |                                      |
| fieldvalue  | string  |                                              | Value(s) to analyze                  |
| fieldname   | string  |                                              | Fieldname(s) to analyze for          |
| fieldtype   | string  |                                              | Fieldtype(s) to analyze for          |
||

Executing an analysis fieldquery
--------------------------------

Use the `analyze` method of the client to execute the query object. See the example code below.

Result of an analysis field query
---------------------------------

The result contains a nested data model that is best explained by looking at the example code below.

Example
-------

```php
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

```
