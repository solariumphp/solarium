<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Client;

use Psr\EventDispatcher\EventDispatcherInterface;
use Solarium\Core\Client\Adapter\AdapterInterface;
use Solarium\Core\Plugin\PluginInterface;
use Solarium\Core\Query\QueryInterface;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\OutOfBoundsException;
use Solarium\Exception\UnexpectedValueException;
use Solarium\QueryType\Analysis\Query\Document as AnalysisQueryDocument;
use Solarium\QueryType\Analysis\Query\Field as AnalysisQueryField;
use Solarium\QueryType\Extract\Query as ExtractQuery;
use Solarium\QueryType\Extract\Result as ExtractResult;
use Solarium\QueryType\Graph\Query as GraphQuery;
use Solarium\QueryType\ManagedResources\Query\Resources as ManagedResourcesQuery;
use Solarium\QueryType\ManagedResources\Query\Stopwords as ManagedStopwordsQuery;
use Solarium\QueryType\ManagedResources\Query\Synonyms as ManagedSynonymsQuery;
use Solarium\QueryType\MoreLikeThis\Query as MoreLikeThisQuery;
use Solarium\QueryType\MoreLikeThis\Result as MoreLikeThisResult;
use Solarium\QueryType\Ping\Query as PingQuery;
use Solarium\QueryType\Ping\Result as PingResult;
use Solarium\QueryType\RealtimeGet\Query as RealtimeGetQuery;
use Solarium\QueryType\RealtimeGet\Result as RealtimeGetResult;
use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\QueryType\Select\Result\Result as SelectResult;
use Solarium\QueryType\Server\Api\Query as ApiQuery;
use Solarium\QueryType\Server\Collections\Query\Query as CollectionsQuery;
use Solarium\QueryType\Server\Configsets\Query\Query as ConfigsetsQuery;
use Solarium\QueryType\Server\CoreAdmin\Query\Query as CoreAdminQuery;
use Solarium\QueryType\Server\CoreAdmin\Result\Result as CoreAdminResult;
use Solarium\QueryType\Spellcheck\Query as SpellcheckQuery;
use Solarium\QueryType\Spellcheck\Result\Result as SpellcheckResult;
use Solarium\QueryType\Stream\Query as StreamQuery;
use Solarium\QueryType\Suggester\Query as SuggesterQuery;
use Solarium\QueryType\Suggester\Result\Result as SuggesterResult;
use Solarium\QueryType\Terms\Query as TermsQuery;
use Solarium\QueryType\Terms\Result as TermsResult;
use Solarium\QueryType\Update\Query\Query as UpdateQuery;
use Solarium\QueryType\Update\Result as UpdateResult;

