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
 *
 * @package Solarium
 * @subpackage Client
 */

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
 * $client = new Solarium_Client;
 * $query = $client->createSelect();
 * $result = $client->select($query);
 * </code>
 *
 * @package Solarium
 * @subpackage Client
 */
class Solarium_Client extends Solarium_Configurable
{

    /**
     * Querytype select
     */
    const QUERYTYPE_SELECT = 'select';

    /**
     * Querytype update
     */
    const QUERYTYPE_UPDATE = 'update';

    /**
     * Querytype ping
     */
    const QUERYTYPE_PING = 'ping';

    /**
     * Querytype morelikethis
     */
    const QUERYTYPE_MORELIKETHIS = 'mlt';

    /**
     * Querytype analysis field
     */
    const QUERYTYPE_ANALYSIS_FIELD = 'analysis-field';

    /**
     * Querytype analysis document
     */
    const QUERYTYPE_ANALYSIS_DOCUMENT = 'analysis-document';

    /**
     * Querytype terms
     */
    const QUERYTYPE_TERMS = 'terms';

    /**
     * Querytype suggester
     */
    const QUERYTYPE_SUGGESTER = 'suggester';

    /**
     * Default options
     *
     * @var array
     */
    protected $_options = array(
        'adapter' => 'Solarium_Client_Adapter_Http',
    );

    /**
     * Querytype mappings
     *
     * These can be customized using {@link registerQueryType()}
     */
    protected $_queryTypes = array(
        self::QUERYTYPE_SELECT => array(
            'query'          => 'Solarium_Query_Select',
            'requestbuilder' => 'Solarium_Client_RequestBuilder_Select',
            'responseparser' => 'Solarium_Client_ResponseParser_Select'
        ),
        self::QUERYTYPE_UPDATE => array(
            'query'          => 'Solarium_Query_Update',
            'requestbuilder' => 'Solarium_Client_RequestBuilder_Update',
            'responseparser' => 'Solarium_Client_ResponseParser_Update'
        ),
        self::QUERYTYPE_PING => array(
            'query'          => 'Solarium_Query_Ping',
            'requestbuilder' => 'Solarium_Client_RequestBuilder_Ping',
            'responseparser' => 'Solarium_Client_ResponseParser_Ping'
        ),
        self::QUERYTYPE_MORELIKETHIS => array(
            'query'           => 'Solarium_Query_MoreLikeThis',
            'requestbuilder'  => 'Solarium_Client_RequestBuilder_MoreLikeThis',
            'responseparser'  => 'Solarium_Client_ResponseParser_MoreLikeThis'
        ),
        self::QUERYTYPE_ANALYSIS_DOCUMENT => array(
            'query'          => 'Solarium_Query_Analysis_Document',
            'requestbuilder' => 'Solarium_Client_RequestBuilder_Analysis_Document',
            'responseparser' => 'Solarium_Client_ResponseParser_Analysis_Document'
        ),
        self::QUERYTYPE_ANALYSIS_FIELD => array(
            'query'          => 'Solarium_Query_Analysis_Field',
            'requestbuilder' => 'Solarium_Client_RequestBuilder_Analysis_Field',
            'responseparser' => 'Solarium_Client_ResponseParser_Analysis_Field'
        ),
        self::QUERYTYPE_TERMS => array(
            'query'          => 'Solarium_Query_Terms',
            'requestbuilder' => 'Solarium_Client_RequestBuilder_Terms',
            'responseparser' => 'Solarium_Client_ResponseParser_Terms'
        ),
        self::QUERYTYPE_SUGGESTER => array(
            'query'          => 'Solarium_Query_Suggester',
            'requestbuilder' => 'Solarium_Client_RequestBuilder_Suggester',
            'responseparser' => 'Solarium_Client_ResponseParser_Suggester'
        ),
    );

