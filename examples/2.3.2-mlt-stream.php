<?php

require_once(__DIR__.'/init.php');
htmlHeader();

echo "<h2>Note: You need to define your own /mlt handler in solrconfig.xml to run this example!</h2>";
echo "<pre>&lt;requestHandler name=&quot;/mlt&quot; class=&quot;solr.MoreLikeThisHandler&quot; /&gt;</pre>";

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a morelikethis query instance
$query = $client->createMoreLikeThis();

// supply text you want similar documents for
$text = <<<EOT
Samsung SpinPoint P120 SP2514N - hard drive - 250 GB - ATA-133
7200RPM, 8MB cache, IDE Ultra ATA-133, NoiseGuard, SilentSeek technology, Fluid Dynamic Bearing (FDB) motor
EOT;

$query->setQuery($text);
$query->setQueryStream(true);
$query->setMltFields('name,features');
$query->setMinimumDocumentFrequency(1);
$query->setMinimumTermFrequency(1);
$query->createFilterQuery('stock')->setQuery('inStock:true');
$query->setInterestingTerms('details');
$query->setBoost(true);

// this executes the query and returns the result
$resultset = $client->moreLikeThis($query);

// display the total number of MLT documents found by Solr
echo 'Number of MLT matches found: '.$resultset->getNumFound().'<br/><br/>';

// display the "interesting" terms for the query
echo 'Interesting terms with the boost value used:';
echo '<ul>';
foreach ($resultset->getInterestingTerms() as $term => $boost) {
    echo '<li>'.$term.' (boost='.$boost.')</li>';
}
echo '</ul>';

echo '<b>Listing of matched docs:</b>';

// show MLT documents using the resultset iterator
foreach ($resultset as $document) {

    echo '<hr/><table>';

    // the documents are also iterable, to get all fields
    foreach ($document as $field => $value) {
        // this converts multivalue fields to a comma-separated string
        if (is_array($value)) {
            $value = implode(', ', $value);
        }

        echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
    }

    echo '</table>';
}

htmlFooter();
