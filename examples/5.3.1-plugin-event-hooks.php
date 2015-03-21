<?php
require(__DIR__.'/init.php');
use Solarium\Core\Event\Events;

// this very simple plugin shows a timing for each event and display some request debug info
class BasicDebug extends Solarium\Core\Plugin\AbstractPlugin
{
    protected $start;
    protected $output = array();

    protected function initPluginType()
    {
        $this->start = microtime(true);

        $dispatcher = $this->client->getEventDispatcher();
        $dispatcher->addListener(Events::PRE_CREATE_REQUEST, array($this, 'preCreateRequest'));
        $dispatcher->addListener(Events::POST_CREATE_REQUEST, array($this, 'postCreateRequest'));
        $dispatcher->addListener(Events::PRE_EXECUTE_REQUEST, array($this, 'preExecuteRequest'));
        $dispatcher->addListener(Events::POST_EXECUTE_REQUEST, array($this, 'postExecuteRequest'));
        $dispatcher->addListener(Events::PRE_CREATE_RESULT, array($this, 'preCreateResult'));
        $dispatcher->addListener(Events::POST_CREATE_RESULT, array($this, 'postCreateResult'));
        $dispatcher->addListener(Events::PRE_EXECUTE, array($this, 'preExecute'));
        $dispatcher->addListener(Events::POST_EXECUTE, array($this, 'postExecute'));
        $dispatcher->addListener(Events::PRE_CREATE_QUERY, array($this, 'preCreateQuery'));
        $dispatcher->addListener(Events::POST_CREATE_QUERY, array($this, 'postCreateQuery'));
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

    public function preCreateRequest()
    {
        $this->timer('preCreateRequest');
    }

    public function postCreateRequest()
    {
        $this->timer('postCreateRequest');
    }

    // This method uses the aviable param(s) (see plugin abstract class)
    // You can access or modify data this way
    public function preExecuteRequest($event)
    {
        $this->timer('preExecuteRequest');

        // this dummy param will be visible in the debug output but will also be used in the actual Solr request
        $event->getRequest()->addParam('dummyparam', 'dummyvalue');

        $this->output[] = 'Request URI: ' . $event->getRequest()->getUri();
    }

    public function postExecuteRequest()
    {
        $this->timer('postExecuteRequest');
    }

    public function preCreateResult()
    {
        $this->timer('preCreateResult');
    }

    public function postCreateResult()
    {
        $this->timer('postCreateResult');
    }

    public function preExecute()
    {
        $this->timer('preExecute');
    }

    public function postExecute()
    {
        $this->timer('postExecute');
    }

    public function preCreateQuery()
    {
        $this->timer('preCreateResult');
    }

    public function postCreateQuery()
    {
        $this->timer('postCreateResult');
    }
}


htmlHeader();

// create a client instance and register the plugin
$plugin = new BasicDebug();
$client = new Solarium\Client($config);
$client->registerPlugin('debugger', $plugin);

// execute a select query and display the results
$query = $client->createSelect();
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

// display the debug plugin output
echo '<hr/><h1>Plugin output</h1>';
$plugin->display();

htmlFooter();
