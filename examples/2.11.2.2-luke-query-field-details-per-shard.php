<?php

require_once __DIR__.'/init.php';

htmlHeader();

echo '<h2>Note: This example only works in SolrCloud since Solr 9.11 or Solr 10.1</h2>';
echo '<h2>Note: Use with caution especially on large indexes!</h2>';

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// create a Luke query
$lukeQuery = $client->createLuke();

// set the fields you want detailed information for
$lukeQuery->setFields('text,cat,price_c');

// you can also get detailed information for all fields
// $lukeQuery->setFields('*');

// request per-shard field field details
$lukeQuery->setShardsInfo(true);

// in user-managed clusters, you can also set explicit shard URLs to distribute across
// $lukeQuery->setShards('localhost:8983/solr/shard1,localhost:8983/solr/shard2');

// omitting index flags for each field can speed up Luke requests
// $lukeQuery->setIncludeIndexFieldFlags(false);

// set the number of top terms for each field (Solr's default is 10)
$lukeQuery->setNumTerms(5);

$result = $client->luke($lukeQuery);

$index = $result->getIndex();
$fields = $result->getFields();
$shards = $result->getShards();
$info = $result->getInfo();

echo '<h1>index</h1>';

echo '<table>';
echo '<tr><th>numDocs</th><td>'.$index->getNumDocs().'</td></tr>';
echo '<tr><th>maxDoc</th><td>'.$index->getMaxDoc().'</td></tr>';
echo '<tr><th>deletedDocs</th><td>'.$index->getDeletedDocs().'</td></tr>';
echo '<tr><th>segmentCount</th><td>'.$index->getSegmentCount().'</td></tr>';
echo '</table>';

echo '<hr/>';

echo '<h1>fields</h1>';

foreach ($fields as $field) {
    echo '<h2>'.$field.'</h2>';

    echo '<table>';
    echo '<tr><th>type</th><td>'.$field->getType().'</td></tr>';
    echo '<tr><th>schema</th><td>';
    foreach ($field->getSchema() as $flag) {
        echo $flag.'<br/>';
    }
    echo '</td></tr>';
    if (null !== $field->getDynamicBase()) {
        echo '<tr><th>dynamicBase</th><td>'.$field->getDynamicBase().'</td></tr>';
    }
    // some fields don't have index flags in the result (with $lukeQuery->setIncludeIndexFieldFlags(false), none of the fields have)
    if (null !== $field->getIndex()) {
        echo '<tr><th>index</th><td>';
        // some fields have '(unstored field)' in the result instead of flags
        if (is_object($field->getIndex())) {
            foreach ($field->getIndex() as $flag) {
                echo $flag.'<br/>';
            }
        } else {
            echo $field->getIndex();
        }
        echo '</td></tr>';
    }
    if (null !== $field->getDocs()) {
        echo '<tr><th>docs</th><td>'.$field->getDocs().'</td></tr>';
    }
    echo '</table>';
}

echo '<hr/>';

echo '<h1>shards</h1>';

foreach ($shards as $shard) {
    $shardIndex = $shard->getIndex();
    $shardFields = $shard->getFields();

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

    // not available if none of the requested fields exist on this shard
    if (null !== $shardFields) {
        echo '<h3>fields</h3>';

        foreach ($shardFields as $field) {
            echo '<h4>'.$field.'</h4>';

            echo '<table>';
            if (null !== $field->getDistinct()) {
                echo '<tr><th>distinct</th><td>'.$field->getDistinct().'</td></tr>';
            }
            if (null !== $field->getTopTerms()) {
                echo '<tr><th>topTerms</th><td>';
                foreach ($field->getTopTerms() as $term => $frequency) {
                    echo $term.': '.$frequency.'<br/>';
                }
                echo '</td></tr>';
            }
            if (null !== $field->getHistogram()) {
                echo '<tr><th>histogram</th><td>';
                foreach ($field->getHistogram() as $bucket => $frequency) {
                    echo $bucket.': '.$frequency.'<br/>';
                }
                echo '</td></tr>';
            }
            echo '</table>';
        }
    }
}

echo '<hr/>';

echo '<h1>info</h1>';

echo '<table>';
echo '<tr><th>key</th><td>';
foreach ($info->getKey() as $abbreviation => $flag) {
    echo $abbreviation.': '.$flag.'<br/>';
}
echo '</td></tr>';
echo '<tr><th>NOTE</th><td>'.$info->getNote().'</td></tr>';
echo '</table>';

htmlFooter();
