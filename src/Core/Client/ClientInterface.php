<?php

namespace Solarium\Core\Client;

use Solarium\Core\Client\Adapter\AdapterInterface;
use Solarium\Core\Plugin\PluginInterface;
use Solarium\Core\Query\QueryInterface;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\OutOfBoundsException;
use Solarium\Exception\UnexpectedValueException;
use Solarium\QueryType\Analysis\Query\Document as AnalysisQueryDocument;
use Solarium\QueryType\Analysis\Query\Field as AnalysisQueryField;
use Solarium\QueryType\Analysis\Result\Document as AnalysisResultDocument;
use Solarium\QueryType\Analysis\Result\Field as AnalysisResultField;
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
interface ClientInterface
{
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
    public function createEndpoint($options = null, $setAsDefault = false);

    /**
     * Add an endpoint.
     *
     * Supports a endpoint instance or a config array as input.
     * In case of options a new endpoint instance wil be created based on the options.
     *
     * @param Endpoint|array $endpoint
     *
     * @throws InvalidArgumentException
     *
     * @return self Provides fluent interface
     */
    public function addEndpoint($endpoint);

    /**
     * Add multiple endpoints.
     *
     * @param array $endpoints
     *
     * @return self Provides fluent interface
     */
    public function addEndpoints(array $endpoints);

    /**
     * Get an endpoint by key.
     *
     * @param string $key
     *
     * @throws OutOfBoundsException
     *
     * @return Endpoint
     */
    public function getEndpoint($key = null);

    /**
     * Get all endpoints.
     *
     * @return Endpoint[]
     */
    public function getEndpoints();

    /**
     * Remove a single endpoint.
     *
     * You can remove a endpoint by passing it's key, or by passing the endpoint instance
     *
     * @param string|Endpoint $endpoint
     *
     * @return self Provides fluent interface
     */
    public function removeEndpoint($endpoint);

    /**
     * Remove all endpoints.
     *
     * @return self Provides fluent interface
     */
    public function clearEndpoints();

    /**
     * Set multiple endpoints.
     *
     * This overwrites any existing endpoints
     *
     * @param array $endpoints
     */
    public function setEndpoints($endpoints);

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
    public function setDefaultEndpoint($endpoint);

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
     * @param string|Adapter\AdapterInterface $adapter
     *
     * @throws InvalidArgumentException
     *
     * @return self Provides fluent interface
     */
    public function setAdapter($adapter);

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
    public function getAdapter($autoload = true);

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
    public function registerQueryType($type, $queryClass);

    /**
     * Register multiple querytypes.
     *
     * @param array $queryTypes
     *
     * @return self Provides fluent interface
     */
    public function registerQueryTypes($queryTypes);

    /**
     * Get all registered querytypes.
     *
     * @return array
     */
    public function getQueryTypes();

    /**
     * Gets the event dispatcher.
     *
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher();

    /**
     * Sets the event dispatcher.
     *
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return self Provides fluent interface
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher);

    /**
     * Register a plugin.
     *
     * You can supply a plugin instance or a plugin classname as string.
     * This requires the availability of the class through autoloading
     * or a manual require.
     *
     * @param string                 $key
     * @param string|PluginInterface $plugin
     * @param array                  $options
     *
     * @throws InvalidArgumentException
     *
     * @return self Provides fluent interface
     */
    public function registerPlugin($key, $plugin, $options = []);

    /**
     * Register multiple plugins.
     *
     * @param array $plugins
     *
     * @return self Provides fluent interface
     */
    public function registerPlugins($plugins);

    /**
     * Get all registered plugins.
     *
     * @return PluginInterface[]
     */
    public function getPlugins();

    /**
     * Get a plugin instance.
     *
     * @param string $key
     * @param bool   $autocreate
     *
     * @throws OutOfBoundsException
     *
     * @return PluginInterface|null
     */
    public function getPlugin($key, $autocreate = true);