    /**
     * Plugin types
     *
     * @var array
     */
    protected $_pluginTypes = array(
        'loadbalancer' => 'Solarium_Plugin_Loadbalancer',
        'postbigrequest' => 'Solarium_Plugin_PostBigRequest',
        'customizerequest' => 'Solarium_Plugin_CustomizeRequest',
        'parallelexecution' => 'Solarium_Plugin_ParallelExecution',
        'bufferedadd' => 'Solarium_Plugin_BufferedAdd',
        'prefetchiterator' => 'Solarium_Plugin_PrefetchIterator',
    );

    /**
     * Registered plugin instances
     *
     * @var array
     */
    protected $_pluginInstances = array();

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
     * Initialization hook
     */
    protected function _init()
    {
        foreach ($this->_options AS $name => $value) {
            switch ($name) {
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
        $this->_adapter->setOptions($this->getOption('adapteroptions'));
    }

    /**
     * Get the adapter instance
     *
     * If {@see $_adapter} doesn't hold an instance a new one will be created by
     * calling {@see _createAdapter()}
     *
     * @param boolean $autoload
     * @return Solarium_Client_Adapter
     */
    public function getAdapter($autoload = true)
    {
        if (null === $this->_adapter && $autoload) {
            $this->_createAdapter();
        }

        return $this->_adapter;
    }

    /**
     * Register a querytype
     *
     * You can also use this method to override any existing querytype with a new mapping.
     * This requires the availability of the classes through autoloading or a manual
     * require before calling this method.
     *
     * @param string $type
     * @param string $query
     * @param string|object $requestBuilder
     * @param string|object $responseParser
     * @return Solarium_Client Provides fluent interface
     */
    public function registerQueryType($type, $query, $requestBuilder, $responseParser)
    {
        $this->_queryTypes[$type] = array(
            'query' => $query,
            'requestbuilder' => $requestBuilder,
            'responseparser' => $responseParser,
        );

        return $this;
    }

    /**
     * Register multiple querytypes
     *
     * @param array $queryTypes
     * @return Solarium_Client Provides fluent interface
     */
    public function registerQueryTypes($queryTypes)
    {
        foreach ($queryTypes as $type => $queryType) {

            if (!isset($queryType['type'])) $queryType['type'] = $type;

            $this->registerQueryType(
                $queryType['type'],
                $queryType['query'],
                $queryType['requestbuilder'],
                $queryType['responseparser']
            );
        }
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
     * You can supply a plugin instance or a plugin classname as string.
     * This requires the availability of the class through autoloading
     * or a manual require.
     *
     * @param string $key
     * @param string|Solarium_Plugin_Abstract $plugin
     * @param array $options
     * @return Solarium_Client Provides fluent interface
     */
    public function registerPlugin($key, $plugin, $options = array())
    {
        if (is_string($plugin)) {
            $plugin = new $plugin;
        }

        if (!($plugin instanceof Solarium_Plugin_Abstract)) {
           throw new Solarium_Exception('All plugins must extend Solarium_Plugin_Abstract');
        }

        $plugin->init($this, $options);

        $this->_pluginInstances[$key] = $plugin;

        return $this;
    }

    /**
     * Register multiple plugins
     *
     * @param array $plugins
     * @return Solarium_Client Provides fluent interface
     */
    public function registerPlugins($plugins)
    {
        foreach ($plugins as $key => $plugin) {

            if (!isset($plugin['key'])) $plugin['key'] = $key;

            $this->registerPlugin(
                $plugin['key'],
                $plugin['plugin'],
                $plugin['options']
            );
        }

        return $this;
    }

    /**
     * Get all registered plugins
     *
     * @return array
     */
    public function getPlugins()
    {
        return $this->_pluginInstances;
    }

    /**
     * Get a plugin instance
     *
     * @param string $key
     * @param boolean $autocreate
     * @return Solarium_Plugin_Abstract|null
     */
    public function getPlugin($key, $autocreate = true)
    {
        if (isset($this->_pluginInstances[$key])) {
            return $this->_pluginInstances[$key];
        } elseif ($autocreate) {
            if (array_key_exists($key, $this->_pluginTypes)) {
                $this->registerPlugin($key, $this->_pluginTypes[$key]);
                return $this->_pluginInstances[$key];
            } else {
                throw new Solarium_Exception('Cannot autoload plugin of unknown type: ' . $key);
            }
        } else {
            return null;
        }
    }

    /**
     * Remove a plugin instance
     *
     * You can remove a plugin by passing the plugin key, or the plugin instance
     *
     * @param string|Solarium_Plugin_Abstract $plugin
     * @return Solarium_Client Provides fluent interface
     */
    public function removePlugin($plugin)
    {
        if (is_object($plugin)) {
            foreach ($this->_pluginInstances as $key => $instance) {
                if ($instance === $plugin) {
                    unset($this->_pluginInstances[$key]);
                    break;
                }
            }
        } else {
            if (isset($this->_pluginInstances[$plugin])) {
                unset($this->_pluginInstances[$plugin]);
            }
        }
        return $this;
    }

    /**
     * Trigger external events for plugins
     *
     * This methods adds 'namespacing' to the event name to prevent conflicts with Solariums internal event keys.
     *
     * Based on the event name you can always tell if an event was internal (Solarium base classes)
     * or external (plugins, even if it's a plugin included with Solarium).
     *
     * External events always have the 'event' prefix in the event name.
     *
     * @param string $event
     * @param array $params
     * @param bool $resultOverride
     * @return void|mixed
     */
    public function triggerEvent($event, $params = array(), $resultOverride = false)
    {
        // Add namespacing
        $event = 'event'.$event;

        return $this->_callPlugins($event, $params, $resultOverride);
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
        foreach ($this->_pluginInstances AS $plugin) {
            if (method_exists($plugin, $event)) {
                $result = call_user_func_array(array($plugin, $event), $params);

                if ($result !== null && $resultOverride) {
                    return $result;
                }
            }
        }
    }

    /**
     * Creates a request based on a query instance
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

        $requestBuilder = $this->_queryTypes[$queryType]['requestbuilder'];
        if (is_string($requestBuilder)) {
            $requestBuilder = new $requestBuilder;
        }
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
        if ($pluginResult !== null) {
            $response = $pluginResult; //a plugin result overrules the standard execution result
        } else {
            $response = $this->getAdapter()->execute($request);
        }

        $this->_callPlugins('postExecuteRequest', array($request, $response));

        return $response;
    }

    /**
     * Execute a ping query
     *
     * Example usage:
     * <code>
     * $client = new Solarium_Client;
     * $query = $client->createPing();
     * $result = $client->ping($query);
     * </code>
     *
     * @see Solarium_Query_Ping
     *
     * @internal This is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API.
     *
     * @param Solarium_Query_Ping $query
     * @return Solarium_Result_Ping
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
     * $query = $client->createUpdate();
     * $update->addOptimize();
     * $result = $client->update($update);
     * </code>
     *
     * @see Solarium_Query_Update
     * @see Solarium_Result_Update
     *
     * @internal This is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API.
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
     * $query = $client->createSelect();
     * $result = $client->select($query);
     * </code>
     *
     * @see Solarium_Query_Select
     * @see Solarium_Result_Select
     *
     * @internal This is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API.
     *
     * @param Solarium_Query_Select $query
     * @return Solarium_Result_Select
     */
    public function select($query)
    {
        return $this->execute($query);
    }

    /**
     * Execute a MoreLikeThis query
     *
     * Example usage:
     * <code>
     * $client = new Solarium_Client;
     * $query = $client->createMoreLikeThis();
     * $result = $client->moreLikeThis($query);
     * </code>
     *
     * @see Solarium_Query_MoreLikeThis
     * @see Solarium_Result_MoreLikeThis
     *
     * @internal This is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API.
     *
     * @param Solarium_Query_MoreLikeThis $query
     * @return Solarium_Result_MoreLikeThis
     */
    public function moreLikeThis($query)
    {
        return $this->execute($query);
    }

    /**
     * Execute an analysis query
     *
     * @internal This is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API.
     *
     * @param Solarium_Query_Analysis_Document|Solarium_Query_Analysis_Field $query
     * @return Solarium_Result_Analysis_Document|Solarium_Result_Analysis_Field
     */
    public function analyze($query)
    {
        return $this->execute($query);
    }

    /**
     * Execute a terms query
     *
     * @internal This is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API.
     *
     * @param Solarium_Query_Terms $query
     * @return Solarium_Result_Terms
     */
    public function terms($query)
    {
        return $this->execute($query);
    }

    /**
     * Execute a suggester query
     *
     * @internal This is a convenience method that forwards the query to the
     *  execute method, thus allowing for an easy to use and clean API.
     *
     * @param Solarium_Query_Suggester $query
     * @return Solarium_Result_Suggester
     */
    public function suggester($query)
    {
        return $this->execute($query);
    }

    /**
     * Create a query instance
     *
     * @param string $type
     * @param array $options
     * @return Solarium_Query
     */
    public function createQuery($type, $options = null)
    {
        $type = strtolower($type);

        $pluginResult = $this->_callPlugins('preCreateQuery', array($type, $options), true);
        if($pluginResult !== null) return $pluginResult;

        if (!isset($this->_queryTypes[$type])) {
            throw new Solarium_Exception('Unknown querytype: '. $type);
        }

        $class = $this->_queryTypes[$type]['query'];
        $query = new $class($options);

        $this->_callPlugins('postCreateQuery', array($type, $options, $query));

        return $query;
    }

    /**
     * Create a select query instance
     *
     * @param mixed $options
     * @return Solarium_Query_Select
     */
    public function createSelect($options = null)
    {
        return $this->createQuery(self::QUERYTYPE_SELECT, $options);
    }

    /**
     * Create a MoreLikeThis query instance
     *
     * @param mixed $options
     * @return Solarium_Query_MoreLikeThis
     */
    public function createMoreLikeThis($options = null)
    {
        return $this->createQuery(self::QUERYTYPE_MORELIKETHIS, $options);
    }

    /**
     * Create an update query instance
     *
     * @param mixed $options
     * @return Solarium_Query_Update
     */
    public function createUpdate($options = null)
    {
        return $this->createQuery(self::QUERYTYPE_UPDATE, $options);
    }

    /**
     * Create a ping query instance
     *
     * @param mixed $options
     * @return Solarium_Query_Ping
     */
    public function createPing($options = null)
    {
        return $this->createQuery(self::QUERYTYPE_PING, $options);
    }

    /**
     * Create an analysis field query instance
     *
     * @param mixed $options
     * @return Solarium_Query_Analysis_Field
     */
    public function createAnalysisField($options = null)
    {
        return $this->createQuery(self::QUERYTYPE_ANALYSIS_FIELD, $options);
    }

    /**
     * Create an analysis document query instance
     *
     * @param mixed $options
     * @return Solarium_Query_Analysis_Document
     */
    public function createAnalysisDocument($options = null)
    {
        return $this->createQuery(self::QUERYTYPE_ANALYSIS_DOCUMENT, $options);
    }

    /**
     * Create a terms query instance
     *
     * @param mixed $options
     * @return Solarium_Query_Terms
     */
    public function createTerms($options = null)
    {
        return $this->createQuery(self::QUERYTYPE_TERMS, $options);
    }

    /**
     * Create a suggester query instance
     *
     * @param mixed $options
     * @return Solarium_Query_Suggester
     */
    public function createSuggester($options = null)
    {
        return $this->createQuery(self::QUERYTYPE_SUGGESTER, $options);
    }
}
