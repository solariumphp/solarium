<?php
require('init.php');


// this very simple plugin shows a timing for each event and display some request debug info
class basicDebug extends Solarium\Core\Plugin
{

    protected $start;
    protected $output = array();

    public function initPlugin($client, $options)
    {
        $this->start = microtime(true);
    }

    protected function timer($event)
    {
        $time = round(microtime(true) - $this->start, 5);
        $this->output[] = '['.$time.'] ' . $event;

    }

    public function display()
    {
        echo implode('<br/>', $this->output);
    }

    public function preCreateRequest($query)
    {
        $this->timer('preCreateRequest');
    }

    public function postCreateRequest($query, $request)
    {
        $this->timer('postCreateRequest');
    }

    // This method uses the aviable param(s) (see plugin abstract class)
    // You can access or modify data this way
    public function preExecuteRequest($request)
    {
        $this->timer('preExecuteRequest');

        // this dummy param will be visible in the debug output but will also be used in the actual Solr request
        $request->addParam('dummyparam', 'dummyvalue');

        $this->output[] = 'Request URI: ' . $request->getUri();
    }

    public function postExecuteRequest($request, $response)
    {
        $this->timer('postExecuteRequest');
    }

    public function preCreateResult($query, $response)
    {
        $this->timer('preCreateResult');
    }

    public function postCreateResult($query, $response, $result)
    {
        $this->timer('postCreateResult');
    }

    public function preExecute($query)
    {
        $this->timer('preExecute');
    }

    public function postExecute($query, $result)
    {
        $this->timer('postExecute');
    }

    public function preCreateQuery($type, $options)
    {
        $this->timer('preCreateResult');
    }

    public function postCreateQuery($type, $options, $query)
    {
        $this->timer('postCreateResult');
    }

}


htmlHeader();

// create a client instance and register the plugin
$plugin = new basicDebug();
$client = new Solarium\Client($config);
$client->registerPlugin('debugger', $plugin);

// execute a select query and display the results
$query = $client->createSelect();
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

// display the debug plugin output
echo '<hr/><h1>Plugin output</h1>';
$plugin->display();

htmlFooter();