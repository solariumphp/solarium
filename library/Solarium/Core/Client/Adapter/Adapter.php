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
 * @subpackage Core
 */

/**
 * @namespace
 */
namespace Solarium\Core\Client\Adapter;
use Solarium\Core\Configurable;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;

/**
 * Base class for all adapters
 *
 * The goal of an adapter is to accept a query, execute it and return the right
 * result object. This is actually quite a complex task as it involves the
 * handling of all Solr communication.
 *
 * The adapter structure allows for varying implementations of this task.
 *
 * Most adapters will use some sort of HTTP client. In that case the
 * Solarium\Client\Request request builders and Solarium\Client\Response
 * response parsers can be used to simplify HTTP communication.
 * See {@link Solarium\Client\Adapter\Http} as an example.
 *
 * However an adapter may also implement all logic by itself if needed.
 *
 * @package Solarium
 * @subpackage Core
 */
abstract class Adapter extends Configurable
{
    /**
     * Default options
     *
     * The defaults match a standard Solr example instance as distributed by
     * the Apache Lucene Solr project.
     *
     * @var array
     */
    protected $options = array(
        'host'    => '127.0.0.1',
        'port'    => 8983,
        'path'    => '/solr',
        'core'    => null,
        'timeout' => 5,
    );

    /**
     * Initialization hook
     *
     * In this case the path needs to be cleaned of trailing slashes.
     * @see setPath()
     */
    protected function init()
    {
        foreach ($this->options AS $name => $value) {
            switch ($name) {
                case 'path':
                    $this->setPath($value);
                    break;
            }
        }
    }

    /**
     * Set host option
     *
     * @param string $host This can be a hostname or an IP address
     * @return self Provides fluent interface
     */
    public function setHost($host)
    {
        return $this->setOption('host', $host);
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
     * @return self Provides fluent interface
     */
    public function setPort($port)
    {
        return $this->setOption('port', $port);
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
     * @return self Provides fluent interface
     */
    public function setPath($path)
    {
        if (substr($path, -1) == '/') $path = substr($path, 0, -1);

        return $this->setOption('path', $path);
    }

    /**
     * Get path option
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getOption('path');
    }

    /**
     * Set core option
     *
     * @param string $core
     * @return self Provides fluent interface
     */
    public function setCore($core)
    {
        return $this->setOption('core', $core);
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
     * Set timeout option
     *
     * @param int $timeout
     * @return self Provides fluent interface
     */
    public function setTimeout($timeout)
    {
        return $this->setOption('timeout', $timeout);
    }

    /**
     * Get timeout option
     *
     * @return string
     */
    public function getTimeout()
    {
        return $this->getOption('timeout');
    }

    /**
     * Execute a request
     *
     * Abstract method to require an implementation inside all adapters.
     *
     * @abstract
     * @param Request $request
     * @return Response
     */
    abstract public function execute($request);

    /**
     * Get the base url for all requests
     *
     * Based on host, path, port and core options.
     *
     * @return string
     */
    public function getBaseUri()
    {
        $uri = 'http://' . $this->getHost() . ':' . $this->getPort() . $this->getPath() . '/';

        $core = $this->getCore();
        if (!empty($core)) {
            $uri .= $core.'/';
        }

        return $uri;
    }
}