<?php
require(__DIR__.'/init.php');

htmlHeader();

// create a client instance and autoload the postbigrequest plugin
$client = new Solarium\Client($config);
$client->getPlugin('postbigrequest');

// create a basic query to execute
$query = $client->createSelect();

// add a huge filterquery to create a very long query string
// note: normally you would use a range for this, it's just an easy way to create a very big querystring as a test
$fq = '';
for ($i = 1; $i <= 1000; $i++) {
    $fq .= ' OR price:'.$i;
}
$fq = substr($fq, 4);
$query->createFilterQuery('fq')->setQuery($fq);

// without the plugin this query would fail as it is bigger than the default servlet container header buffer
$resultset = $client->select($query);

// display the total number of documents found by solr
echo 'NumFound: '.$resultset->getNumFound();

// show documents using the resultset iterator
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
