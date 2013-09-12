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
namespace Solarium\Plugin\Loadbalancer;

use Solarium\Core\Plugin\Plugin;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Query\Query;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\OutOfBoundsException;
use Solarium\Exception\RuntimeException;
use Solarium\Exception\HttpException;
use Solarium\Plugin\Loadbalancer\Event\Events;
use Solarium\Plugin\Loadbalancer\Event\EndpointFailure as EndpointFailureEvent;
use Solarium\Core\Event\Events as CoreEvents;
use Solarium\Core\Event\PreCreateRequest as PreCreateRequestEvent;
use Solarium\Core\Event\PreExecuteRequest as PreExecuteRequestEvent;

/**
 * Loadbalancer plugin
 *
 * Using this plugin you can use software loadbalancing over multiple Solr instances.
 * You can add any number of endpoints, each with their own weight. The weight influences
 * the probability of a endpoint being used for a query.
 *
 * By default all queries except updates are loadbalanced. This can be customized by setting blocked querytypes.
 * Any querytype that may not be loadbalanced will be executed by Solarium with the default endpoint.
 * In a master-slave setup the default endpoint should be connecting to the master endpoint.
 *
 * You can also enable the failover mode. In this case a query will be retried on another endpoint in case of error.
 */
class Loadbalancer extends Plugin
{
    /**
     * Default options
     *
     * @var array
     */
    protected $options = array(
        'failoverenabled' => false,
        'failovermaxretries' => 1,
    );

    /**
     * Registered endpoints
     *
     * @var Endpoint[]
     */
    protected $endpoints = array();

    /**
     * Query types that are blocked from loadbalancing
     *
     * @var array
     */
    protected $blockedQueryTypes = array(
        Client::QUERY_UPDATE => true
    );

    /**
     * Last used endpoint key
     *
     * The value can be null if no queries have been executed, or if the last executed query didn't use loadbalancing.
     *
     * @var null|string
     */
    protected $lastEndpoint;

    /**
     * Endpoint key to use for next query (overrules randomizer)
     *
     * @var string
     */
    protected $nextEndpoint;

    /**
     * Default endpoint key
     *
     * This endpoint is used for queries that cannot be loadbalanced
     * (for instance update queries that need to go to the master)
     *
     * @var string
     */
    protected $defaultEndpoint;

    /**
     * Pool of endpoint keys to use for requests
     *
     * @var WeightedRandomChoice
     */
    protected $randomizer;

    /**
     * Query type
     *
     * @var string
     */
    protected $queryType;

    /**
     * Used for failover mechanism
     *
     * @var array
     */
    protected $endpointExcludes;

