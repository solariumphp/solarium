<?php

require_once(__DIR__.'/init.php');

use Solarium\Plugin\BufferedDelete\Event\Events;
use Solarium\Plugin\BufferedDelete\Event\PreFlush as PreFlushEvent;
use Solarium\QueryType\Update\Query\Query;

htmlHeader();

// create a client instance and autoload the buffered delete plugin
$client = new Solarium\Client($adapter, $eventDispatcher, $config);
$buffer = $client->getPlugin('buffereddelete'); // or 'buffereddeletelite'
$buffer->setBufferSize(10); // this is quite low, in most cases you can use a much higher value

// also register an event hook to display what is happening
// this only works with 'buffereddelete', 'buffereddeletelite' doesn't trigger events
$client->getEventDispatcher()->addListener(
    Events::PRE_FLUSH,
    function (PreFlushEvent $event) {
        echo 'Flushing buffer (' . count($event->getBuffer()) . ' deletes)<br/>';
    }
);

// let's delete 25 docs
for ($i=1; $i<=25; $i++) {
    $buffer->addDeleteById($i);
}

// you can also delete documents matching a query
$buffer->addDeleteQuery('cat:discontinued');
$buffer->addDeleteQuery('manu_id_s:acme');

// At this point two flushes will already have been done by the buffer automatically (at the 10th and 20th delete), now
// manually flush the remainder. Alternatively you can use the commit method if you want to include a commit command.
$buffer->flush();

// In total 3 flushes (requests) have been sent to Solr. This should be visible in the output of the event hook.

htmlFooter();
