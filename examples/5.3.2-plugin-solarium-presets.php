<?php
require('init.php');

// This is a custom query class that could have some customized logic
class MyQuery extends Solarium_Query_Select
{
    // ...customization here...
}

// this very simple plugin that modifies the default querytype mapping
class queryCustomizer extends Solarium_Plugin_Abstract
{

    protected function _initPlugin()
    {
        $this->_client->registerQueryType(
            Solarium_Client::QUERYTYPE_SELECT,
            'MyQuery',
            'Solarium_Client_RequestBuilder_Select',
            'Solarium_Client_ResponseParser_Select'
        );
    }
    
}


htmlHeader();

// create a client instance and register the plugin
$client = new Solarium_Client($config);
$client->registerPlugin('querycustomizer', 'queryCustomizer');

// create a select query instance
$query = $client->createSelect();

// check the query class, it should be our custom query class
echo 'Query class: ' . get_class($query) . '<br/>';

// execute the query and display the results
$resultset = $client->select($query);
echo 'NumFound: '.$resultset->getNumFound();
foreach ($resultset as $document) {

    echo '<hr/><table>';

    foreach($document AS $field => $value)
    {
        if(is_array($value)) $value = implode(', ', $value);

        echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
    }

    echo '</table>';
}

htmlFooter();