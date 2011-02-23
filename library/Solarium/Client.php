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
     * Default options
     *
     * The defaults match a standard Solr example instance as distributed by
     * the Apache Lucene Solr project.
     *
     * @var array
     */
    protected $_options = array(
        'host'    => '127.0.0.1',
        'port'    => 8983,
        'path'    => '/solr',
        'core'    => null,
        'adapter' => 'Solarium_Client_Adapter_Stream',
    );

    /**
     * Adapter instance
     *
     * The adapter is lazy-loading, it will be instantiated on first use by
     * {@link getAdapter()} based on the 'adapter' entry in {@link $_options}.
     * This options can be set using {@link setAdapter()}
     *
     * @var Solarium_Client_Adapter
     */
    protected $_adapter;

    /**
     * {@inheritdoc}
     *
     * In this case the path needs to be cleaned of trailing slashes.
     * @see setPath()
     */
    protected function _init()
    {
        foreach ($this->_options AS $name => $value) {
            switch ($name) {
                case 'path':
                    $this->setPath($value);
                    break;
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * If any option of this client is changed after the adapter has been
     * instantiated the change is propagated to the adapter. This allows for
     * switching the client to another core for a second query, for instance.
     */
    protected function _setOption($name, $value)
    {
        parent::_setOption($name, $value);

        if (null !== $this->_adapter) {
            $this->_adapter->setOptions($this->_options);
        }

        return $this;
    }

    /**
     * Set host option
     *
     * @param string $host This can be a hostname or an IP address
     * @return Solarium_Client Provides fluent interface
     */
    public function setHost($host)
    {
        return $this->_setOption('host', $host);
    }

    /**
     * Get host option
     *
     * @return string
     */
    public function getHost()
    {
        return $this->getOption('host');
    }

    /**
     * Set port option
     *
     * @param int $port Common values are 80, 8080 and 8983
     * @return Solarium_Client Provides fluent interface
     */
    public function setPort($port)
    {
        return $this->_setOption('port', $port);
    }

    /**
     * Get port option
     *
     * @return int
     */
    public function getPort()
    {
        return $this->getOption('port');
    }

    /**
     * Set path option
     *
     * If the path has a trailing slash it will be removed.
     *
     * @param string $path
     * @return Solarium_Client Provides fluent interface
     */
    public function setPath($path)
    {
        if (substr($path, -1) == '/') $path = substr($path, 0, -1);

        return $this->_setOption('path', $path);
    }

    /**
     * Get path option
     *
     * @return void
     */
    public function getPath()
    {
        return $this->getOption('path');
    }

    /**
     * Set core option
     *
     * @param string $core
     * @return Solarium_Client Provides fluent interface
     */
    public function setCore($core)
    {
        return $this->_setOption('core', $core);
    }

    /**
     * Get core option
     *
     * @return string
     */
    public function getCore()
    {
        return $this->getOption('core');
    }

    /**
     * Set the adapter
     *
     * The adapter has to be a class that extends Solarium_Client_Adapter.
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
     * Execute a ping query
     *
     * This is a convenience method that forwards the query to the adapter and
     * returns the adapter result, thus allowing for an easy to use and clean
     * API.
     *
     * @param Solarium_Query_Ping $query
     * @return Solarium_Result_Ping
     */
    public function ping($query)
    {
        return $this->getAdapter()->ping($query);
    }

    /**
     * Execute an update query
     *
     * This is a convenience method that forwards the query to the adapter and
     * returns the adapter result, thus allowing for an easy to use and clean
     * API.
     *
     * @param Solarium_Query_Update $query
     * @return Solarium_Result_Select
     */
    public function update($query)
    {
        return $this->getAdapter()->update($query);
    }

    /**
     * Execute a select query
     *
     * This is a convenience method that forwards the query to the adapter and
     * returns the adapter result, thus allowing for an easy to use and clean
     * API.
     *
     * @param Solarium_Query_Select $query
     * @return Solarium_Result_Select
     */
    public function select($query)
    {
        return $this->getAdapter()->select($query);
    }
}