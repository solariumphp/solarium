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
 *
 * @package Solarium
 * @subpackage Client
 */

/**
 * Main interface for interaction with Solr
 *
 * The client holds the Solr connection settings and uses an adapter instance to
 * execute queries and return the results. This is the main interface for any
 * user of the Solarium library.
 *
 * Example usage with default settings:
 * <code>
 * $client = new Solarium_Client;
 * $query = new Solarium_Query_Select;
 * $result = $client->select($query);
 * </code>
 *
 * @package Solarium
 * @subpackage Client
 */
class Solarium_Client extends Solarium_Configurable
{

    /**
     * Querytype definitions
     */
    const QUERYTYPE_SELECT = 'select';
    const QUERYTYPE_UPDATE = 'update';
    const QUERYTYPE_PING = 'ping';

    /**
     * Default options
     *
     * The defaults match a standard Solr example instance as distributed by
     * the Apache Lucene Solr project.
     *
     * @var array
     */
    protected $_options = array(
        'adapter' => 'Solarium_Client_Adapter_Http',
    );

    /**
     * Querytype mappings
     */
    protected $_queryTypes = array(
        self::QUERYTYPE_SELECT => array(
            'requestbuilder' => 'Solarium_Client_RequestBuilder_Select',
            'responseparser' => 'Solarium_Client_ResponseParser_Select'
        ),
        self::QUERYTYPE_UPDATE => array(
            'requestbuilder' => 'Solarium_Client_RequestBuilder_Update',
            'responseparser' => 'Solarium_Client_ResponseParser_Update'
        ),
        self::QUERYTYPE_PING => array(
            'requestbuilder' => 'Solarium_Client_RequestBuilder_Ping',
            'responseparser' => 'Solarium_Client_ResponseParser_Ping'
        ),
    );

    /**
     * Registered plugin instances
     * 
     * @var array
     */
    protected $_plugins = array();

    /**
     * Adapter instance
     *
     * If an adapter instance is set using {@link setAdapter()} this var will
     * contain a reference to that instance.
     *
     * In all other cases the adapter is lazy-loading, it will be instantiated
     * on first use by {@link getAdapter()} based on the 'adapter' entry in
     * {@link $_options}. This option can be set using {@link setAdapter()}
     *
     * @var Solarium_Client_Adapter
     */
    protected $_adapter;

    /**
     * Request builder instances
     *
     * @var array
     */
    protected $_requestBuilders;

    /**
     * Set the adapter
     *
     * The adapter has to be a class that extends
     * {@link Solarium_Client_Adapter}.
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
     * @param string|Solarium_Client_Adapter $adapter
     * @return Solarium_Client Provides fluent interface
     */
    public function setAdapter($adapter)
    {
        if (is_string($adapter)) {
            $this->_adapter = null;
            return $this->_setOption('adapter', $adapter);
        } else {
            // forward options
            $adapter->setOptions($this->_options);
            // overwrite existing adapter
            $this->_adapter = $adapter;
            return $this;
        }
    }

    /**
     * Create an adapter instance
     *
     * The 'adapter' entry in {@link $_options} will be used to create an
     * adapter instance. This entry can be the default value of
     * {@link $_options}, a value passed to the constructor or a value set by
     * using {@link setAdapter()}
     *
     * This method is used for lazy-loading the adapter upon first use in
     * {@link getAdapter()}
     *
     * @return void
     */
    protected function _createAdapter()
    {
        $adapterClass = $this->getOption('adapter');
        $this->_adapter = new $adapterClass;
        $this->_adapter->setOptions($this->_options);
    }

    /**
     * Get the adapter instance
     *
     * If {@see $_adapter} doesn't hold an instance a new one will be created by
     * calling {@see _createAdapter()}
     *
     * @return Solarium_Client_Adapter
     */
    public function getAdapter()
    {
        if (null === $this->_adapter) {
            $this->_createAdapter();
        }

        return $this->_adapter;
    }

    /**
     * Register a querytype
     *
     * You can also use this method to override any existing querytype with a new mapping
     *
     * @param string $type
     * @param string $requestBuilder
     * @param string $responseParser
     * @return Solarium_Client Provides fluent interface
     */
    public function registerQueryType($type, $requestBuilder, $responseParser)
    {
        $this->_queryTypes[$type] = array(
            'requestbuilder' => $requestBuilder,
            'responseparser' => $responseParser,
        );

        return $this;
    }

    /**
     * Get all registered querytypes
     * 
     * @return array
     */
    public function getQueryTypes()
    {
        return $this->_queryTypes;
    }

    /**
     * Register a plugin
     *
     * @param string $key
     * @param string $class
     * @param array $options
     * @return Solarium_Client Provides fluent interface
     */
    public function registerPlugin($key, $class, $options = array())
    {
        $plugin = new $class($this, $options);
        if (!($plugin instanceof Solarium_Plugin_Abstract)) {
           throw new Solarium_Exception('All plugins must extend Solarium_Plugin_Abstract');
        }

        $this->_plugins[$key] = $plugin;

        return $this;
    }

