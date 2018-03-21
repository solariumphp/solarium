<?php

namespace Solarium\Core\Client;

use Solarium\Core\Client\Adapter\AdapterInterface;
use Solarium\Core\Configurable;
use Solarium\Core\Event\Events;
use Solarium\Core\Event\PostCreateQuery as PostCreateQueryEvent;
use Solarium\Core\Event\PostCreateRequest as PostCreateRequestEvent;
use Solarium\Core\Event\PostCreateResult as PostCreateResultEvent;
use Solarium\Core\Event\PostExecute as PostExecuteEvent;
use Solarium\Core\Event\PostExecuteRequest as PostExecuteRequestEvent;
use Solarium\Core\Event\PreCreateQuery as PreCreateQueryEvent;
use Solarium\Core\Event\PreCreateRequest as PreCreateRequestEvent;
use Solarium\Core\Event\PreCreateResult as PreCreateResultEvent;
use Solarium\Core\Event\PreExecute as PreExecuteEvent;
use Solarium\Core\Event\PreExecuteRequest as PreExecuteRequestEvent;
use Solarium\Core\Plugin\PluginInterface;
use Solarium\Core\Query\QueryInterface;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\OutOfBoundsException;
use Solarium\Exception\UnexpectedValueException;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Main interface for interaction with Solr.
 *
 * The client is the main interface for usage of the Solarium library.
 * You can use it to get query instances and to execute them.
 * It also allows to register plugins and querytypes to customize Solarium.
 * Finally, it also gives access to the adapter, which holds the Solr connection settings.
 *
 * Example usage with default settings:
 * <code>
 * $client = new Solarium\Client;
 * $query = $client->createSelect();
 * $result = $client->select($query);
 * </code>
 */
class Client extends Configurable implements ClientInterface
{
    /**
     * Querytype select.
     */
    const QUERY_SELECT = 'select';

    /**
     * Querytype update.
     */
    const QUERY_UPDATE = 'update';

    /**
     * Querytype ping.
     */
    const QUERY_PING = 'ping';

    /**
     * Querytype morelikethis.
     */
    const QUERY_MORELIKETHIS = 'mlt';

    /**
     * Querytype analysis field.
     */
    const QUERY_ANALYSIS_FIELD = 'analysis-field';

    /**
     * Querytype analysis document.
     */
    const QUERY_ANALYSIS_DOCUMENT = 'analysis-document';

    /**
     * Querytype terms.
     */
    const QUERY_TERMS = 'terms';

    /**
     * Querytype spellcheck.
     */
    const QUERY_SPELLCHECK = 'spell';

    /**
     * Querytype suggester.
     */
    const QUERY_SUGGESTER = 'suggester';

    /**
     * Querytype stream.
     */
    const QUERY_STREAM = 'stream';

    /**
     * Querytype graph.
     */
    const QUERY_GRAPH = 'graph';

    /**
     * Querytype extract.
     */
    const QUERY_EXTRACT = 'extract';

