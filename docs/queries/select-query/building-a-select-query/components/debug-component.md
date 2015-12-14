For a description of Solr debugging see the [http://wiki.apache.org/solr/CommonQueryParameters\#Debugging Solr wiki page](http://wiki.apache.org/solr/CommonQueryParameters#Debugging_Solr_wiki_page "wikilink").

Options
-------

| Name         | Type   | Default value | Description                                                                                                                                                                                                                                                                               |
|--------------|--------|---------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| explainother | string | null          | Using this parameter you can specify a Lucene query to identify a set of documents. If non-blank, the explain info of each document that matches this query, relative to the main query (specified by the q parameter) will be returned along with the rest of the debugging information. |
||

Example
-------

```php
<?php

require(__DIR__.'/init.php');
htmlHeader();

// create a client instance
$client = new Solarium\Client($config);

// get a select query instance
$query = $client->createSelect();
$query->setQuery('ipod');

// add debug settings
$debug = $query->getDebug();
$debug->setExplainOther('id:MA*');

// this executes the query and returns the result
$resultset = $client->select($query);
$debugResult = $resultset->getDebug();

// display the debug results
echo '<h1>Debug data</h1>';
echo 'Querystring: ' . $debugResult->getQueryString() . '<br/>';
echo 'Parsed query: ' . $debugResult->getParsedQuery() . '<br/>';
echo 'Query parser: ' . $debugResult->getQueryParser() . '<br/>';
echo 'Other query: ' . $debugResult->getOtherQuery() . '<br/>';

echo '<h2>Explain data</h2>';
foreach ($debugResult->getExplain() as $key => $explanation) {
    echo '<h3>Document key: ' . $key . '</h3>';
    echo 'Value: ' . $explanation->getValue() . '<br/>';
    echo 'Match: ' . (($explanation->getMatch() == true) ? 'true' : 'false')  . '<br/>';
    echo 'Description: ' . $explanation->getDescription() . '<br/>';
    echo '<h4>Details</h4>';
    foreach ($explanation as $detail) {
        echo 'Value: ' . $detail->getValue() . '<br/>';
        echo 'Match: ' . (($detail->getMatch() == true) ? 'true' : 'false')  . '<br/>';
        echo 'Description: ' . $detail->getDescription() . '<br/>';
        echo '<hr/>';
    }
}

echo '<h2>ExplainOther data</h2>';
foreach ($debugResult->getExplainOther() as $key => $explanation) {
    echo '<h3>Document key: ' . $key . '</h3>';
    echo 'Value: ' . $explanation->getValue() . '<br/>';
    echo 'Match: ' . (($explanation->getMatch() == true) ? 'true' : 'false')  . '<br/>';
    echo 'Description: ' . $explanation->getDescription() . '<br/>';
    echo '<h4>Details</h4>';
    foreach ($explanation as $detail) {
        echo 'Value: ' . $detail->getValue() . '<br/>';
        echo 'Match: ' . (($detail->getMatch() == true) ? 'true' : 'false')  . '<br/>';
        echo 'Description: ' . $detail->getDescription() . '<br/>';
        echo '<hr/>';
    }
}

echo '<h2>Timings (in ms)</h2>';
echo 'Total time: ' . $debugResult->getTiming()->getTime() . '<br/>';
echo '<h3>Phases</h3>';
foreach ($debugResult->getTiming()->getPhases() as $phaseName => $phaseData) {
    echo '<h4>' . $phaseName . '</h4>';
    foreach ($phaseData as $class => $time) {
        echo $class . ': ' . $time . '<br/>';
    }
    echo '<hr/>';
}

htmlFooter();

```
