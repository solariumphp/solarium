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
 */

/**
 * Loadbalancer plugin
 *
 * Using this plugin you can use software loadbalancing over multiple Solr instances.
 * You can add any number of servers, each with their own weight. The weight influences
 * the probability of a server being used for a query.
 *
 * By default all queries except updates are loadbalanced. This can be customized by setting blocked querytypes.
 * Any querytype that may not be loadbalanced will be executed by Solarium with the default adapter settings.
 * In a master-slave setup the default adapter should be connecting to the master server.
 *
 * You can also enable the failover mode. In this case a query will be retried on another server in case of error.
 *
 * @package Solarium
 * @subpackage Plugin
 */
class Solarium_Plugin_Loadbalancer extends Solarium_Plugin_Abstract
{

    /**
     * Default options
     *
     * @var array
     */
    protected $_options = array(
        'failoverenabled' => false,
        'failovermaxretries' => 1,
    );

    /**
     * Registered servers
     *
     * @var array
     */
    protected $_servers = array();

    /**
     * Query types that are blocked from loadbalancing
     *
     * @var array
     */
    protected $_blockedQueryTypes = array(
        Solarium_Client::QUERYTYPE_UPDATE => true
    );

    /**
     * Key of the last used server
     *
     * The value can be null if no queries have been executed, or if the last executed query didn't use loadbalancing.
     *
     * @var null|string
     */
    protected $_lastServerKey;

    /**
     * Server to use for next query (overrules randomizer)
     *
     * @var string
     */
    protected $_nextServer;

    /**
     * Presets of the client adapter
     *
     * These settings are used to restore the adapter to it's original status for queries
     * that cannot be loadbalanced (for instance update queries that need to go to the master)
     *
     * @var array
     */
    protected $_adapterPresets;

    /**
     * Pool of servers to use for requests
     *
     * @var Solarium_Plugin_Loadbalancer_WeightedRandomChoice
     */
    protected $_randomizer;

    /**
     * Query type
     *
     * @var string
     */
    protected $_queryType;

    /**
     * Used for failover mechanism
     *
     * @var array
     */
    protected $_serverExcludes;

    /**
     * Initialize options
     *
     * Several options need some extra checks or setup work, for these options
     * the setters are called.
     *
     * @return void
     */
    protected function _init()
    {
        foreach ($this->_options AS $name => $value) {
            switch ($name) {
                case 'server':
                    $this->setServers($value);
                    break;
                case 'blockedquerytype':
                    $this->setBlockedQueryTypes($value);
                    break;
            }
        }
    }

