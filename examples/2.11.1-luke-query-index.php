<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// create a Luke query
$lukeQuery = $client->createLuke();
$lukeQuery->setShow($lukeQuery::SHOW_INDEX);

$result = $client->luke($lukeQuery);

$index = $result->getIndex();

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

htmlFooter();
