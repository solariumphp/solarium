<?php

require('init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get a morelikethis query instance
$query = $client->createMoreLikeThis();

$query->setQuery('id:SP2514N');
$query->setMltFields('manu,cat');
$query->setMinimumDocumentFrequency(1);
$query->setMinimumTermFrequency(1);
$query->createFilterQuery('stock')->setQuery('inStock:true');
$query->setInterestingTerms('details');
$query->setMatchInclude(true);

// this executes the query and returns the result
$resultset = $client->select($query);

echo 'Document used for matching:<br/><table>';
foreach($resultset->getMatch() AS $field => $value)
{
    // this converts multivalue fields to a comma-separated string
    if(is_array($value)) $value = implode(', ', $value);

    echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
}
echo '</table><hr/>';

// display the total number of MLT documents found by solr
echo 'Number of MLT matches found: '.$resultset->getNumFound().'<br/><br/>';
echo '<b>Listing of matched docs:</b>';

// show MLT documents using the resultset iterator
foreach ($resultset as $document) {

    echo '<hr/><table>';

    // the documents are also iterable, to get all fields
    foreach($document AS $field => $value)
    {
        // this converts multivalue fields to a comma-separated string
        if(is_array($value)) $value = implode(', ', $value);
        
        echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
    }

    echo '</table>';
}

htmlFooter();