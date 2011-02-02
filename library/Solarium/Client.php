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
 */

/**
 * The Solarium Client is the main accesspoint for interaction with Solr
 */
class Solarium_Client extends Solarium_Configurable
{

    /**
     * Default options
     *
     * @var array
     */
    protected $_options = array(
        'host'    => '127.0.0.1',
        'port'    => 80,
        'path'    => '/solr',
        'core'    => null,
        'adapter' => 'Solarium_Client_Adapter_Stream',
    );

    /**
     * Adapter instance
     *
     * @var Solarium_Client_Adapter
     */
    protected $_adapter;

    /**
     * Init options array. Some options might need some extra checks or setup
     * work.
     *
     * @return void
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
     * Set an option
     *
     * @param string $name
     * @param mixed $value
     * @return void
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
     * @param string $host
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
     * @param int $port
     * @return Solarium_Client Provides fluent interface
     */
    public function setPort($port)
    {
        return $this->_setOption('port', $port);
    }

    /**
     * Get port option
     * @return int
     */
    public function getPort()
    {
        return $this->getOption('port');
    }

    /**
     * Set path option
     *
     * @param string $path
     * @return Solarium_Client Provides fluent interface
     */
    public function setPath($path)
    {
        // remove trailing slashes
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
     * @param string|Solarium_Client_Adapter $adapter
     * @return Solarium_Client Provides fluent interface
     */
    public function setAdapter($adapter)
    {
        if (is_string($adapter)) {
            $adapter = new $adapter;
        }
        
        $adapter->setOptions($this->_options);
        $this->_adapter = $adapter;
        return $this;
    }

    /**
     * Get the adapter instance
     *
     * @return Solarium_Client_Adapter
     */
    public function getAdapter()
    {
        if (null === $this->_adapter) {
            $this->setAdapter($this->_options['adapter']);
        }

        return $this->_adapter;
    }

    /**
     * Execute a ping query
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
     * @param Solarium_Query_Select $query
     * @return Solarium_Result_Select
     */
    public function select($query)
    {
        return $this->getAdapter()->select($query);
    }
}