/**
 * Main interface for interaction with Solr.
 *
 * The client is the main interface for usage of the Solarium library.
 * You can use it to get query instances and to execute them.
 * It also allows to register plugins and query types to customize Solarium.
 * It gives access to the event dispatcher so that you can add listeners.
 * Finally, it also gives access to the adapter, which holds the Solr connection settings.
 *
 * Example usage with default settings:
 * <code>
 * $client = new Solarium\Client($adapter, $eventDispatcher);
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
    public function createEndpoint($options = null, bool $setAsDefault = false): Endpoint;

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
    public function addEndpoint($endpoint): self;

    /**
     * Add multiple endpoints.
     *
     * @param array $endpoints
     *
     * @return self Provides fluent interface
     */
    public function addEndpoints(array $endpoints): self;

    /**
     * Get an endpoint by key.
     *
     * @param string $key
     *
     * @throws OutOfBoundsException
     *
     * @return Endpoint
     */
    public function getEndpoint(string $key = null): Endpoint;

    /**
     * Get all endpoints.
     *
     * @return Endpoint[]
     */
    public function getEndpoints(): array;

    /**
     * Remove a single endpoint.
     *
     * You can remove a endpoint by passing its key, or by passing the endpoint instance
     *
     * @param string|Endpoint $endpoint
     *
     * @return self Provides fluent interface
     */
    public function removeEndpoint($endpoint): self;

    /**
     * Remove all endpoints.
     *
     * @return self Provides fluent interface
     */
    public function clearEndpoints(): self;

    /**
     * Set multiple endpoints.
     *
     * This overwrites any existing endpoints
     *
     * @param array $endpoints
     *
     * @return self Provides fluent interface
     */
    public function setEndpoints(array $endpoints): self;

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
    public function setDefaultEndpoint($endpoint): self;

    /**
     * Set the adapter.
     *
     * @param AdapterInterface $adapter
     *
     * @return self Provides fluent interface
     */
    public function setAdapter(AdapterInterface $adapter): self;

    /**
     * Get the adapter instance.
     *
     * @return AdapterInterface
     */
    public function getAdapter(): AdapterInterface;

    /**
     * Register a query type.
     *
     * You can also use this method to override any existing query type with a new mapping.
     * This requires the availability of the classes through autoloading or a manual
     * require before calling this method.
     *
     * @param string $type
     * @param string $queryClass
     *
     * @return self Provides fluent interface
     */
    public function registerQueryType(string $type, string $queryClass): self;

    /**
     * Register multiple query types.
     *
     * @param array $queryTypes
     *
     * @return self Provides fluent interface
     */
    public function registerQueryTypes(array $queryTypes): self;

    /**
     * Get all registered query types.
     *
     * @return array
     */
    public function getQueryTypes(): array;

    /**
     * Gets the event dispatcher.
     *
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher(): EventDispatcherInterface;

    /**
     * Sets the event dispatcher.
     *
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return self Provides fluent interface
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): self;

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
    public function registerPlugin(string $key, $plugin, array $options = []): self;

    /**
     * Register multiple plugins.
     *
     * @param array $plugins
     *
     * @return self Provides fluent interface
     */
    public function registerPlugins(array $plugins): self;

    /**
     * Get all registered plugins.
     *
     * @return PluginInterface[]
     */
    public function getPlugins(): array;

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
    public function getPlugin(string $key, bool $autocreate = true): ?PluginInterface;

    /**
     * Remove a plugin instance.
     *
     * You can remove a plugin by passing the plugin key, or the plugin instance
     *
     * @param string|PluginInterface $plugin
     *
     * @return self Provides fluent interface
     */
    public function removePlugin($plugin): self;

    /**
     * Creates a request based on a query instance.
     *
     * @param QueryInterface $query
     *
     * @throws UnexpectedValueException
     *
     * @return Request
     */
    public function createRequest(QueryInterface $query): Request;

    /**
     * Creates a result object.
     *
     * @param QueryInterface $query
     * @param array|Response $response
     *
     * @throws UnexpectedValueException;
     *
     * @return ResultInterface
     */
    public function createResult(QueryInterface $query, $response): ResultInterface;

    /**
     * Execute a query.
     *
     * @param QueryInterface       $query
     * @param Endpoint|string|null $endpoint
     *
     * @return ResultInterface
     */
    public function execute(QueryInterface $query, $endpoint = null): ResultInterface;

    /**
     * Execute a request and return the response.
     *
     * @param Request              $request
     * @param Endpoint|string|null $endpoint
     *
     * @return Response
     */
    public function executeRequest(Request $request, $endpoint = null): Response;

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
     * @return ResultInterface|\Solarium\QueryType\Ping\Result
     */
    public function ping(QueryInterface $query, $endpoint = null): PingResult;

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
     * @return ResultInterface|\Solarium\QueryType\Update\Result
     */
    public function update(QueryInterface $query, $endpoint = null): UpdateResult;

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
     * @return ResultInterface|\Solarium\QueryType\Select\Result\Result
     */
    public function select(QueryInterface $query, $endpoint = null): SelectResult;

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
     * @return ResultInterface|\Solarium\QueryType\MoreLikeThis\Result
     */
    public function moreLikeThis(QueryInterface $query, $endpoint = null): MoreLikeThisResult;

    /**
     * Execute an analysis query.
     *
     * @internal this is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API
     *
     * @param QueryInterface|\Solarium\QueryType\Analysis\Query\Document|\Solarium\QueryType\Analysis\Query\Field $query
     * @param Endpoint|string|null                                                                                $endpoint
     *
     * @return ResultInterface|\Solarium\QueryType\Analysis\Result\Document|\Solarium\QueryType\Analysis\Result\Field
     */
    public function analyze(QueryInterface $query, $endpoint = null): ResultInterface;

    /**
     * Execute a terms query.
     *
     * @internal this is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API
     *
     * @param QueryInterface|\Solarium\QueryType\Terms\Query $query
     * @param Endpoint|string|null                           $endpoint
     *
     * @return ResultInterface|\Solarium\QueryType\Terms\Result
     */
    public function terms(QueryInterface $query, $endpoint = null): TermsResult;

    /**
     * Execute a spellcheck query.
     *
     * @internal this is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API
     *
     * @param QueryInterface|\Solarium\QueryType\Spellcheck\Query $query
     * @param Endpoint|string|null                                $endpoint
     *
     * @return ResultInterface|\Solarium\QueryType\Spellcheck\Result\Result
     */
    public function spellcheck(QueryInterface $query, $endpoint = null): SpellcheckResult;

    /**
     * Execute a suggester query.
     *
     * @internal this is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API
     *
     * @param QueryInterface|\Solarium\QueryType\Suggester\Query $query
     * @param Endpoint|string|null                               $endpoint
     *
     * @return ResultInterface|\Solarium\QueryType\Suggester\Result\Result
     */
    public function suggester(QueryInterface $query, $endpoint = null): SuggesterResult;

    /**
     * Execute an extract query.
     *
     * @internal this is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API
     *
     * @param QueryInterface|\Solarium\QueryType\Extract\Query $query
     * @param Endpoint|string|null                             $endpoint
     *
     * @return ResultInterface|\Solarium\QueryType\Extract\Result
     */
    public function extract(QueryInterface $query, $endpoint = null): ExtractResult;

    /**
     * Execute a RealtimeGet query.
     *
     * @internal this is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API
     *
     * @param QueryInterface|\Solarium\QueryType\RealtimeGet\Query $query
     * @param Endpoint|string|null                                 $endpoint
     *
     * @return ResultInterface|\Solarium\QueryType\RealtimeGet\Result
     */
    public function realtimeGet(QueryInterface $query, $endpoint = null): RealtimeGetResult;

    /**
     * Execute a CoreAdmin query.
     *
     * @internal this is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API
     *
     * @param QueryInterface|\Solarium\QueryType\Server\CoreAdmin\Query\Query $query
     * @param Endpoint|string|null                                            $endpoint
     *
     * @return ResultInterface|\Solarium\QueryType\Server\CoreAdmin\Result\Result
     */
    public function coreAdmin(QueryInterface $query, $endpoint = null): CoreAdminResult;

    /**
     * Execute a Collections API query.
     *
     * @internal this is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API
     *
     * @param QueryInterface|\Solarium\QueryType\Server\Collections\Query\Query $query
     * @param Endpoint|string|null                                              $endpoint
     *
     * @return ResultInterface|\Solarium\QueryType\Server\Collections\Result\ClusterStatusResult
     */
    public function collections(QueryInterface $query, $endpoint = null): ResultInterface;

    /**
     * Execute a Configsets API query.
     *
     * @internal this is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API
     *
     * @param QueryInterface|\Solarium\QueryType\Server\Configsets\Query\Query $query
     * @param Endpoint|string|null                                             $endpoint
     *
     * @return ResultInterface|\Solarium\QueryType\Server\Configsets\Result\ListConfigsetsResult
     */
    public function configsets(QueryInterface $query, $endpoint = null): ResultInterface;

    /**
     * Create a query instance.
     *
     * @param string $type
     * @param array  $options
     *
     * @throws InvalidArgumentException|UnexpectedValueException
     *
     * @return \Solarium\Core\Query\AbstractQuery|QueryInterface
     */
    public function createQuery(string $type, array $options = null): QueryInterface;

    /**
     * Create a select query instance.
     *
     * @param array $options
     *
     * @return \Solarium\QueryType\Select\Query\Query
     */
    public function createSelect(array $options = null): SelectQuery;

    /**
     * Create a MoreLikeThis query instance.
     *
     * @param array $options
     *
     * @return \Solarium\QueryType\MoreLikeThis\Query
     */
    public function createMoreLikeThis(array $options = null): MoreLikeThisQuery;

    /**
     * Create an update query instance.
     *
     * @param array $options
     *
     * @return \Solarium\QueryType\Update\Query\Query
     */
    public function createUpdate(array $options = null): UpdateQuery;

    /**
     * Create a ping query instance.
     *
     * @param array $options
     *
     * @return \Solarium\QueryType\Ping\Query
     */
    public function createPing(array $options = null): PingQuery;

    /**
     * Create an analysis field query instance.
     *
     * @param array $options
     *
     * @return AnalysisQueryField
     */
    public function createAnalysisField(array $options = null): AnalysisQueryField;

    /**
     * Create an analysis document query instance.
     *
     * @param array $options
     *
     * @return AnalysisQueryDocument
     */
    public function createAnalysisDocument(array $options = null): AnalysisQueryDocument;

    /**
     * Create a terms query instance.
     *
     * @param array $options
     *
     * @return \Solarium\QueryType\Terms\Query
     */
    public function createTerms(array $options = null): TermsQuery;

    /**
     * Create a spellcheck query instance.
     *
     * @param array $options
     *
     * @return \Solarium\QueryType\Spellcheck\Query
     */
    public function createSpellcheck(array $options = null): SpellcheckQuery;

    /**
     * Create a suggester query instance.
     *
     * @param array $options
     *
     * @return \Solarium\QueryType\Suggester\Query
     */
    public function createSuggester(array $options = null): SuggesterQuery;

    /**
     * Create an extract query instance.
     *
     * @param array $options
     *
     * @return \Solarium\QueryType\Extract\Query
     */
    public function createExtract(array $options = null): ExtractQuery;

    /**
     * Create a stream query instance.
     *
     * @param array $options
     *
     * @return \Solarium\QueryType\Stream\Query
     */
    public function createStream(array $options = null): StreamQuery;

    /**
     * Create a graph query instance.
     *
     * @param array $options
     *
     * @return \Solarium\QueryType\Graph\Query
     */
    public function createGraph(array $options = null): GraphQuery;

    /**
     * Create a RealtimeGet query instance.
     *
     * @param array $options
     *
     * @return \Solarium\QueryType\RealtimeGet\Query
     */
    public function createRealtimeGet(array $options = null): RealtimeGetQuery;

    /**
     * Create a CoreAdmin query instance.
     *
     * @param array $options
     *
     * @return \Solarium\QueryType\Server\CoreAdmin\Query\Query
     */
    public function createCoreAdmin(array $options = null): CoreAdminQuery;

    /**
     * Create a Collections API query instance.
     *
     * @param array $options
     *
     * @return \Solarium\QueryType\Server\Collections\Query\Query
     */
    public function createCollections(array $options = null): CollectionsQuery;

    /**
     * Create a Configsets API query instance.
     *
     * @param array $options
     *
     * @return \Solarium\QueryType\Server\Configsets\Query\Query
     */
    public function createConfigsets(array $options = null): ConfigsetsQuery;

    /**
     * Create an API query instance.
     *
     * @param array $options
     *
     * @return \Solarium\QueryType\Server\Api\Query
     */
    public function createApi(array $options = null): ApiQuery;

    /**
     * Create a managed resources query instance.
     *
     * @param array $options
     *
     * @return \Solarium\QueryType\ManagedResources\Query\Resources
     */
    public function createManagedResources(array $options = null): ManagedResourcesQuery;

    /**
     * Create a managed stopwords query instance.
     *
     * @param array $options
     *
     * @return \Solarium\QueryType\ManagedResources\Query\Stopwords
     */
    public function createManagedStopwords(array $options = null): ManagedStopwordsQuery;

    /**
     * Create a managed synonyms query instance.
     *
     * @param array $options
     *
     * @return \Solarium\QueryType\ManagedResources\Query\Synonyms
     */
    public function createManagedSynonyms(array $options = null): ManagedSynonymsQuery;
}
