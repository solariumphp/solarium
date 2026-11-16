<?php

require_once __DIR__.'/init.php';

htmlHeader();

echo '<h2>Note: This example only works in SolrCloud since Solr 9.11 or Solr 10.1</h2>';

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// create a Luke query
$lukeQuery = $client->createLuke();
$lukeQuery->setShow($lukeQuery::SHOW_INDEX);

// request full index info per shard
$lukeQuery->setShardsInfo(true);

// in user-managed clusters, you can also set explicit shard URLs to distribute across
// $lukeQuery->setShards('localhost:8983/solr/shard1,localhost:8983/solr/shard2');

$result = $client->luke($lukeQuery);

$index = $result->getIndex();
$shards = $result->getShards();

echo '<h1>index</h1>';

echo '<table>';
echo '<tr><th>numDocs</th><td>'.$index->getNumDocs().'</td></tr>';
echo '<tr><th>maxDoc</th><td>'.$index->getMaxDoc().'</td></tr>';
echo '<tr><th>deletedDocs</th><td>'.$index->getDeletedDocs().'</td></tr>';
echo '<tr><th>segmentCount</th><td>'.$index->getSegmentCount().'</td></tr>';
echo '</table>';

echo '<hr/>';

echo '<h1>shards</h1>';

foreach ($shards as $shard) {
    $shardIndex = $shard->getIndex();

    echo '<h2>'.$shard->getAddress().'</h2>';
    echo '<h3>index</h3>';

    echo '<table>';
    echo '<tr><th>numDocs</th><td>'.$shardIndex->getNumDocs().'</td></tr>';
    echo '<tr><th>maxDoc</th><td>'.$shardIndex->getMaxDoc().'</td></tr>';
    echo '<tr><th>deletedDocs</th><td>'.$shardIndex->getDeletedDocs().'</td></tr>';
    echo '<tr><th>version</th><td>'.$shardIndex->getVersion().'</td></tr>';
    echo '<tr><th>segmentCount</th><td>'.$shardIndex->getSegmentCount().'</td></tr>';
    echo '<tr><th>current</th><td>'.($shardIndex->getCurrent() ? 'true' : 'false').'</td></tr>';
    echo '<tr><th>hasDeletions</th><td>'.($shardIndex->getHasDeletions() ? 'true' : 'false').'</td></tr>';
    echo '<tr><th>directory</th><td>'.$shardIndex->getDirectory().'</td></tr>';
    echo '<tr><th>segmentsFile</th><td>'.$shardIndex->getSegmentsFile().'</td></tr>';
    echo '<tr><th>segmentsFileSizeInBytes</th><td>'.$shardIndex->getSegmentsFileSizeInBytes().'</td></tr>';

    $userData = $shardIndex->getUserData();
    echo '<tr><th>userData</th><td>';
    if (null !== $userData->getCommitCommandVer()) {
        echo 'commitCommandVer: '.$userData->getCommitCommandVer().'<br/>';
    }
    if (null !== $userData->getCommitTimeMSec()) {
        echo 'commitTimeMSec: '.$userData->getCommitTimeMSec().'<br/>';
    }
    echo '</td></tr>';

    if (null !== $shardIndex->getLastModified()) {
        echo '<tr><th>lastModified</th><td>'.$shardIndex->getLastModified()->format(DATE_RFC3339_EXTENDED).'</td></tr>';
    }
    echo '</table>';
}

htmlFooter();
