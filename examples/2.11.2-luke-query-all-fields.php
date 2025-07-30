<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// create a Luke query
$lukeQuery = $client->createLuke();
$lukeQuery->setShow($lukeQuery::SHOW_ALL);

// omitting index flags for each field can speed up Luke requests
//$lukeQuery->setIncludeIndexFieldFlags(false);

$result = $client->luke($lukeQuery);

$index = $result->getIndex();
$fields = $result->getFields();
$info = $result->getInfo();

echo '<h1>index</h1>';

echo '<table>';
echo '<tr><th>numDocs</th><td>' . $index->getNumDocs() . '</td></tr>';
echo '<tr><th>maxDoc</th><td>' . $index->getMaxDoc() . '</td></tr>';
echo '<tr><th>deletedDocs</th><td>' . $index->getDeletedDocs() . '</td></tr>';
echo '<tr><th>indexHeapUsageBytes</th><td>' . ($index->getIndexHeapUsageBytes() ?? '(not supported by this version of Solr)') . '</td></tr>';
echo '<tr><th>version</th><td>' . $index->getVersion() . '</td></tr>';
echo '<tr><th>segmentCount</th><td>' . $index->getSegmentCount() . '</td></tr>';
echo '<tr><th>current</th><td>' . ($index->getCurrent() ? 'true' : 'false') . '</td></tr>';
echo '<tr><th>hasDeletions</th><td>' . ($index->getHasDeletions() ? 'true' : 'false') . '</td></tr>';
echo '<tr><th>directory</th><td>' . $index->getDirectory() . '</td></tr>';
echo '<tr><th>segmentsFile</th><td>' . $index->getSegmentsFile() . '</td></tr>';
echo '<tr><th>segmentsFileSizeInBytes</th><td>' . $index->getSegmentsFileSizeInBytes() . '</td></tr>';

$userData = $index->getUserData();
echo '<tr><th>userData</th><td>';
if (null !== $userData->getCommitCommandVer()) {
    echo 'commitCommandVer: ' . $userData->getCommitCommandVer() . '<br/>';
}
if (null !== $userData->getCommitTimeMSec()) {
    echo 'commitTimeMSec: ' . $userData->getCommitTimeMSec() . '<br/>';
}
echo '</td></tr>';

if (null !== $index->getLastModified()) {
    echo '<tr><th>lastModified</th><td>' . $index->getLastModified()->format(DATE_RFC3339_EXTENDED) . '</td></tr>';
}
echo '</table>';

echo '<hr/>';

echo '<h1>fields</h1>';

echo '<table>';
echo '<tr>';
echo '<th>name</th><th>type</th><th>schema</th>';
echo '<th>schema⚑<br/>indexed</th><th>schema⚑<br/>tokenized</th><th>schema⚑<br/>stored</th><th>schema⚑<br/>docValues</th>'; // just a few examples, there's a corresponding is*() method for each flag
echo '<th>dynamicBase</th><th>index</th>';
echo '<th>index⚑<br/>tokenized</th><th>index⚑<br/>termVectors</th><th>index⚑<br/>omitNorms</th>'; // a few more examples of corresponding is*() methods for flags
echo '<th>docs</th>';
echo '</tr>';
foreach ($fields as $field) {
    echo '<tr>';
    echo '<th>' . $field->getName() . '</th><td>' . $field->getType() . '</td><td>' . $field->getSchema() . '</td>';
    echo '<td>' . ($field->getSchema()->isIndexed() ? '⚐' : '') . '</td><td>' . ($field->getSchema()->isTokenized() ? '⚐' : '') . '</td><td>' . ($field->getSchema()->isStored() ? '⚐' : '') . '</td><td>' . ($field->getSchema()->isDocValues() ? '⚐' : '') . '</td>';
    echo '<td>' . $field->getDynamicBase() . '</td><td>' . $field->getIndex() . '</td>';
    // some fields have '(unstored field)' or no index flags at all in the result
    // and with $lukeQuery->setIncludeIndexFieldFlags(false), index flags are omitted for each field
    if (is_object($field->getIndex())) {
        echo '<td>' . ($field->getIndex()->isTokenized() ? '⚐' : '') . '</td><td>' . ($field->getIndex()->isTermVectors() ? '⚐' : '') . '</td><td>' . ($field->getIndex()->isOmitNorms() ? '⚐' : '') . '</td>';
    } else {
        echo '<td></td><td></td><td></td>';
    }
    echo '<td>' . $field->getDocs() . '</td>';
    echo '</tr>';
}
echo '</table>';

echo '<hr/>';

echo '<h1>info</h1>';

echo '<table>';
echo '<tr><th>key</th><td>';
foreach ($info->getKey() as $abbreviation => $flag) {
    echo $abbreviation . ': ' . $flag . '<br/>';
}
echo '</td></tr>';
echo '<tr><th>NOTE</th><td>' . $info->getNote() . '</td></tr>';
echo '</table>';

htmlFooter();