    /**
     * Set failover enabled option
     *
     * @param bool $value
     * @return self Provides fluent interface
     */
    public function setFailoverEnabled($value)
    {
        return $this->_setOption('failoverenabled', $value);
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
     * @param int $value
     * @return self Provides fluent interface
     */
    public function setFailoverMaxRetries($value)
    {
        return $this->_setOption('failovermaxretries', $value);
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
     * Add a server to the loadbalacing 'pool'
     *
     * @param string $key
     * @param array $options
     * @param int $weight Must be a positive number
     * @return self Provides fluent interface
     */
    public function addServer($key, $options, $weight = 1)
    {
        if (array_key_exists($key, $this->_servers)) {
            throw new Solarium_Exception('A server for the loadbalancer plugin must have a unique key');
        } else {
            $this->_servers[$key] = array(
                'options' => $options,
                'weight' => $weight,
            );
        }

        // reset the randomizer as soon as a new server is added
        $this->_randomizer = null;

        return $this;
    }

    /**
     * Get servers in the loadbalancing pool
     *
     * @return array
     */
    public function getServers()
    {
        return $this->_servers;
    }

    /**
     * Get a server entry by key
     *
     * @param string $key
     * @return array
     */
    public function getServer($key)
    {
        if (!isset($this->_servers[$key])) {
            throw new Solarium_Exception('Unknown server key');
        }

        return $this->_servers[$key];
    }

    /**
     * Set servers, overwriting any existing servers
     *
     * @param array $servers Use server key as array key and 'options' and 'weight' as array entries
     * @return self Provides fluent interface
     */
    public function setServers($servers)
    {
        $this->clearServers();
        $this->addServers($servers);
        return $this;
    }

    /**
     * Add multiple servers
     *
     * @param array $servers
     * @return self Provides fluent interface
     */
    public function addServers($servers)
    {
        foreach ($servers AS $key => $data) {
            $this->addServer($key, $data['options'], $data['weight']);
        }

        return $this;
    }

    /**
     * Clear all server entries
     *
     * @return self Provides fluent interface
     */
    public function clearServers()
    {
        $this->_servers = array();
    }

    /**
     * Remove a server by key
     *
     * @param string $key
     * @return self Provides fluent interface
     */
    public function removeServer($key)
    {
        if (isset($this->_servers[$key])) {
            unset($this->_servers[$key]);
        }

        return $this;
    }

    /**
     * Set a forced server (by key) for the next request
     *
     * As soon as one query has used the forced server this setting is reset. If you want to remove this setting
     * pass NULL as the key value.
     *
     * If the next query cannot be loadbalanced (for instance based on the querytype) this setting is ignored
     * but will still be reset.
     *
     * @param string|null $key
     * @return self Provides fluent interface
     */
    public function setForcedServerForNextQuery($key)
    {
        if ($key !== null && !array_key_exists($key, $this->_servers)) {
            throw new Solarium_Exception('Unknown server forced for next query');
        }

        $this->_nextServer = $key;
        return $this;
    }

    /**
     * Get the ForcedServerForNextQuery value
     *
     * @return string|null
     */
    public function getForcedServerForNextQuery()
    {
        return $this->_nextServer;
    }

    /**
     * Get an array of blocked querytypes
     *
     * @return array
     */
    public function getBlockedQueryTypes()
    {
        return array_keys($this->_blockedQueryTypes);
    }

    /**
     * Set querytypes to block from loadbalancing
     *
     * Overwrites any existing types
     *
     * @param array $types Use an array with the constants defined in Solarium_Client as values
     * @return self Provides fluent interface
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
     * @param string $type Use one of the constants defined in Solarium_Client
     * @return self Provides fluent interface
     */
    public function addBlockedQueryType($type)
    {
        if (!array_key_exists($type, $this->_blockedQueryTypes)) {
            $this->_blockedQueryTypes[$type] = true;
        }

        return $this;
    }

    /**
     * Add querytypes to block from loadbalancing
     *
     * Appended to any existing types
     *
     * @param array $types Use an array with the constants defined in Solarium_Client as values
     * @return self Provides fluent interface
     */
    public function addBlockedQueryTypes($types)
    {
        foreach ($types AS $type) {
            $this->addBlockedQueryType($type);
        }
    }

    /**
     * Remove a single querytype from the block list
     *
     * @param string $type
     * @return void
     */
    public function removeBlockedQueryType($type)
    {
        if (array_key_exists($type, $this->_blockedQueryTypes)) {
            unset($this->_blockedQueryTypes[$type]);
        }
    }

    /**
     * Clear all blocked querytypes
     *
     * @return self Provides fluent interface
     */
    public function clearBlockedQueryTypes()
    {
        $this->_blockedQueryTypes = array();
    }

    /**
     * Get the key of the server that was used for the last query
     *
     * May return a null value if no query has been executed yet, or the last query could not be loadbalanced.
     *
     * @return null|string
     */
    public function getLastServerKey()
    {
        return $this->_lastServerKey;
    }

    /**
     * Event hook to capture querytype
     *
     * @param Solarium_Query $query
     * @return void
     */
    public function preCreateRequest($query)
    {
        $this->_queryType = $query->getType();
    }

    /**
     * Event hook to adjust client settings just before query execution
     *
     * @param Solarium_Client_Request $request
     * @return Solarium_Client_Response
     */
    public function preExecuteRequest($request)
    {
        $adapter = $this->_client->getAdapter();

        // save adapter presets (once) to allow the settings to be restored later
        if ($this->_adapterPresets == null) {
            $this->_adapterPresets = array(
                'host'    => $adapter->getHost(),
                'port'    => $adapter->getPort(),
                'path'    => $adapter->getPath(),
                'core'    => $adapter->getCore(),
                'timeout' => $adapter->getTimeout(),
            );
        }

        // check querytype: is loadbalancing allowed?
        if (!array_key_exists($this->_queryType, $this->_blockedQueryTypes)) {
            return  $this->_getLoadbalancedResponse($request);
        } else {
            $options = $this->_adapterPresets;
            $this->_lastServerKey = null;

            // apply new settings to adapter
            $adapter->setOptions($options);

            // execute request and return result
            return $adapter->execute($request);
        }
    }

    /**
     * Execute a request using the adapter
     *
     * @param Solarium_Client_Request $request
     * @return Solarium_Client_Response $response
     */
    protected function _getLoadbalancedResponse($request)
    {
        $this->_serverExcludes = array(); // reset for each query
        $adapter = $this->_client->getAdapter();

        if ($this->getFailoverEnabled() == true) {

            for ($i=0; $i<=$this->getFailoverMaxRetries(); $i++) {
                $options = $this->_getRandomServerOptions();
                $adapter->setOptions($options);
                try {
                    return $adapter->execute($request);
                } catch(Solarium_Client_HttpException $e) {
                    // ignore HTTP errors and try again
                    // but do issue an event for things like logging
                    $e = new Solarium_Exception('Maximum number of loadbalancer retries reached');
                    $this->_client->triggerEvent('LoadbalancerServerFail', array($options, $e));
                }
            }

            // if we get here no more retries available, throw exception
            $e = new Solarium_Exception('Maximum number of loadbalancer retries reached');
            throw $e;

        } else {
            // no failover retries, just execute and let an exception bubble upwards
            $options = $this->_getRandomServerOptions();
            $adapter->setOptions($options);
            return $adapter->execute($request);
        }
    }

    /**
     * Get options array for a randomized server
     *
     * @return array
     */
    protected function _getRandomServerOptions()
    {
        // determine the server to use
        if ($this->_nextServer !== null) {
            $serverKey = $this->_nextServer;
            // reset forced server directly after use
            $this->_nextServer = null;
        } else {
            $serverKey = $this->_getRandomizer()->getRandom($this->_serverExcludes);
        }

        $this->_serverExcludes[] = $serverKey;
        $this->_lastServerKey = $serverKey;
        return $this->_servers[$serverKey]['options'];
    }

    /**
     * Get randomizer instance
     *
     * @return Solarium_Plugin_Loadbalancer_WeightedRandomChoice
     */
    protected function _getRandomizer()
    {
        if ($this->_randomizer === null) {
            $choices = array();
            foreach ($this->_servers AS $key => $settings) {
                $choices[$key] = $settings['weight'];
            }
            $this->_randomizer = new Solarium_Plugin_Loadbalancer_WeightedRandomChoice($choices);
        }

        return $this->_randomizer;
    }

}