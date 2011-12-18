<?php

require('init.php');
htmlHeader();

// This is a custom query class that could have some customized logic
class MyQuery extends Solarium\Query\Select
{
    // ...customization here...
}

// And this is the extended client, that modifies the default query mapping
// for select queries to our custom query class.
// BTW, the same could also be done using a plugin, see example 5.3.2
class MyClient extends Solarium\Client
{
     /**
     * Querytype mappings
     */
    protected $_queryTypes = array(
        self::QUERYTYPE_SELECT => array(
            'query'          => 'MyQuery',
            'requestbuilder' => 'Solarium\Client\RequestBuilder\Select',
            'responseparser' => 'Solarium\Client\ResponseParser\Select'
        ),
        self::QUERYTYPE_UPDATE => array(
            'query'          => 'Solarium\Query\Update',
            'requestbuilder' => 'Solarium\Client\RequestBuilder\Update',
            'responseparser' => 'Solarium\Client\ResponseParser\Update'
        ),
        self::QUERYTYPE_PING => array(
            'query'          => 'Solarium\Query\Ping',
            'requestbuilder' => 'Solarium\Client\RequestBuilder\Ping',
            'responseparser' => 'Solarium\Client\ResponseParser\Ping'
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
    foreach($document AS $field => $value)
    {
        // this converts multivalue fields to a comma-separated string
        if(is_array($value)) $value = implode(', ', $value);
        
        echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
    }

    echo '</table>';
}

htmlFooter();