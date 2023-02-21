<?php

require_once(__DIR__.'/init.php');
htmlHeader();

echo '<h2>Note: Use with caution especially on large indexes!</h2>';

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// create a Luke query
$lukeQuery = $client->createLuke();

// set the fields you want detailed information for
$lukeQuery->setFields('text,cat,price_c');

// you can also get detailed information for all fields
//$lukeQuery->setFields('*');

// omitting index flags for each field can speed up Luke requests
//$lukeQuery->setIncludeIndexFieldFlags(false);

// set the number of top terms for each field (Solr's default is 10)
$lukeQuery->setNumTerms(5);

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

foreach ($fields as $field) {
    echo '<h2>' . $field . '</h2>';

    echo '<table>';
    echo '<tr><th>type</th><td>' . $field->getType() . '</td></tr>';
    echo '<tr><th>schema</th><td>';
    foreach ($field->getSchema() as $flag) {
        echo $flag . '<br/>';
    }
    echo '</td></tr>';
    if (null !== $field->getDynamicBase()) {
        echo '<tr><th>dynamicBase</th><td>' . $field->getDynamicBase() . '</td></tr>';
    }
    // some fields don't have index flags in the result (with $lukeQuery->setIncludeIndexFieldFlags(false), none of the fields have)
    if (null !== $field->getIndex()) {
        echo '<tr><th>index</th><td>';
        // some fields have '(unstored field)' in the result instead of flags
        if (is_object($field->getIndex())) {
            foreach ($field->getIndex() as $flag) {
                echo $flag . '<br/>';
            }
        } else {
            echo $field->getIndex();
        }
        echo '</td></tr>';
    }
    if (null !== $field->getDocs()) {
        echo '<tr><th>docs</th><td>' . $field->getDocs() . '</td></tr>';
    }
    if (null !== $field->getDistinct()) {
        echo '<tr><th>distinct</th><td>' . $field->getDistinct() . '</td></tr>';
    }
    if (null !== $field->getTopTerms()) {
        echo '<tr><th>topTerms</th><td>';
        foreach ($field->getTopTerms() as $term => $frequency) {
            echo $term . ': ' . $frequency . '<br/>';
        }
        echo '</td></tr>';
    }
    if (null !== $field->getHistogram()) {
        echo '<tr><th>histogram</th><td>';
        foreach ($field->getHistogram() as $bucket => $frequency) {
            echo $bucket . ': ' . $frequency . '<br/>';
        }
        echo '</td></tr>';
    }
    echo '</table>';
}

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
