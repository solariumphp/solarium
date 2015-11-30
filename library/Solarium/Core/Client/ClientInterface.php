<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 *
 * @copyright Copyright 2011 Bas de Nooijer <solarium@raspberry.nl>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */
namespace Solarium\Core\Client;

use Solarium\Core\Query\QueryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Main interface for interaction with Solr
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
     * Create a endpoint instance
     *
     * If you supply a string as the first arguments ($options) it will be used as the key for the endpoint
     * and it will be registered.
     * If you supply an options array/object that contains a key the endpoint will also be registered.
     *
     * When no key is supplied the endpoint cannot be registered, in that case you will need to do this manually
     * after setting the key, by using the addEndpoint method.
     *
     * @param  mixed    $options
     * @param  boolean  $setAsDefault
     * @return Endpoint
     */
    public function createEndpoint($options = null, $setAsDefault = false);

    /**
     * Add an endpoint
     *
     * Supports a endpoint instance or a config array as input.
     * In case of options a new endpoint instance wil be created based on the options.
     *
     * @throws InvalidArgumentException
     * @param  Endpoint|array           $endpoint
     * @return self                     Provides fluent interface
     */
    public function addEndpoint($endpoint);

    /**
     * Add multiple endpoints
     *
     * @param  array $endpoints
     * @return self  Provides fluent interface
     */
    public function addEndpoints(array $endpoints);

    /**
     * Get an endpoint by key
     *
     * @throws OutOfBoundsException
     * @param  string               $key
     * @return Endpoint
     */
    public function getEndpoint($key = null);

    /**
     * Get all endpoints
     *
     * @return Endpoint[]
     */
    public function getEndpoints();

    /**
     * Remove a single endpoint
     *
     * You can remove a endpoint by passing it's key, or by passing the endpoint instance
     *
     * @param  string|Endpoint $endpoint
     * @return ClientInterface Provides fluent interface
     */
    public function removeEndpoint($endpoint);

    /**
     * Remove all endpoints
     *
     * @return self Provides fluent interface
     */
    public function clearEndpoints();

    /**
     * Set multiple endpoints
     *
     * This overwrites any existing endpoints
     *
     * @param array $endpoints
     */
    public function setEndpoints($endpoints);

    /**
     * Set a default endpoint
     *
     * All queries executed without a specific endpoint will use this default endpoint.
     *
     * @param  string|Endpoint      $endpoint
     * @return ClientInterface      Provides fluent interface
     * @throws OutOfBoundsException
     */
    public function setDefaultEndpoint($endpoint);

    /**
     * Set the adapter
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
     * @throws InvalidArgumentException
     * @param  string|Adapter\AdapterInterface $adapter
     * @return ClientInterface                 Provides fluent interface
     */
    public function setAdapter($adapter);

    /**
     * Get the adapter instance
     *
     * If {@see $adapter} doesn't hold an instance a new one will be created by
     * calling {@see createAdapter()}
     *
     * @param  boolean          $autoload
     * @return AdapterInterface
     */
    public function getAdapter($autoload = true);

    /**
     * Register a querytype
     *
     * You can also use this method to override any existing querytype with a new mapping.
     * This requires the availability of the classes through autoloading or a manual
     * require before calling this method.
     *
     * @param  string $type
     * @param  string $queryClass
     * @return self   Provides fluent interface
     */
    public function registerQueryType($type, $queryClass);

    /**
     * Register multiple querytypes
     *
     * @param  array $queryTypes
     * @return self  Provides fluent interface
     */
    public function registerQueryTypes($queryTypes);

    /**
     * Get all registered querytypes
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
     * @return $this
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher);

    /**
     * Register a plugin
     *
     * You can supply a plugin instance or a plugin classname as string.
     * This requires the availability of the class through autoloading
     * or a manual require.
     *
     * @throws InvalidArgumentException
     * @param  string                   $key
     * @param  string|PluginInterface   $plugin
     * @param  array                    $options
     * @return self                     Provides fluent interface
     */
    public function registerPlugin($key, $plugin, $options = array());

    /**
     * Register multiple plugins
     *
     * @param  array $plugins
     * @return self  Provides fluent interface
     */
    public function registerPlugins($plugins);

    /**
     * Get all registered plugins
     *
     * @return PluginInterface[]
     */
    public function getPlugins();

    /**
     * Get a plugin instance
     *
     * @throws OutOfBoundsException
     * @param  string               $key
     * @param  boolean              $autocreate
     * @return PluginInterface|null
     */
    public function getPlugin($key, $autocreate = true);

    /**
     * Remove a plugin instance
     *
     * You can remove a plugin by passing the plugin key, or the plugin instance
     *
     * @param  string|PluginInterface $plugin
     * @return ClientInterface        Provides fluent interface
     */
    public function removePlugin($plugin);

    /**
     * Creates a request based on a query instance
     *
     * @throws UnexpectedValueException
     * @param  QueryInterface           $query
     * @return Request
     */
    public function createRequest(QueryInterface $query);

    /**
     * Creates a result object
     *
     * @throws UnexpectedValueException;
     * @param  QueryInterface            $query
     * @param  array Response            $response
     * @return ResultInterface
     */
    public function createResult(QueryInterface $query, $response);

    /**
     * Execute a query
     *
     * @param  QueryInterface       $query
     * @param  Endpoint|string|null $endpoint
     * @return ResultInterface
     */
    public function execute(QueryInterface $query, $endpoint = null);

    /**
     * Execute a request and return the response
     *
     * @param Request
     * @param Endpoint|string|null
     * @return Response
     */
    public function executeRequest($request, $endpoint = null);

    /**
     * Execute a ping query
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
     * @internal This is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API.
     *
     * @param  QueryInterface|\Solarium\QueryType\Ping\Query $query
     * @param  Endpoint|string|null                          $endpoint
     * @return \Solarium\QueryType\Ping\Result
     */
    public function ping(QueryInterface $query, $endpoint = null);

    /**
     * Execute an update query
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
     * @internal This is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API.
     *
     * @param  QueryInterface|\Solarium\QueryType\Update\Query\Query $query
     * @param  Endpoint|string|null                                  $endpoint
     * @return \Solarium\QueryType\Update\Result
     */
    public function update(QueryInterface $query, $endpoint = null);

    /**
     * Execute a select query
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
     * @internal This is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API.
     *
     * @param  QueryInterface|\Solarium\QueryType\Select\Query\Query $query
     * @param  Endpoint|string|null                                  $endpoint
     * @return \Solarium\QueryType\Select\Result\Result
     */
    public function select(QueryInterface $query, $endpoint = null);

    /**
     * Execute a MoreLikeThis query
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
     * @internal This is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API.
     *
     * @param  QueryInterface|\Solarium\QueryType\MoreLikeThis\Query $query
     * @param  Endpoint|string|null                                  $endpoint
     * @return \Solarium\QueryType\MoreLikeThis\Result
     */
    public function moreLikeThis(QueryInterface $query, $endpoint = null);

    /**
     * Execute an analysis query
     *
     * @internal This is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API.
     *
     * @param  QueryInterface|\Solarium\QueryType\Analysis\Query\Document|\Solarium\QueryType\Analysis\Query\Field $query
     * @param  Endpoint|string|null                                                                                $endpoint
     * @return \Solarium\QueryType\Analysis\Result\Document|\Solarium\QueryType\Analysis\Result\Field
     */
    public function analyze(QueryInterface $query, $endpoint = null);

    /**
     * Execute a terms query
     *
     * @internal This is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API.
     *
     * @param  QueryInterface|\Solarium\QueryType\Terms\Query $query
     * @param  Endpoint|string|null                           $endpoint
     * @return \Solarium\QueryType\Terms\Result
     */
    public function terms(QueryInterface $query, $endpoint = null);

    /**
     * Execute a suggester query
     *
     * @internal This is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API.
     *
     * @param  QueryInterface|\Solarium\QueryType\Suggester\Query $query
     * @param  Endpoint|string|null                               $endpoint
     * @return \Solarium\QueryType\Suggester\Result\Result
     */
    public function suggester(QueryInterface $query, $endpoint = null);

    /**
     * Execute an extract query
     *
     * @internal This is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API.
     *
     * @param  QueryInterface|\Solarium\QueryType\Extract\Query $query
     * @param  Endpoint|string|null                             $endpoint
     * @return \Solarium\QueryType\Extract\Result
     */
    public function extract(QueryInterface $query, $endpoint = null);

    /**
     * Execute a RealtimeGet query
     *
     * @internal This is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API.
     *
     * @param  QueryInterface|\Solarium\QueryType\RealtimeGet\Query $query
     * @param  Endpoint|string|null                                 $endpoint
     * @return \Solarium\QueryType\RealtimeGet\Result
     */
    public function realtimeGet(QueryInterface $query, $endpoint = null);

    /**
     * Create a query instance
     *
     * @throws InvalidArgumentException|UnexpectedValueException
     * @param  string                                            $type
     * @param  array                                             $options
     * @return \Solarium\Core\Query\Query
     */
    public function createQuery($type, $options = null);

    /**
     * Create a select query instance
     *
     * @param  mixed                                  $options
     * @return \Solarium\QueryType\Select\Query\Query
     */
    public function createSelect($options = null);

    /**
     * Create a MoreLikeThis query instance
     *
     * @param  mixed                                  $options
     * @return \Solarium\QueryType\MorelikeThis\Query
     */
    public function createMoreLikeThis($options = null);

    /**
     * Create an update query instance
     *
     * @param  mixed                                  $options
     * @return \Solarium\QueryType\Update\Query\Query
     */
    public function createUpdate($options = null);

    /**
     * Create a ping query instance
     *
     * @param  mixed                          $options
     * @return \Solarium\QueryType\Ping\Query
     */
    public function createPing($options = null);

    /**
     * Create an analysis field query instance
     *
     * @param  mixed                                    $options
     * @return \Solarium\QueryType\Analysis\Query\Field
     */
    public function createAnalysisField($options = null);

    /**
     * Create an analysis document query instance
     *
     * @param  mixed                                       $options
     * @return \Solarium\QueryType\Analysis\Query\Document
     */
    public function createAnalysisDocument($options = null);

    /**
     * Create a terms query instance
     *
     * @param  mixed                           $options
     * @return \Solarium\QueryType\Terms\Query
     */
    public function createTerms($options = null);

    /**
     * Create a suggester query instance
     *
     * @param  mixed                               $options
     * @return \Solarium\QueryType\Suggester\Query
     */
    public function createSuggester($options = null);

    /**
     * Create an extract query instance
     *
     * @param  mixed                             $options
     * @return \Solarium\QueryType\Extract\Query
     */
    public function createExtract($options = null);

    /**
     * Create a RealtimeGet query instance
     *
     * @param  mixed                                 $options
     * @return \Solarium\QueryType\RealtimeGet\Query
     */
    public function createRealtimeGet($options = null);
}
