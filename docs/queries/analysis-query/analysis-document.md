This querytype accepts one or more documents as input and will return analysis details for each document.

Building an analysis document query
-----------------------------------

See the example code below.

**Available options:**

| Name        | Type    | Default value                                   | Description                          |
|-------------|---------|-------------------------------------------------|--------------------------------------|
| query       | string  | null                                            | Query to use for query-time analysis |
| showmatch   | boolean | null                                            |                                      |
| handler     | string  | analysis/document                               |                                      |
| resultclass | string  | Solarium\\QueryType\\Analysis\\Result\\Document |                                      |
||

Executing an analysis document query
------------------------------------

Use the `analyze` method of the client to execute the query object. See the example code below.

Result of an analysis document query
------------------------------------

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
$query = $client->createAnalysisDocument();

$query->setShowMatch(true);
$query->setQuery('ipod');

$doc = new Solarium\QueryType\Update\Query\Document(
    array(
        'id' => 'MA147LL',
        'name' => 'Apple 60 GB iPod with Video Playback Black',
        'manu' => 'Apple Computer Inc.',
        'cat' => 'electronics',
        'cat' => 'music',
        'features' => 'iTunes, Podcasts, Audiobooks',
        'features' => 'Stores up to 15,000 songs, 25,000 photos, or 150 hours of video',
        'features' => '2.5-inch, 320x240 color TFT LCD display with LED backlight',
        'features' => 'Up to 20 hours of battery life',
        'features' => 'Plays AAC, MP3, WAV, AIFF, Audible, Apple Lossless, H.264 video',
        'features' => 'Notes, Calendar, Phone book, Hold button, Date display, Photo wallet, Built-in games, '.
            'JPEG photo playback, Upgradeable firmware, USB 2.0 compatibility, Playback speed control, '.
            'Rechargeable capability, Battery level indication',
        'includes' => 'earbud headphones, USB cable',
        'weight' => 5.5,
        'price' => 399.00,
        'popularity' => 10,
        'inStock' => true,
    )
);

$query->addDocument($doc);

// this executes the query and returns the result
$result = $client->analyze($query);

// show the results
foreach ($result as $document) {

    echo '<hr><h2>Document: ' . $document->getName() . '</h2>';

    foreach ($document as $field) {

        echo '<h3>Field: ' . $field->getName() . '</h3>';

        $indexAnalysis = $field->getIndexAnalysis();
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

        $queryAnalysis = $field->getQueryAnalysis();
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