    /**
     * Get all registered querytypes
     *
     * @return array
     */
    public function getPlugins()
    {
        return $this->_plugins;
    }

    /**
     * Get a plugin instance
     *
     * @param string $key
     * @return array|null
     */
    public function getPlugin($key)
    {
        if (isset($this->_plugins[$key])) {
            return $this->_plugins[$key];
        } else {
            return null;
        }
    }

    /**
     * Remove a plugin instance
     * 
     * @param string $key
     * @return Solarium_Client Provides fluent interface
     */
    public function removePlugin($key)
    {
        if (isset($this->_plugins[$key])) {
            unset($this->_plugins[$key]);
        }
        return $this;
    }

    /**
     * Forward events to plugins
     *
     * @param string $event
     * @param array $params
     * @param bool $resultOverride
     * @return void|mixed
     */
    protected function _callPlugins($event, $params, $resultOverride = false)
    {
        foreach ($this->_plugins AS $plugin) {
            $result = call_user_func_array(array($plugin, $event), $params);

            if ($result !== null && $resultOverride) {
                return $result;
            }
        }
    }

    /**
     * Creates a request based on a query instance
     *
     * @todo add caching of request builder?
     *
     * @param Solarium_Query $query
     * @return Solarium_Client_Request
     */
    public function createRequest($query)
    {
        $pluginResult = $this->_callPlugins('preCreateRequest', array($query), true);
        if($pluginResult !== null) return $pluginResult;

        $queryType = $query->getType();
        if (!isset($this->_queryTypes[$queryType])) {
            throw new Solarium_Exception('No requestbuilder registered for querytype: '. $queryType);
        }

        $requestBuilderClass = $this->_queryTypes[$queryType]['requestbuilder'];
        $requestBuilder = new $requestBuilderClass;
        $request = $requestBuilder->build($query);

        $this->_callPlugins('postCreateRequest', array($query, $request));

        return $request;
    }

    /**
     * Creates a result object
     *
     * @param Solarium_Query $query
     * @param array Solarium_Client_Response $response
     * @return Solarium_Result
     */
    public function createResult($query, $response)
    {
        $pluginResult = $this->_callPlugins('preCreateResult', array($query, $response), true);
        if($pluginResult !== null) return $pluginResult;

        $resultClass = $query->getResultClass();
        $result = new $resultClass($this, $query, $response);

        $this->_callPlugins('postCreateResult', array($query, $response, $result));

        return $result;
    }

    /**
     * Execute a query
     *
     * @param Solarium_Query
     * @return Solarium_Result
     */
    public function execute($query)
    {
        $pluginResult = $this->_callPlugins('preExecute', array($query), true);
        if($pluginResult !== null) return $pluginResult;

        $request = $this->createRequest($query);
        $response = $this->executeRequest($request);

        $result = $this->createResult($query, $response);

        $this->_callPlugins('postExecute', array($query, $result));

        return $result;
    }
    
    /**
     * Execute a request and return the response
     *
     * @param Solarium_Client_Request
     * @return Solarium_Client_Response
     */
    public function executeRequest($request)
    {
        $pluginResult = $this->_callPlugins('preExecuteRequest', array($request), true);
        if($pluginResult !== null) return $pluginResult;

        $response = $this->getAdapter()->execute($request);

        $this->_callPlugins('postExecuteRequest', array($request, $response));

        return $response;
    }

    /**
     * Execute a ping query
     *
     * Example usage:
     * <code>
     * $client = new Solarium_Client;
     * $query = new Solarium_Query_Ping;
     * $result = $client->ping($query);
     * </code>
     *
     * @see Solarium_Query_Ping
     *
     * @internal This is a convenience method that forwards the query to the
     *  adapter and returns the adapter result, thus allowing for an easy to use
     *  and clean API.
     *
     * @param Solarium_Query_Ping $query
     * @return boolean
     */
    public function ping($query)
    {
        return $this->execute($query);
    }

    /**
     * Execute an update query
     *
     * Example usage:
     * <code>
     * $client = new Solarium_Client;
     * $update = new Solarium_Query_Update;
     * $update->addOptimize();
     * $result = $client->ping($update);
     * </code>
     *
     * @see Solarium_Query_Update
     * @see Solarium_Result_Update
     *
     * @internal This is a convenience method that forwards the query to the
     *  adapter and returns the adapter result, thus allowing for an easy to use
     *  and clean API.
     *
     * @param Solarium_Query_Update $query
     * @return Solarium_Result_Update
     */
    public function update($query)
    {
        return $this->execute($query);
    }

    /**
     * Execute a select query
     *
     * Example usage:
     * <code>
     * $client = new Solarium_Client;
     * $query = new Solarium_Query_Select;
     * $result = $client->ping($query);
     * </code>
     *
     * @see Solarium_Query_Select
     * @see Solarium_Result_Select
     *
     * @internal This is a convenience method that forwards the query to the
     *  adapter and returns the adapter result, thus allowing for an easy to use
     *  and clean API.
     *
     * @param Solarium_Query_Select $query
     * @return Solarium_Result_Select
     */
    public function select($query)
    {
        return $this->execute($query);
    }
}