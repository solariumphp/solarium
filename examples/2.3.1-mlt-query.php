<?php

require_once(__DIR__.'/init.php');
htmlHeader();

echo "<h2>Note: You need to define your own /mlt handler in solrconfig.xml to run this example!</h2>";
echo "<pre>&lt;requestHandler name=&quot;/mlt&quot; class=&quot;solr.MoreLikeThisHandler&quot; /&gt;</pre>";

// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// get a morelikethis query instance
$query = $client->createMoreLikethis();

// query a document you want similar documents for
$query->setQuery('id:SP2514N')
    ->setMltFields('manu,cat')
    ->setMinimumDocumentFrequency(1)
    ->setMinimumTermFrequency(1)
    ->setInterestingTerms('details')
    ->setBoost(true)
    ->setMatchInclude(true)
    ->createFilterQuery('stock')->setQuery('inStock:true');

// this executes the query and returns the result
$resultset = $client->moreLikeThis($query);

echo 'Document used for matching:<br/><table>';
foreach ($resultset->getMatch() as $field => $value) {
    // this converts multivalue fields to a comma-separated string
    if (is_array($value)) {
        $value = implode(', ', $value);
    }

    echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
}
echo '</table><hr/>';

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