    /**
     * Remove a plugin instance.
     *
     * You can remove a plugin by passing the plugin key, or the plugin instance
     *
     * @param string|PluginInterface $plugin
     *
     * @return self Provides fluent interface
     */
    public function removePlugin($plugin);

    /**
     * Creates a request based on a query instance.
     *
     * @param QueryInterface $query
     *
     * @throws UnexpectedValueException
     *
     * @return Request
     */
    public function createRequest(QueryInterface $query);

    /**
     * Creates a result object.
     *
     * @param QueryInterface $query
     * @param array Response $response
     *
     * @throws UnexpectedValueException;
     *
     * @return ResultInterface
     */
    public function createResult(QueryInterface $query, $response);

    /**
     * Execute a query.
     *
     * @param QueryInterface       $query
     * @param Endpoint|string|null $endpoint
     *
     * @return ResultInterface
     */
    public function execute(QueryInterface $query, $endpoint = null);

    /**
     * Execute a request and return the response.
     *
     * @param Request
     * @param Endpoint|string|null
     * @param mixed      $request
     * @param null|mixed $endpoint
     *
     * @return Response
     */
    public function executeRequest($request, $endpoint = null);

    /**
     * Execute a ping query.
     *
     * Example usage:
     * <code>
     * $client = new Solarium\Client;
     * $query = $client->createPing();
     * $result = $client->ping($query);
     * </code>
     *
     * @see \Solarium\QueryType\Ping\Query
     *
     * @internal this is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API
     *
     * @param QueryInterface|\Solarium\QueryType\Ping\Query $query
     * @param Endpoint|string|null                          $endpoint
     *
     * @return \Solarium\QueryType\Ping\Result
     */
    public function ping(QueryInterface $query, $endpoint = null);

    /**
     * Execute an update query.
     *
     * Example usage:
     * <code>
     * $client = new Solarium\Client;
     * $query = $client->createUpdate();
     * $update->addOptimize();
     * $result = $client->update($update);
     * </code>
     *
     * @see \Solarium\QueryType\Update\Query\Query
     * @see \Solarium\QueryType\Update\Result
     *
     * @internal this is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API
     *
     * @param QueryInterface|\Solarium\QueryType\Update\Query\Query $query
     * @param Endpoint|string|null                                  $endpoint
     *
     * @return \Solarium\QueryType\Update\Result
     */
    public function update(QueryInterface $query, $endpoint = null);

    /**
     * Execute a select query.
     *
     * Example usage:
     * <code>
     * $client = new Solarium\Client;
     * $query = $client->createSelect();
     * $result = $client->select($query);
     * </code>
     *
     * @see \Solarium\QueryType\Select\Query\Query
     * @see \Solarium\QueryType\Select\Result\Result
     *
     * @internal this is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API
     *
     * @param QueryInterface|\Solarium\QueryType\Select\Query\Query $query
     * @param Endpoint|string|null                                  $endpoint
     *
     * @return \Solarium\QueryType\Select\Result\Result
     */
    public function select(QueryInterface $query, $endpoint = null);

    /**
     * Execute a MoreLikeThis query.
     *
     * Example usage:
     * <code>
     * $client = new Solarium\Client;
     * $query = $client->createMoreLikeThis();
     * $result = $client->moreLikeThis($query);
     * </code>
     *
     * @see \Solarium\QueryType\MoreLikeThis\Query
     * @see \Solarium\QueryType\MoreLikeThis\Result
     *
     * @internal this is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API
     *
     * @param QueryInterface|\Solarium\QueryType\MoreLikeThis\Query $query
     * @param Endpoint|string|null                                  $endpoint
     *
     * @return \Solarium\QueryType\MoreLikeThis\Result
     */
    public function moreLikeThis(QueryInterface $query, $endpoint = null);

    /**
     * Execute an analysis query.
     *
     * @internal this is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API
     *
     * @param QueryInterface|AnalysisQueryDocument|AnalysisQueryField $query
     * @param Endpoint|string|null                                    $endpoint
     *
     * @return AnalysisResultDocument|AnalysisResultField
     */
    public function analyze(QueryInterface $query, $endpoint = null);