    /**
     * Initialize options
     *
     * Several options need some extra checks or setup work, for these options
     * the setters are called.
     *
     * @return void
     */
    protected function init()
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'endpoint':
                    $this->setEndpoints($value);
                    break;
                case 'blockedquerytype':
                    $this->setBlockedQueryTypes($value);
                    break;
            }
        }
    }

    /**
     * Plugin init function
     *
     * Register event listeners
     *
     * @return void
     */
    protected function initPluginType()
    {
        $dispatcher = $this->client->getEventDispatcher();
        $dispatcher->addListener(CoreEvents::PRE_EXECUTE_REQUEST, array($this, 'preExecuteRequest'));
        $dispatcher->addListener(CoreEvents::PRE_CREATE_REQUEST, array($this, 'preCreateRequest'));
    }

    /**
     * Set failover enabled option
     *
     * @param  bool $value
     * @return self Provides fluent interface
     */
    public function setFailoverEnabled($value)
    {
        return $this->setOption('failoverenabled', $value);
    }

    /**
     * Get failoverenabled option
     *
     * @return boolean
     */
    public function getFailoverEnabled()
    {
        return $this->getOption('failoverenabled');
    }

    /**
     * Set failover max retries
     *
     * @param  int  $value
     * @return self Provides fluent interface
     */
    public function setFailoverMaxRetries($value)
    {
        return $this->setOption('failovermaxretries', $value);
    }

    /**
     * Get failovermaxretries option
     *
     * @return int
     */
    public function getFailoverMaxRetries()
    {
        return $this->getOption('failovermaxretries');
    }

    /**
     * Add an endpoint to the loadbalacing 'pool'
     *
     * @throws InvalidArgumentException
     * @param  Endpoint|string          $endpoint
     * @param  int                      $weight   Must be a positive number
     * @return self                     Provides fluent interface
     */
    public function addEndpoint($endpoint, $weight = 1)
    {
        if (!is_string($endpoint)) {
            $endpoint = $endpoint->getKey();
        }

        if (array_key_exists($endpoint, $this->endpoints)) {
            throw new InvalidArgumentException('An endpoint for the loadbalancer plugin must have a unique key');
        } else {
            $this->endpoints[$endpoint] = $weight;
        }

        // reset the randomizer as soon as a new endpoint is added
        $this->randomizer = null;

        return $this;
    }

    /**
     * Add multiple endpoints
     *
     * @param  array $endpoints
     * @return self  Provides fluent interface
     */
    public function addEndpoints(array $endpoints)
    {
        foreach ($endpoints as $endpoint => $weight) {
            $this->addEndpoint($endpoint, $weight);
        }

        return $this;
    }

    /**
     * Get the endpoints in the loadbalancing pool
     *
     * @return Endpoint[]
     */
    public function getEndpoints()
    {
        return $this->endpoints;
    }

    /**
     * Clear all endpoint entries
     *
     * @return self Provides fluent interface
     */
    public function clearEndpoints()
    {
        $this->endpoints = array();
    }

    /**
     * Remove an endpoint by key
     *
     * @param  Endpoint|string $endpoint
     * @return self            Provides fluent interface
     */
    public function removeEndpoint($endpoint)
    {
        if (!is_string($endpoint)) {
            $endpoint = $endpoint->getKey();
        }

        if (isset($this->endpoints[$endpoint])) {
            unset($this->endpoints[$endpoint]);
        }

        return $this;
    }

    /**
     * Set multiple endpoints
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
     * Set a forced endpoints (by key) for the next request
     *
     * As soon as one query has used the forced endpoint this setting is reset. If you want to remove this setting
     * pass NULL as the key value.
     *
     * If the next query cannot be loadbalanced (for instance based on the querytype) this setting is ignored
     * but will still be reset.
     *
     * @throws OutOfBoundsException
     * @param  string|null|Endpoint $endpoint
     * @return self                 Provides fluent interface
     */
    public function setForcedEndpointForNextQuery($endpoint)
    {
        if (!is_string($endpoint)) {
            $endpoint = $endpoint->getKey();
        }

        if ($endpoint !== null && !array_key_exists($endpoint, $this->endpoints)) {
            throw new OutOfBoundsException('Unknown endpoint forced for next query');
        }

        $this->nextEndpoint = $endpoint;

        return $this;
    }

    /**
     * Get the ForcedEndpointForNextQuery value
     *
     * @return string|null
     */
    public function getForcedEndpointForNextQuery()
    {
        return $this->nextEndpoint;
    }

    /**
     * Get an array of blocked querytypes
     *
     * @return array
     */
    public function getBlockedQueryTypes()
    {
        return array_keys($this->blockedQueryTypes);
    }

    /**
     * Set querytypes to block from loadbalancing
     *
     * Overwrites any existing types
     *
     * @param  array $types Use an array with the constants defined in Solarium\Client as values
     * @return self  Provides fluent interface
     */
    public function setBlockedQueryTypes($types)
    {
        $this->clearBlockedQueryTypes();
        $this->addBlockedQueryTypes($types);

        return $this;
    }

    /**
     * Add a querytype to block from loadbalancing
     *
     * @param  string $type Use one of the constants defined in Solarium\Client
     * @return self   Provides fluent interface
     */
    public function addBlockedQueryType($type)
    {
        if (!array_key_exists($type, $this->blockedQueryTypes)) {
            $this->blockedQueryTypes[$type] = true;
        }

        return $this;
    }

    /**
     * Add querytypes to block from loadbalancing
     *
     * Appended to any existing types
     *
     * @param  array $types Use an array with the constants defined in Solarium\Client as values
     * @return self  Provides fluent interface
     */
    public function addBlockedQueryTypes($types)
    {
        foreach ($types as $type) {
            $this->addBlockedQueryType($type);
        }
    }

    /**
     * Remove a single querytype from the block list
     *
     * @param  string $type
     * @return void
     */
    public function removeBlockedQueryType($type)
    {
        if (array_key_exists($type, $this->blockedQueryTypes)) {
            unset($this->blockedQueryTypes[$type]);
        }
    }

    /**
     * Clear all blocked querytypes
     *
     * @return self Provides fluent interface
     */
    public function clearBlockedQueryTypes()
    {
        $this->blockedQueryTypes = array();
    }

    /**
     * Get the key of the endpoint that was used for the last query
     *
     * May return a null value if no query has been executed yet, or the last query could not be loadbalanced.
     *
     * @return null|string
     */
    public function getLastEndpoint()
    {
        return $this->lastEndpoint;
    }

    /**
     * Event hook to capture querytype
     *
     * @param  PreCreateRequestEvent $event
     * @return void
     */
    public function preCreateRequest(PreCreateRequestEvent $event)
    {
        $this->queryType = $event->getQuery()->getType();
    }

    /**
     * Event hook to adjust client settings just before query execution
     *
     * @param PreExecuteRequestEvent $event
     */
    public function preExecuteRequest(PreExecuteRequestEvent $event)
    {
        $adapter = $this->client->getAdapter();

        // save adapter presets (once) to allow the settings to be restored later
        if ($this->defaultEndpoint == null) {
            $this->defaultEndpoint = $this->client->getEndpoint()->getKey();
        }

        // check querytype: is loadbalancing allowed?
        if (!array_key_exists($this->queryType, $this->blockedQueryTypes)) {
            $response = $this->getLoadbalancedResponse($event->getRequest());
        } else {
            $endpoint = $this->client->getEndpoint($this->defaultEndpoint);
            $this->lastEndpoint = null;

            // execute request and return result
            $response = $adapter->execute($event->getRequest(), $endpoint);
        }

        $event->setResponse($response);
    }

    /**
     * Execute a request using the adapter
     *
     * @throws RuntimeException
     * @param  Request  $request
     * @return Response $response
     */
    protected function getLoadbalancedResponse($request)
    {
        $this->endpointExcludes = array(); // reset for each query
        $adapter = $this->client->getAdapter();

        if ($this->getFailoverEnabled() == true) {

            for ($i=0; $i<=$this->getFailoverMaxRetries(); $i++) {
                $endpoint = $this->getRandomEndpoint();
                try {
                    return $adapter->execute($request, $endpoint);
                } catch (HttpException $e) {
                    // ignore HTTP errors and try again
                    // but do issue an event for things like logging
                    $this->client->getEventDispatcher()->dispatch(
                        Events::ENDPOINT_FAILURE,
                        new EndpointFailureEvent($endpoint, $e)
                    );
                }
            }

            // if we get here no more retries available, throw exception
            throw new RuntimeException('Maximum number of loadbalancer retries reached');

        } else {
            // no failover retries, just execute and let an exception bubble upwards
            $endpoint = $this->getRandomEndpoint();

            return $adapter->execute($request, $endpoint);
        }
    }

    /**
     * Get a random endpoint
     *
     * @return Endpoint
     */
    protected function getRandomEndpoint()
    {
        // determine the endpoint to use
        if ($this->nextEndpoint !== null) {
            $key = $this->nextEndpoint;
            // reset forced endpoint directly after use
            $this->nextEndpoint = null;
        } else {
            $key = $this->getRandomizer()->getRandom($this->endpointExcludes);
        }

        $this->endpointExcludes[] = $key;
        $this->lastEndpoint = $key;

        return $this->client->getEndpoint($key);
    }

    /**
     * Get randomizer instance
     *
     * @return WeightedRandomChoice
     */
    protected function getRandomizer()
    {
        if ($this->randomizer === null) {
            $this->randomizer = new WeightedRandomChoice($this->endpoints);
        }

        return $this->randomizer;
    }
}
