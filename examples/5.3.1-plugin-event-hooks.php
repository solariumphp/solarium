<?php
require('init.php');


// this very simple plugin shows a timing for each event and display some request debug info
class basicDebug extends Solarium_Plugin_Abstract
{

    protected $_start;
    protected $_output = array();

    public function _initPlugin()
    {
        $this->_start = microtime(true);
    }

    protected function _timer($event)
    {
        $time = round(microtime(true) - $this->_start, 5);
        $this->_output[] = '['.$time.'] ' . $event;

    }

    public function display()
    {
        echo implode('<br/>', $this->_output);
    }

    public function preCreateRequest()
    {
        $this->_timer('preCreateRequest');
    }

    public function postCreateRequest()
    {
        $this->_timer('postCreateRequest');
    }

    // This method uses the aviable param(s) (see plugin abstract class)
    // You can access or modify data this way
    public function preExecuteRequest($request)
    {
        $this->_timer('preExecuteRequest');

        // this dummy param will be visible in the debug output but will also be used in the actual Solr request
        $request->addParam('dummyparam', 'dummyvalue');

        $this->_output[] = 'Request URI: ' . $request->getUri();
    }

    public function postExecuteRequest()
    {
        $this->_timer('postExecuteRequest');
    }

    public function preCreateResult()
    {
        $this->_timer('preCreateResult');
    }

    public function postCreateResult()
    {
        $this->_timer('postCreateResult');
    }

    public function preExecute()
    {
        $this->_timer('preExecute');
    }

    public function postExecute()
    {
        $this->_timer('postExecute');
    }

    public function preCreateQuery()
    {
        $this->_timer('preCreateResult');
    }

    public function postCreateQuery()
    {
        $this->_timer('postCreateResult');
    }

}


htmlHeader();

// create a client instance and register the plugin
$plugin = new basicDebug();
$client = new Solarium_Client($config);
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