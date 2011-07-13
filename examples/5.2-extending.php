<?php

require('init.php');
htmlHeader();

// This is a custom query class that could have some customized logic
class MyQuery extends Solarium_Query_Select
{
    // ...customization here...
}

// And this is the extended client, that modifies the default query mapping
// for select queries to our custom query class.
// BTW, the same could also be done using a plugin, see example 5.3.2
class MyClient extends Solarium_Client
{
     /**
     * Querytype mappings
     */
    protected $_queryTypes = array(
        self::QUERYTYPE_SELECT => array(
            'query'          => 'MyQuery',
            'requestbuilder' => 'Solarium_Client_RequestBuilder_Select',
            'responseparser' => 'Solarium_Client_ResponseParser_Select'
        ),
        self::QUERYTYPE_UPDATE => array(
            'query'          => 'Solarium_Query_Update',
            'requestbuilder' => 'Solarium_Client_RequestBuilder_Update',
            'responseparser' => 'Solarium_Client_ResponseParser_Update'
        ),
        self::QUERYTYPE_PING => array(
            'query'          => 'Solarium_Query_Ping',
            'requestbuilder' => 'Solarium_Client_RequestBuilder_Ping',
            'responseparser' => 'Solarium_Client_ResponseParser_Ping'
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