<?php

use Solarium\Support\Utility;

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// create a Luke query
$lukeQuery = $client->createLuke();
$lukeQuery->setShow($lukeQuery::SHOW_SCHEMA);

$result = $client->luke($lukeQuery);

$index = $result->getIndex();
$schema = $result->getSchema();
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

echo '<h1>schema</h1>';

echo '<h2>fields</h2>';

echo '<table>';
echo '<tr>';
echo '<th>name</th><th>type</th><th>flags</th>';
echo '<th>⚑<br/>indexed</th><th>⚑<br/>tokenized</th><th>⚑<br/>stored</th><th>⚑<br/>docValues</th>'; // just a few examples, there's a corresponding is*() method for each flag
echo '<th>required</th><th>default</th><th>uniqueKey</th><th>positionIncrementGap</th>';
echo '<th>copyDests</th><th>copySources</th>';
echo '</tr>';
foreach ($schema->getFields() as $field) {
    echo '<tr>';
    echo '<th>' . $field->getName() . '</th><td>' . $field->getType() . '</td><td>' . $field->getFlags() . '</td>';
    echo '<td>' . ($field->getFlags()->isIndexed() ? '⚐' : '') . '</td><td>' . ($field->getFlags()->isTokenized() ? '⚐' : '') . '</td><td>' . ($field->getFlags()->isStored() ? '⚐' : '') . '</td><td>' . ($field->getFlags()->isDocValues() ? '⚐' : '') . '</td>';
    echo '<td>' . ($field->isRequired() ? '✓' : '') . '</td><td>' . $field->getDefault() . '</td><td>' . ($field->isUniqueKey() ? '✓' : '') . '</td><td>' . $field->getPositionIncrementGap() . '</td>';
    echo '<td>' . implode(', ', $field->getCopyDests()) . '</td><td>' . implode(', ', $field->getCopySources()) . '</td>';
    echo '</tr>';
}
echo '</table>';

echo '<h2>dynamicFields</h2>';

echo '<table>';
echo '<tr>';
echo '<th>name</th><th>type</th><th>flags</th>';
echo '<th>⚑<br/>multiValued</th><th>⚑<br/>omitNorms</th><th>⚑<br/>sortMissingFirst</th><th>⚑<br/>sortMissingLast</th>'; // a few more examples of corresponding is*() methods for flags
echo '<th>positionIncrementGap</th>';
echo '<th>copyDests</th><th>copySources</th>';
echo '</tr>';
foreach ($schema->getDynamicFields() as $field) {
    echo '<tr>';
    echo '<th>' . $field->getName() . '</th><td>' . $field->getType() . '</td><td>' . $field->getFlags() . '</td>';
    echo '<td>' . ($field->getFlags()->isMultiValued() ? '⚐' : '') . '</td><td>' . ($field->getFlags()->isOmitNorms() ? '⚐' : '') . '</td><td>' . ($field->getFlags()->isSortMissingFirst() ? '⚐' : '') . '</td><td>' . ($field->getFlags()->isSortMissingLast() ? '⚐' : '') . '</td>';
    echo '<td>' . $field->getPositionIncrementGap() . '</td>';
    echo '<td>' . implode(', ', $field->getCopyDests()) . '</td><td>' . implode(', ', $field->getCopySources()) . '</td>';
    echo '</tr>';
}
echo '</table>';

echo '<h2>uniqueKeyField</h2>';

if (null !== $uniqueKeyField = $schema->getUniqueKeyField()) {
    echo $uniqueKeyField;
} else {
    echo 'No &lt;uniqueKey&gt; defined in schema.';
}

echo '<h2>similarity</h2>';

$similarity = $schema->getSimilarity();

echo '<table>';
echo '<tr><th>className</th><td>' . $similarity->getClassName() . '</td></tr>';
echo '<tr><th>details</th><td>' . $similarity->getDetails() . '</td></tr>';
echo '</table>';

echo '<h2>types</h2>';

echo '<table>';
echo '<tr>';
echo '<th>name</th><th>fields</th><th>tokenized</th><th>className</th>';
echo '<th>indexAnalyzer</th><th>queryAnalyzer</th><th>similarity</th>';
echo '</tr>';
foreach ($schema->getTypes() as $type) {
    echo '<tr>';
    echo '<th>' . $type->getName() . '</th><td>' . implode(', ', $type->getFields()) . '</td><td>' . ($type->isTokenized() ? '✓' : '') . '</td><td>' . Utility::compactSolrClassName($type->getClassName()) . '</td>';
    echo '<td>' . Utility::compactSolrClassName($type->getIndexAnalyzer()) . '</td><td>' . Utility::compactSolrClassName($type->getQueryAnalyzer()) . '</td><td>' . Utility::compactSolrClassName($type->getSimilarity()) . '</td>';
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
