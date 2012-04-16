<?php
require('init.php');

// this very simple plugin is used to show some events
class simpleDebug extends Solarium_Plugin_Abstract
{
    protected $_output = array();

    public function display()
    {
        echo implode('<br/>', $this->_output);
    }

    public function eventBufferedAddFlushStart($buffer) {
        $this->_output[] = 'Flushing buffer (' . count($buffer) . 'docs)';
    }
}

htmlHeader();

// create a client instance and autoload the buffered add plugin
$client = new Solarium_Client($config);
$buffer = $client->getPlugin('bufferedadd');
$buffer->setBufferSize(10); // this is quite low, in most cases you can use a much higher value

// also register a plugin for outputting events
$debug = new simpleDebug();
$client->registerPlugin('debugger', $debug);

// let's insert 25 docs
for ($i=1; $i<=25; $i++) {

    // create a new document with dummy data and add it to the buffer
    $data = array(
        'id' => 'test_'.$i,
        'name' => 'test for buffered add',
        'price' => $i,
    );
    $buffer->createDocument($data);

    // alternatively you could create document instances yourself and use the addDocument(s) method
}

// At this point two flushes will already have been done by the buffer automatically (at the 10th and 20th doc), now
// manually flush the remainder. Alternatively you can use the commit method if you want to include a commit command.
$buffer->flush();

// In total 3 flushes (requests) have been sent to Solr. This should be visible in this output:
$debug->display();

htmlFooter();