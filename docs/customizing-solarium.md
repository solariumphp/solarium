Customizing Solarium
====================

Solarium was designed to be highly customizable, in the following ways:

1.  by allowing for partial usage, for instance only convert a query into a request object but handle the communication in your own code
2.  by adding an event dispatcher for plugins

What method of customization to use depends on your case:

-   if you want to use only some parts of Solarium, but not modify it, go with method 1
-   if you want to customize Solarium by altering/adding behaviour use the plugin structure. This has many advantages over extending:
    -   Plugins are easily reusable
    -   You can combine multiple plugins
    -   By leaving your Solarium library stock you can add plugins developed by others without issues
    -   Plugins can easily be disabled, for instance in case of issues or for debugging
    -   No issues with upgrading Solarium
    -   A very basic plugin could even be implemented using a closure, so plugins are not hard to use!


Partial usage
-------------

Normally you execute your query using the corresponding method in the client class. For instance for a select query you use the select($query) method. This methods map to the execute method, which executes the steps needed to get the query response. It is also possible to call these methods yourself. This way you can execute only a part of the query flow, or alter the data in between calls (but in that case a plugin might be a better choice).

The example code shows how to do this. It also shows how to generate the URI for a query string, something that's requested a lot. To show all methods the example continues after getting the URI, but at this point you could of course also switch to some custom code.

```php
<?php

require_once(__DIR__.'/init.php');
htmlHeader();

// This example shows how to manually execute the query flow.
// By doing this manually you can customize data in between any step (although a plugin might be better for this)
// And you can use only a part of the flow. You could for instance use the query object and request builder,
// but execute the request in your own code.


// create a client instance
$client = new Solarium\Client($adapter, $eventDispatcher, $config);

// create a select query instance
$query = $client->createSelect();

// manually create a request for the query
$request = $client->createRequest($query);

// you can now use the request object for getting an uri (e.g. to use in your own code)
// or you could modify the request object
echo 'Request URI: ' . $request->getUri() . '<br/>';

// you can still execute the request using the client and get a 'raw' response object
$response = $client->executeRequest($request);

// and finally you can convert the response into a result
$result = $client->createResult($query, $response);

// display the total number of documents found by Solr
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

```


Plugin system
-------------

The Solarium plugin has several features:

-   custom plugin code can be executed for events
-   events give access to relevant vars, for reading but also to optionally modify them
-   plugins have access to the Solarium client instance they belong to, so you can also modify Solarium settings and class mappings in a plugin

By combining these options you can achieve almost any type of customization with a plugin.

Solarium can use any PSR-14 compatible event dispatcher for events. The included examples use the [Symfony EventDispatcher](https://symfony.com/doc/current/components/event_dispatcher.html).

This example shows all available events and how to use the events to create a very basic debugger:

```php
<?php

require_once(__DIR__.'/init.php');
use Solarium\Core\Event\Events;

// this very simple plugin shows a timing for each event and display some request debug info
class BasicDebug extends Solarium\Core\Plugin\AbstractPlugin
{
    protected $start;
    protected $output = array();

    // This method is called when the plugin is registered with the client.
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

    // This method is called if the plugin is removed from the client.
    public function deinitPlugin()
    {
        $dispatcher = $this->client->getEventDispatcher();
        $dispatcher->removeListener(Events::PRE_CREATE_REQUEST, array($this, 'preCreateRequest'));
        $dispatcher->removeListener(Events::POST_CREATE_REQUEST, array($this, 'postCreateRequest'));
        $dispatcher->removeListener(Events::PRE_EXECUTE_REQUEST, array($this, 'preExecuteRequest'));
        $dispatcher->removeListener(Events::POST_EXECUTE_REQUEST, array($this, 'postExecuteRequest'));
        $dispatcher->removeListener(Events::PRE_CREATE_RESULT, array($this, 'preCreateResult'));
        $dispatcher->removeListener(Events::POST_CREATE_RESULT, array($this, 'postCreateResult'));
        $dispatcher->removeListener(Events::PRE_EXECUTE, array($this, 'preExecute'));
        $dispatcher->removeListener(Events::POST_EXECUTE, array($this, 'postExecute'));
        $dispatcher->removeListener(Events::PRE_CREATE_QUERY, array($this, 'preCreateQuery'));
        $dispatcher->removeListener(Events::POST_CREATE_QUERY, array($this, 'postCreateQuery'));
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

    // This method uses the available param(s) (see plugin abstract class).
    // You can access or modify data this way.
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
$client = new Solarium\Client($adapter, $eventDispatcher, $config);
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

```

The second example shows how to replace the built-in select querytype with a custom implementation: 
```php
<?php

require_once(__DIR__.'/init.php');
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
$client = new Client($adapter, $eventDispatcher, $config);
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

```

### The order of plugin executions

Since plugins leverage events, the event dispatcher is responsible for the order they get called if two plugins register
for the same event. In some cases that doesn't matter. In other cases it is essential that Plugin A acts before Plugin B.
If the order matters you need to read the documentation of the event dispatcher of choice that you inject into the
Solarium Client's constructor.

For various events `CustomizeRequest` needs to be executed before `PostBigRequest`. And `Loadbalancer` should always be
the last plugin.

If you use the Symfony EventDispatcher, Solarium takes care of the order of these critical plugins and the events they
listen to. This is done by setting the "priority".
