<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// create a Luke query
$lukeQuery = $client->createLuke();
$lukeQuery->setShow($lukeQuery::SHOW_DOC);

// get a document using the uniqueKeyField specified in the schema
$lukeQuery->setId('9885A004');

// alternatively, you can use a Lucene documentID
//$lukeQuery->setDocId(27);

$result = $client->luke($lukeQuery);

$index = $result->getIndex();
$docInfo = $result->getDoc();
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

echo '<h1>doc</h1>';

echo '<h2>docId</h2>';

echo '<table>';
echo '<tr><th>Lucene documentID</th><td>' . $docInfo->getDocId() . '</td></tr>';
echo '</table>';

echo '<h2>lucene</h2>';

echo '<table>';
echo '<tr>';
echo '<th>name</th><th>type</th><th>schema</th>';
echo '<th>schema⚑<br/>indexed</th><th>schema⚑<br/>tokenized</th><th>schema⚑<br/>stored</th><th>schema⚑<br/>docValues</th>'; // just a few examples, there's a corresponding is*() method for each flag
echo '<th>flags</th>';
echo '<th>flags⚑<br/>indexed</th><th>flags⚑<br/>omitNorms</th><th>flags⚑<br/>omitTermFreqAndPositions</th>'; // a few more examples of corresponding is*() methods for flags
echo '<th>value</th><th>docFreq</th><th>termVector</th>';
echo '</tr>';
foreach ($docInfo->getLucene() as $field) {
    echo '<tr>';
    echo '<th>' . $field->getName() . '</th><td>' . $field->getType() . '</td><td>' . $field->getSchema() . '</td>';
    echo '<td>' . ($field->getSchema()->isIndexed() ? '⚐' : '') . '</td><td>' . ($field->getSchema()->isTokenized() ? '⚐' : '') . '</td><td>' . ($field->getSchema()->isStored() ? '⚐' : '') . '</td><td>' . ($field->getSchema()->isDocValues() ? '⚐' : '') . '</td>';
    echo '<td>' . $field->getFlags() . '</td>';
    echo '<td>' . ($field->getFlags()->isIndexed() ? '⚐' : '') . '</td><td>' . ($field->getFlags()->isOmitNorms() ? '⚐' : '') . '</td><td>' . ($field->getFlags()->isOmitTermFreqAndPositions() ? '⚐' : '') . '</td>';
    echo '<td>' . $field->getValue() . '</td><td>' . $field->getDocFreq() . '</td><td>';
    if (null !== $termVector = $field->getTermVector()) {
        foreach ($termVector as $term => $frequency) {
            echo $term . ': ' . $frequency . '<br/>';
        }
    }
    echo '</td>';
    echo '</tr>';
}
echo '</table>';

echo '<h2>solr</h2>';

echo '<table>';
foreach ($docInfo->getSolr() as $field => $value) {
    if (is_array($value)) {
        $value = implode(', ', $value);
    }

    echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
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
