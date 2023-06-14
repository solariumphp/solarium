<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get an extract query instance and add settings
$query = $client->createExtract();
$query->setFile(__DIR__.'/index.html');
$query->setExtractOnly(true);
$query->setExtractFormat($query::EXTRACT_FORMAT_TEXT);

// this executes the query and returns the result
$result = $client->extract($query);

echo '<b>Extract query executed</b><br/>';

echo '<textarea readonly="readonly" style="width:100%;height:400px">';
echo htmlspecialchars(trim($result->getFile()));
echo '</textarea>';

echo '<table>';

foreach ($result->getFileMetadata() as $field => $value) {
    if (is_array($value)) {
        $value = implode('<br/>', $value);
    }

    echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
}

echo '</table>';

htmlFooter();