    /**
     * Execute a terms query.
     *
     * @internal this is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API
     *
     * @param QueryInterface|\Solarium\QueryType\Terms\Query $query
     * @param Endpoint|string|null                           $endpoint
     *
     * @return \Solarium\QueryType\Terms\Result
     */
    public function terms(QueryInterface $query, $endpoint = null);

    /**
     * Execute a spellcheck query.
     *
     * @internal this is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API
     *
     * @param QueryInterface|\Solarium\QueryType\Spellcheck\Query $query
     * @param Endpoint|string|null                                $endpoint
     *
     * @return \Solarium\QueryType\Spellcheck\Result\Result
     */
    public function spellcheck(QueryInterface $query, $endpoint = null);

    /**
     * Execute a suggester query.
     *
     * @internal this is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API
     *
     * @param QueryInterface|\Solarium\QueryType\Suggester\Query $query
     * @param Endpoint|string|null                               $endpoint
     *
     * @return \Solarium\QueryType\Suggester\Result\Result
     */
    public function suggester(QueryInterface $query, $endpoint = null);

    /**
     * Execute an extract query.
     *
     * @internal this is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API
     *
     * @param QueryInterface|\Solarium\QueryType\Extract\Query $query
     * @param Endpoint|string|null                             $endpoint
     *
     * @return \Solarium\QueryType\Extract\Result
     */
    public function extract(QueryInterface $query, $endpoint = null);

    /**
     * Execute a RealtimeGet query.
     *
     * @internal this is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API
     *
     * @param QueryInterface|\Solarium\QueryType\RealtimeGet\Query $query
     * @param Endpoint|string|null                                 $endpoint
     *
     * @return \Solarium\QueryType\RealtimeGet\Result
     */
    public function realtimeGet(QueryInterface $query, $endpoint = null);

    /**
     * Execute a CoreAdmin query.
     *
     * @internal this is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API
     *
     * @param QueryInterface|\Solarium\QueryType\Server\CoreAdmin\Query\Query $query
     * @param Endpoint|string|null                                            $endpoint
     *
     * @return \Solarium\QueryType\Server\CoreAdmin\Result\Result
     */
    public function coreAdmin(QueryInterface $query, $endpoint = null);

    /**
     * Create a query instance.
     *
     * @param string $type
     * @param array  $options
     *
     * @throws InvalidArgumentException|UnexpectedValueException
     *
     * @return \Solarium\Core\Query\Query
     */
    public function createQuery($type, $options = null);

    /**
     * Create a select query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\Select\Query\Query
     */
    public function createSelect($options = null);

    /**
     * Create a MoreLikeThis query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\MorelikeThis\Query
     */
    public function createMoreLikeThis($options = null);

    /**
     * Create an update query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\Update\Query\Query
     */
    public function createUpdate($options = null);

    /**
     * Create a ping query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\Ping\Query
     */
    public function createPing($options = null);

    /**
     * Create an analysis field query instance.
     *
     * @param mixed $options
     *
     * @return AnalysisQueryField
     */
    public function createAnalysisField($options = null);

    /**
     * Create an analysis document query instance.
     *
     * @param mixed $options
     *
     * @return AnalysisQueryDocument
     */
    public function createAnalysisDocument($options = null);

    /**
     * Create a terms query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\Terms\Query
     */
    public function createTerms($options = null);

    /**
     * Create a spellcheck query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\Spellcheck\Query
     */
    public function createSpellcheck($options = null);

    /**
     * Create a suggester query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\Suggester\Query
     */
    public function createSuggester($options = null);

    /**
     * Create an extract query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\Extract\Query
     */
    public function createExtract($options = null);

    /**
     * Create a RealtimeGet query instance.
     *
     * @param mixed $options
     *
     * @return \Solarium\QueryType\RealtimeGet\Query
     */
    public function createRealtimeGet($options = null);

    /**
     * @param mixed $options
     *
     * @return \Solarium\QueryType\Server\CoreAdmin\Query\Query
     */
    public function createCoreAdmin($options = null);
}