    /**
     * Querytype get.
     */
    const QUERY_REALTIME_GET = 'get';

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'adapter' => 'Solarium\Core\Client\Adapter\Curl',
        'endpoint' => [
            'localhost' => [],
        ],
    ];

    /**
     * Querytype mappings.
     *
     * These can be customized using {@link registerQueryType()}
     */
    protected $queryTypes = [
        self::QUERY_SELECT => 'Solarium\QueryType\Select\Query\Query',
        self::QUERY_UPDATE => 'Solarium\QueryType\Update\Query\Query',
        self::QUERY_PING => 'Solarium\QueryType\Ping\Query',
        self::QUERY_MORELIKETHIS => 'Solarium\QueryType\MoreLikeThis\Query',
        self::QUERY_ANALYSIS_DOCUMENT => 'Solarium\QueryType\Analysis\Query\Document',
        self::QUERY_ANALYSIS_FIELD => 'Solarium\QueryType\Analysis\Query\Field',
        self::QUERY_TERMS => 'Solarium\QueryType\Terms\Query',
        self::QUERY_SPELLCHECK => 'Solarium\QueryType\Spellcheck\Query',
        self::QUERY_SUGGESTER => 'Solarium\QueryType\Suggester\Query',
        self::QUERY_STREAM => 'Solarium\QueryType\Stream\Query',
        self::QUERY_GRAPH => 'Solarium\QueryType\Graph\Query',
        self::QUERY_EXTRACT => 'Solarium\QueryType\Extract\Query',
        self::QUERY_REALTIME_GET => 'Solarium\QueryType\RealtimeGet\Query',
    ];

    /**
     * Plugin types.
     *
     * @var array
     */
    protected $pluginTypes = [
        'loadbalancer' => 'Solarium\Plugin\Loadbalancer\Loadbalancer',
        'postbigrequest' => 'Solarium\Plugin\PostBigRequest',
        'customizerequest' => 'Solarium\Plugin\CustomizeRequest\CustomizeRequest',
        'parallelexecution' => 'Solarium\Plugin\ParallelExecution\ParallelExecution',
        'bufferedadd' => 'Solarium\Plugin\BufferedAdd\BufferedAdd',
        'prefetchiterator' => 'Solarium\Plugin\PrefetchIterator',
        'minimumscorefilter' => 'Solarium\Plugin\MinimumScoreFilter\MinimumScoreFilter',
    ];

    /**
     * EventDispatcher.
     *
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * Registered plugin instances.
     *
     * @var PluginInterface[]
     */
    protected $pluginInstances = [];

    /**
     * Registered endpoints.
     *
     * @var Endpoint[]
     */
    protected $endpoints = [];

    /**
     * Default endpoint key.
     *
     * @var string
     */
    protected $defaultEndpoint;

    /**
     * Adapter instance.
     *
     * If an adapter instance is set using {@link setAdapter()} this var will
     * contain a reference to that instance.
     *
     * In all other cases the adapter is lazy-loading, it will be instantiated
     * on first use by {@link getAdapter()} based on the 'adapter' entry in
     * {@link $options}. This option can be set using {@link setAdapter()}
     *
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * Constructor.
     *
     * If options are passed they will be merged with {@link $options} using
     * the {@link setOptions()} method.
     *
     * If an EventDispatcher instance is provided this will be used instead of creating a new instance
     *
     * @param array|\Zend_Config $options
     * @param EventDispatcher    $eventDispatcher
     */
    public function __construct($options = null, $eventDispatcher = null)
    {
        $this->eventDispatcher = $eventDispatcher;
        parent::__construct($options);
    }

    /**
     * Create a endpoint instance.
     *
     * If you supply a string as the first arguments ($options) it will be used as the key for the endpoint
     * and it will be registered.
     * If you supply an options array/object that contains a key the endpoint will also be registered.
     *
     * When no key is supplied the endpoint cannot be registered, in that case you will need to do this manually
     * after setting the key, by using the addEndpoint method.
     *
     * @param mixed $options
     * @param bool  $setAsDefault
     *
     * @return Endpoint
     */
    public function createEndpoint($options = null, $setAsDefault = false)
    {
        if (is_string($options)) {
            $endpoint = new Endpoint();
            $endpoint->setKey($options);
        } else {
            $endpoint = new Endpoint($options);
        }

        if (null !== $endpoint->getKey()) {
            $this->addEndpoint($endpoint);
            if (true === $setAsDefault) {
                $this->setDefaultEndpoint($endpoint);
            }
        }

        return $endpoint;
    }

    /**
     * Add an endpoint.
     *
     * Supports a endpoint instance or a config array as input.
     * In case of options a new endpoint instance wil be created based on the options.
     *
     *
     * @param Endpoint|array $endpoint
     *
     * @throws InvalidArgumentException
     *
     * @return self Provides fluent interface
     */
    public function addEndpoint($endpoint)
    {
        if (is_array($endpoint)) {
            $endpoint = new Endpoint($endpoint);
        }

        $key = $endpoint->getKey();

        if (0 === strlen($key)) {
            throw new InvalidArgumentException('An endpoint must have a key value');
        }

        //double add calls for the same endpoint are ignored, but non-unique keys cause an exception
        if (array_key_exists($key, $this->endpoints) && $this->endpoints[$key] !== $endpoint) {
            throw new InvalidArgumentException('An endpoint must have a unique key');
        }

        $this->endpoints[$key] = $endpoint;

        // if no default endpoint is set do so now
        if (null === $this->defaultEndpoint) {
            $this->defaultEndpoint = $key;
        }

        return $this;
    }

    /**
     * Add multiple endpoints.
     *
     * @param array $endpoints
     *
     * @return self Provides fluent interface
     */
    public function addEndpoints(array $endpoints)
    {
        foreach ($endpoints as $key => $endpoint) {
            // in case of a config array: add key to config
            if (is_array($endpoint) && !isset($endpoint['key'])) {
                $endpoint['key'] = $key;
            }

            $this->addEndpoint($endpoint);
        }

        return $this;
    }

    /**
     * Get an endpoint by key.
     *
     *
     * @param string $key
     *
     * @throws OutOfBoundsException
     *
     * @return Endpoint
     */
    public function getEndpoint($key = null)
    {
        if (null === $key) {
            $key = $this->defaultEndpoint;
        }

        if (!isset($this->endpoints[$key])) {
            throw new OutOfBoundsException('Endpoint '.$key.' not available');
        }

        return $this->endpoints[$key];
    }

    /**
     * Get all endpoints.
     *
     * @return Endpoint[]
     */
    public function getEndpoints()
    {
        return $this->endpoints;
    }

    /**
     * Remove a single endpoint.
     *
     * You can remove a endpoint by passing it's key, or by passing the endpoint instance
     *
     * @param string|Endpoint $endpoint
     *
     * @return self Provides fluent interface
     */
    public function removeEndpoint($endpoint)
    {
        if (is_object($endpoint)) {
            $endpoint = $endpoint->getKey();
        }

        if (isset($this->endpoints[$endpoint])) {
            unset($this->endpoints[$endpoint]);
        }

        return $this;
    }

    /**
     * Remove all endpoints.
     *
     * @return self Provides fluent interface
     */
    public function clearEndpoints()
    {
        $this->endpoints = [];
        $this->defaultEndpoint = null;

        return $this;
    }

    /**
     * Set multiple endpoints.
     *
     * This overwrites any existing endpoints
     *
     * @param array $endpoints
     */
    public function setEndpoints($endpoints)
    {
        $this->clearEndpoints();
        $this->addEndpoints($endpoints);
    }

    /**
     * Set a default endpoint.
     *
     * All queries executed without a specific endpoint will use this default endpoint.
     *
     * @param string|Endpoint $endpoint
     *
     * @throws OutOfBoundsException
     *
     * @return self Provides fluent interface
     */
    public function setDefaultEndpoint($endpoint)
    {
        if (is_object($endpoint)) {
            $endpoint = $endpoint->getKey();
        }

        if (!isset($this->endpoints[$endpoint])) {
            throw new OutOfBoundsException('Unknown endpoint '.$endpoint.' cannot be set as default');
        }

        $this->defaultEndpoint = $endpoint;

        return $this;
    }

    /**
     * Set the adapter.
     *
     * The adapter has to be a class that implements the AdapterInterface
     *
     * If a string is passed it is assumed to be the classname and it will be
     * instantiated on first use. This requires the availability of the class
     * through autoloading or a manual require before calling this method.
     * Any existing adapter instance will be removed by this method, this way an
     * instance of the new adapter type will be created upon the next usage of
     * the adapter (lazy-loading)
     *
     * If an adapter instance is passed it will replace the current adapter
     * immediately, bypassing the lazy loading.
     *
     *
     * @param string|Adapter\AdapterInterface $adapter
     *
     * @throws InvalidArgumentException
     *
     * @return self Provides fluent interface
     */
    public function setAdapter($adapter)
    {
        if (is_string($adapter)) {
            $this->adapter = null;

            return $this->setOption('adapter', $adapter);
        } elseif ($adapter instanceof AdapterInterface) {
            // forward options
            $adapter->setOptions($this->getOption('adapteroptions'));
            // overwrite existing adapter
            $this->adapter = $adapter;

            return $this;
        }

        throw new InvalidArgumentException('Invalid adapter input for setAdapter');
    }

    /**
     * Get the adapter instance.
     *
     * If {@see $adapter} doesn't hold an instance a new one will be created by
     * calling {@see createAdapter()}
     *
     * @param bool $autoload
     *
     * @return AdapterInterface
     */
    public function getAdapter($autoload = true)
    {
        if (null === $this->adapter && $autoload) {
            $this->createAdapter();
        }

        return $this->adapter;
    }

    /**
     * Register a querytype.
     *
     * You can also use this method to override any existing querytype with a new mapping.
     * This requires the availability of the classes through autoloading or a manual
     * require before calling this method.
     *
     * @param string $type
     * @param string $queryClass
     *
     * @return self Provides fluent interface
     */
    public function registerQueryType($type, $queryClass)
    {
        $this->queryTypes[$type] = $queryClass;

        return $this;
    }

    /**
     * Register multiple querytypes.
     *
     * @param array $queryTypes
     *
     * @return self Provides fluent interface
     */
    public function registerQueryTypes($queryTypes)
    {
        foreach ($queryTypes as $type => $class) {
            // support both "key=>value" and "(no-key) => array(key=>x,query=>y)" formats
            if (is_array($class)) {
                if (isset($class['type'])) {
                    $type = $class['type'];
                }
                $class = $class['query'];
            }

            $this->registerQueryType($type, $class);
        }
    }

    /**
     * Get all registered querytypes.
     *
     * @return array
     */
    public function getQueryTypes()
    {
        return $this->queryTypes;
    }

    /**
     * Gets the event dispatcher.
     *
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * Sets the event dispatcher.
     *
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return $this
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * Register a plugin.
     *
     * You can supply a plugin instance or a plugin classname as string.
     * This requires the availability of the class through autoloading
     * or a manual require.
     *
     *
     * @param string                 $key
     * @param string|PluginInterface $plugin
     * @param array                  $options
     *
     * @throws InvalidArgumentException
     *
     * @return self Provides fluent interface
     */
    public function registerPlugin($key, $plugin, $options = [])
    {
        if (is_string($plugin)) {
            $plugin = class_exists($plugin) ? $plugin : $plugin.strrchr($plugin, '\\');
            $plugin = new $plugin();
        }

        if (!($plugin instanceof PluginInterface)) {
            throw new InvalidArgumentException('All plugins must implement the PluginInterface');
        }

        $plugin->initPlugin($this, $options);

        $this->pluginInstances[$key] = $plugin;

        return $this;
    }

    /**
     * Register multiple plugins.
     *
     * @param array $plugins
     *
     * @return self Provides fluent interface
     */
    public function registerPlugins($plugins)
    {
        foreach ($plugins as $key => $plugin) {
            if (!isset($plugin['key'])) {
                $plugin['key'] = $key;
            }

            $this->registerPlugin(
                $plugin['key'],
                $plugin['plugin'],
                $plugin['options']
            );
        }

        return $this;
    }

    /**
     * Get all registered plugins.
     *
     * @return PluginInterface[]
     */
    public function getPlugins()
    {
        return $this->pluginInstances;
    }

    /**
     * Get a plugin instance.
     *
     *
     * @param string $key
     * @param bool   $autocreate
     *
     * @throws OutOfBoundsException
     *
     * @return PluginInterface|null
     */
    public function getPlugin($key, $autocreate = true)
    {
        if (isset($this->pluginInstances[$key])) {
            return $this->pluginInstances[$key];
        } elseif ($autocreate) {
            if (array_key_exists($key, $this->pluginTypes)) {
                $this->registerPlugin($key, $this->pluginTypes[$key]);

                return $this->pluginInstances[$key];
            }

            throw new OutOfBoundsException('Cannot autoload plugin of unknown type: '.$key);
        }
    }

    /**
     * Remove a plugin instance.
     *
     * You can remove a plugin by passing the plugin key, or the plugin instance
     *
     * @param string|PluginInterface $plugin
     *
     * @return self Provides fluent interface
     */
    public function removePlugin($plugin)
    {
        if (is_object($plugin)) {
            foreach ($this->pluginInstances as $key => $instance) {
                if ($instance === $plugin) {
                    unset($this->pluginInstances[$key]);
                    break;
                }
            }
        } else {
            if (isset($this->pluginInstances[$plugin])) {
                unset($this->pluginInstances[$plugin]);
            }
        }

        return $this;
    }

    /**
     * Creates a request based on a query instance.
     *
     *
     * @param QueryInterface $query
     *
     * @throws UnexpectedValueException
     *
     * @return Request
     */
    public function createRequest(QueryInterface $query)
    {
        $event = new PreCreateRequestEvent($query);
        $this->eventDispatcher->dispatch(Events::PRE_CREATE_REQUEST, $event);
        if (null !== $event->getRequest()) {
            return $event->getRequest();
        }

        $requestBuilder = $query->getRequestBuilder();
        if (!$requestBuilder || !($requestBuilder instanceof RequestBuilderInterface)) {
            throw new UnexpectedValueException('No requestbuilder returned by querytype: '.$query->getType());
        }

        $request = $requestBuilder->build($query);

        $this->eventDispatcher->dispatch(
            Events::POST_CREATE_REQUEST,
            new PostCreateRequestEvent($query, $request)
        );

        return $request;
    }

    /**
     * Creates a result object.
     *
     *
     * @param QueryInterface $query
     * @param array Response $response
     *
     * @throws UnexpectedValueException;
     *
     * @return ResultInterface
     */
    public function createResult(QueryInterface $query, $response)
    {
        $event = new PreCreateResultEvent($query, $response);
        $this->eventDispatcher->dispatch(Events::PRE_CREATE_RESULT, $event);
        if (null !== $event->getResult()) {
            return $event->getResult();
        }

        $resultClass = $query->getResultClass();
        $result = new $resultClass($query, $response);

        if (!($result instanceof ResultInterface)) {
            throw new UnexpectedValueException('Result class must implement the ResultInterface');
        }

        $this->eventDispatcher->dispatch(
            Events::POST_CREATE_RESULT,
            new PostCreateResultEvent($query, $response, $result)
        );

        return $result;
    }

    /**
     * Execute a query.
     *
     * @param QueryInterface       $query
     * @param Endpoint|string|null $endpoint
     *
     * @return ResultInterface
     */
    public function execute(QueryInterface $query, $endpoint = null)
    {
        $event = new PreExecuteEvent($query);
        $this->eventDispatcher->dispatch(Events::PRE_EXECUTE, $event);
        if (null !== $event->getResult()) {
            return $event->getResult();
        }

        $request = $this->createRequest($query);
        $response = $this->executeRequest($request, $endpoint);
        $result = $this->createResult($query, $response);

        $this->eventDispatcher->dispatch(
            Events::POST_EXECUTE,
            new PostExecuteEvent($query, $result)
        );

        return $result;
    }

    /**
     * Execute a request and return the response.
     *
     * @param Request              $request
     * @param Endpoint|string|null $endpoint
     *
     * @return Response
     */
    public function executeRequest($request, $endpoint = null)
    {
        // load endpoint by string or by using the default one in case of a null value
        if (!($endpoint instanceof Endpoint)) {
            $endpoint = $this->getEndpoint($endpoint);
        }

        $event = new PreExecuteRequestEvent($request, $endpoint);
        $this->eventDispatcher->dispatch(Events::PRE_EXECUTE_REQUEST, $event);
        if (null !== $event->getResponse()) {
            $response = $event->getResponse(); //a plugin result overrules the standard execution result
        } else {
            $response = $this->getAdapter()->execute($request, $endpoint);
        }

        $this->eventDispatcher->dispatch(
            Events::POST_EXECUTE_REQUEST,
            new PostExecuteRequestEvent($request, $endpoint, $response)
        );

        return $response;
    }

    /**
     * Execute a ping query.
     *
     * This is a convenience method that forwards the query to the
     * execute method, thus allowing for an easy to use and clean API.
     *
     * Example usage:
     * <code>
     * $client = new Solarium\Client;
     * $query = $client->createPing();
     * $result = $client->ping($query);
     * </code>
     *
     * @see Solarium\QueryType\Ping
     *
     * @param QueryInterface|\Solarium\QueryType\Ping\Query $query
     * @param Endpoint|string|null                          $endpoint
     *
     * @return \Solarium\QueryType\Ping\Result
     */
    public function ping(QueryInterface $query, $endpoint = null)
    {
        return $this->execute($query, $endpoint);
    }

    /**
     * Execute an update query.
     *
     * This is a convenience method that forwards the query to the
     * execute method, thus allowing for an easy to use and clean API.
     *
     * Example usage:
     * <code>
     * $client = new Solarium\Client;
     * $query = $client->createUpdate();
     * $update->addOptimize();
     * $result = $client->update($update);
     * </code>
     *
     * @see Solarium\QueryType\Update
     * @see Solarium\Result\Update
     *
     * @param QueryInterface|\Solarium\QueryType\Update\Query\Query $query
     * @param Endpoint|string|null                                  $endpoint
     *
     * @return \Solarium\QueryType\Update\Result
     */
    public function update(QueryInterface $query, $endpoint = null)
    {
        return $this->execute($query, $endpoint);
    }

    /**
     * Execute a select query.
     *
     * This is a convenience method that forwards the query to the
     * execute method, thus allowing for an easy to use and clean API.
     *
     * Example usage:
     * <code>
     * $client = new Solarium\Client;
     * $query = $client->createSelect();
     * $result = $client->select($query);
     * </code>
     *
     * @see Solarium\QueryType\Select
     * @see Solarium\Result\Select
     *
     * @param QueryInterface|\Solarium\QueryType\Select\Query\Query $query
     * @param Endpoint|string|null                                  $endpoint
     *
     * @return \Solarium\QueryType\Select\Result\Result
     */
    public function select(QueryInterface $query, $endpoint = null)
    {
        return $this->execute($query, $endpoint);
    }

    /**
     * Execute a MoreLikeThis query.
     *
     * This is a convenience method that forwards the query to the
     * execute method, thus allowing for an easy to use and clean API.
     *
     * Example usage:
     * <code>
     * $client = new Solarium\Client;
     * $query = $client->createMoreLikeThis();
     * $result = $client->moreLikeThis($query);
     * </code>
     *
     * @see Solarium\QueryType\MoreLikeThis
     * @see Solarium\Result\MoreLikeThis
     *
     * @param QueryInterface|\Solarium\QueryType\MoreLikeThis\Query $query
     * @param Endpoint|string|null                                  $endpoint
     *
     * @return \Solarium\QueryType\MoreLikeThis\Result
     */
    public function moreLikeThis(QueryInterface $query, $endpoint = null)
    {
        return $this->execute($query, $endpoint);
    }

    /**
     * Execute an analysis query.
     *
     * This is a convenience method that forwards the query to the
     * execute method, thus allowing for an easy to use and clean API.
     *
     * @param QueryInterface|\Solarium\QueryType\Analysis\Query\Document|\Solarium\QueryType\Analysis\Query\Field $query
     * @param Endpoint|string|null                                                                                $endpoint
     *
     * @return \Solarium\QueryType\Analysis\Result\Document|\Solarium\QueryType\Analysis\Result\Field
     */
    public function analyze(QueryInterface $query, $endpoint = null)
    {
        return $this->execute($query, $endpoint);
    }

    /**
     * Execute a terms query.
     *
     * This is a convenience method that forwards the query to the
     * execute method, thus allowing for an easy to use and clean API.
     *
     * @param QueryInterface|\Solarium\QueryType\Terms\Query $query
     * @param Endpoint|string|null                           $endpoint
     *
     * @return \Solarium\QueryType\Terms\Result
     */
    public function terms(QueryInterface $query, $endpoint = null)
    {
        return $this->execute($query, $endpoint);
    }

    /**
     * Execute a spellcheck query.
     *
     * This is a convenience method that forwards the query to the
     * execute method, thus allowing for an easy to use and clean API.
     *
     * @param QueryInterface|\Solarium\QueryType\Spellcheck\Query $query
     * @param Endpoint|string|null                                $endpoint
     *
     * @return \Solarium\QueryType\Spellcheck\Result\Result
     */
    public function spellcheck(QueryInterface $query, $endpoint = null)
    {
        return $this->execute($query, $endpoint);
    }

    /**
     * Execute a suggester query.
     *
     * This is a convenience method that forwards the query to the
     * execute method, thus allowing for an easy to use and clean API.
     *
     * @param QueryInterface|\Solarium\QueryType\Suggester\Query $query
     * @param Endpoint|string|null                               $endpoint
     *
     * @return \Solarium\QueryType\Suggester\Result\Result
     */
    public function suggester(QueryInterface $query, $endpoint = null)
    {
        return $this->execute($query, $endpoint);
    }

    /**
     * Execute an extract query.
     *
     * This is a convenience method that forwards the query to the
     * execute method, thus allowing for an easy to use and clean API.
     *
     * @param QueryInterface|\Solarium\QueryType\Extract\Query $query
     * @param Endpoint|string|null                             $endpoint
     *
     * @return \Solarium\QueryType\Extract\Result
     */
    public function extract(QueryInterface $query, $endpoint = null)
    {
        return $this->execute($query, $endpoint);
    }

    /**
     * Execute a RealtimeGet query.
     *
     * This is a convenience method that forwards the query to the
     * execute method, thus allowing for an easy to use and clean API.
     *
     * @param QueryInterface|\Solarium\QueryType\RealtimeGet\Query $query
     * @param Endpoint|string|null                                 $endpoint
     *
     * @return \Solarium\QueryType\RealtimeGet\Result
     */
    public function realtimeGet(QueryInterface $query, $endpoint = null)
    {
        return $this->execute($query, $endpoint);
    }

    /**
     * Create a query instance.
     *
     *
     * @param string $type
     * @param array  $options
     *
     * @throws InvalidArgumentException|UnexpectedValueException
     *
     * @return \Solarium\Core\Query\AbstractQuery
     */
    public function createQuery($type, $options = null)
    {
        $type = strtolower($type);

        $event = new PreCreateQueryEvent($type, $options);
        $this->eventDispatcher->dispatch(Events::PRE_CREATE_QUERY, $event);
        if (null !== $event->getQuery()) {
            return $event->getQuery();
        }

        if (!isset($this->queryTypes[$type])) {
            throw new InvalidArgumentException('Unknown querytype: '.$type);
        }

        $class = $this->queryTypes[$type];
        $query = new $class($options);

        if (!($query instanceof QueryInterface)) {
            throw new UnexpectedValueException('All query classes must implement the QueryInterface');
        }

        $this->eventDispatcher->dispatch(
            Events::POST_CREATE_QUERY,
            new PostCreateQueryEvent($type, $options, $query)
        );

        return $query;
    }

    /**
     * Create a select query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\Select\Query\Query
     */
    public function createSelect($options = null)
    {
        return $this->createQuery(self::QUERY_SELECT, $options);
    }

    /**
     * Create a MoreLikeThis query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\MorelikeThis\Query
     */
    public function createMoreLikeThis($options = null)
    {
        return $this->createQuery(self::QUERY_MORELIKETHIS, $options);
    }

    /**
     * Create an update query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\Update\Query\Query
     */
    public function createUpdate($options = null)
    {
        return $this->createQuery(self::QUERY_UPDATE, $options);
    }

    /**
     * Create a ping query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\Ping\Query
     */
    public function createPing($options = null)
    {
        return $this->createQuery(self::QUERY_PING, $options);
    }

    /**
     * Create an analysis field query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\Analysis\Query\Field
     */
    public function createAnalysisField($options = null)
    {
        return $this->createQuery(self::QUERY_ANALYSIS_FIELD, $options);
    }

    /**
     * Create an analysis document query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\Analysis\Query\Document
     */
    public function createAnalysisDocument($options = null)
    {
        return $this->createQuery(self::QUERY_ANALYSIS_DOCUMENT, $options);
    }

    /**
     * Create a terms query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\Terms\Query
     */
    public function createTerms($options = null)
    {
        return $this->createQuery(self::QUERY_TERMS, $options);
    }

    /**
     * Create a specllcheck query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\Spellcheck\Query
     */
    public function createSpellcheck($options = null)
    {
        return $this->createQuery(self::QUERY_SPELLCHECK, $options);
    }

    /**
     * Create a suggester query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\Suggester\Query
     */
    public function createSuggester($options = null)
    {
        return $this->createQuery(self::QUERY_SUGGESTER, $options);
    }

    /**
     * Create an extract query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\Extract\Query
     */
    public function createExtract($options = null)
    {
        return $this->createQuery(self::QUERY_EXTRACT, $options);
    }

    /**
     * Create a stream query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\Stream\Query
     */
    public function createStream($options = null)
    {
        // Streaming expressions tend to be very long. Therfore we use the 'postbigrequest' plugin. The plugin needs to
        // be loaded before the request is created.
        $this->getPlugin('postbigrequest');

        return $this->createQuery(self::QUERY_STREAM, $options);
    }

    /**
     * Create a graph query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\Graph\Query
     */
    public function createGraph($options = null)
    {
        // Streaming expressions tend to be very long. Therfore we use the 'postbigrequest' plugin. The plugin needs to
        // be loaded before the request is created.
        $this->getPlugin('postbigrequest');

        return $this->createQuery(self::QUERY_GRAPH, $options);
    }

    /**
     * Create a RealtimeGet query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\RealtimeGet\Query
     */
    public function createRealtimeGet($options = null)
    {
        return $this->createQuery(self::QUERY_REALTIME_GET, $options);
    }

    /**
     * Initialization hook.
     */
    protected function init()
    {
        if (null === $this->eventDispatcher) {
            $this->eventDispatcher = new EventDispatcher();
        }

        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'endpoint':
                    $this->setEndpoints($value);
                    break;
                case 'querytype':
                    $this->registerQueryTypes($value);
                    break;
                case 'plugin':
                    $this->registerPlugins($value);
                    break;
            }
        }
    }

    /**
     * Create an adapter instance.
     *
     * The 'adapter' entry in {@link $options} will be used to create an
     * adapter instance. This entry can be the default value of
     * {@link $options}, a value passed to the constructor or a value set by
     * using {@link setAdapter()}
     *
     * This method is used for lazy-loading the adapter upon first use in
     * {@link getAdapter()}
     *
     * @throws InvalidArgumentException
     */
    protected function createAdapter()
    {
        $adapterClass = $this->getOption('adapter');
        $adapter = new $adapterClass();

        // check interface
        if (!($adapter instanceof AdapterInterface)) {
            throw new InvalidArgumentException('An adapter must implement the AdapterInterface');
        }

        $adapter->setOptions($this->getOption('adapteroptions'));
        $this->adapter = $adapter;
    }
}
