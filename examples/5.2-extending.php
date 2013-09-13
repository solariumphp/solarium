<?php

require(__DIR__.'/init.php');
use Solarium\Client;
use Solarium\QueryType\Select\Query\Query as Select;

htmlHeader();

// This is a custom query class that could have some customized logic
class MyQuery extends Select
{
    // ...customization here...
}

// And this is the extended client, that modifies the default query mapping
// for select queries to our custom query class.
// BTW, the same could also be done using a plugin, see example 5.3.2
class MyClient extends Client
{
    /**
     * Querytype mappings
     */
    protected $queryTypes = array(
        self::QUERY_SELECT => array(
            'query'          => 'MyQuery',
            'requestbuilder' => 'Solarium\QueryType\Select\RequestBuilder\RequestBuilder',
            'responseparser' => 'Solarium\QueryType\Select\ResponseParser\ResponseParser'
        ),
    );
}


// create a client instance
$client = new MyClient($config);

// create a select query instance
$query = $client->createSelect();

// check the query class, it should be our custom query class
echo 'Query class: ' . get_class($query) . '<br/>';

// execute query
$result = $client->execute($query);

// display the total number of documents found by solr
echo 'NumFound: '.$result->getNumFound();

// show documents using the resultset iterator
foreach ($result as $document) {

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
