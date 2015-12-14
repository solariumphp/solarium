<?php
require(__DIR__.'/init.php');
use Solarium\Client;
use Solarium\Core\Plugin\AbstractPlugin;
use Solarium\QueryType\Select\Query\Query as Select;

// This is a custom query class that could have some customized logic
class MyQuery extends Select
{
    // ...customization here...
}

// this very simple plugin that modifies the default querytype mapping
class QueryCustomizer extends AbstractPlugin
{
    public function initPlugin($client, $options)
    {
        $client->registerQueryType(
            Client::QUERY_SELECT,
            'MyQuery'
        );
    }
}


htmlHeader();

// create a client instance and register the plugin
$client = new Client($config);
$client->registerPlugin('querycustomizer', 'QueryCustomizer');

// create a select query instance
$query = $client->createSelect();

// check the query class, it should be our custom query class
echo 'Query class: ' . get_class($query) . '<br/>';

// execute the query and display the results
$resultset = $client->select($query);
echo 'NumFound: '.$resultset->getNumFound();
foreach ($resultset as $document) {

    echo '<hr/><table>';

    foreach ($document as $field => $value) {
        if (is_array($value)) {
            $value = implode(', ', $value);
        }

        echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
    }

    echo '</table>';
}

htmlFooter();
