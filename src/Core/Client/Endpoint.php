<?php

namespace Solarium\Core\Client;

use Solarium\Core\Configurable;

/**
 * Class for describing an endpoint.
 */
class Endpoint extends Configurable
{
    /**
     * Default options.
     *
     * The defaults match a standard Solr example instance as distributed by
     * the Apache Lucene Solr project.
     *
     * @var array
     */
    protected $options = [
        'scheme' => 'http',
        'host' => '127.0.0.1',
        'port' => 8983,
        'path' => '/solr',
        'core' => null,
        'timeout' => 5,
    ];

    /**
     * Magic method enables a object to be transformed to a string.
     *
     * Get a summary showing significant variables in the object
     * note: uri resource is decoded for readability
     *
     * @return string
     */
    public function __toString()
    {
        $output = __CLASS__.'::__toString'."\n".'base uri: '.$this->getCoreBaseUri()."\n".'host: '.$this->getHost()."\n".'port: '.$this->getPort()."\n".'path: '.$this->getPath()."\n".'core: '.$this->getCore()."\n".'timeout: '.$this->getTimeout()."\n".'authentication: '.print_r($this->getAuthentication(), 1);

        return $output;
    }

    /**
     * Get key value.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->getOption('key');
    }

    /**
     * Set key value.
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setKey($value)
    {
        return $this->setOption('key', $value);
    }

    /**
     * Set host option.
     *
     * @param string $host This can be a hostname or an IP address
     *
     * @return self Provides fluent interface
     */
    public function setHost($host)
    {
        return $this->setOption('host', $host);
    }

    /**
     * Get host option.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->getOption('host');
    }

    /**
     * Set port option.
     *
     * @param int $port Common values are 80, 8080 and 8983
     *
     * @return self Provides fluent interface
     */
    public function setPort($port)
    {
        return $this->setOption('port', $port);
    }

    /**
     * Get port option.
     *
     * @return int
     */
    public function getPort()
    {
        return $this->getOption('port');
    }

    /**
     * Set path option.
     *
     * If the path has a trailing slash it will be removed.
     *
     * @param string $path
     *
     * @return self Provides fluent interface
     */
    public function setPath($path)
    {
        if ('/' == substr($path, -1)) {
            $path = substr($path, 0, -1);
        }

        return $this->setOption('path', $path);
    }

    /**
     * Get path option.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getOption('path');
    }

    /**
     * Set core option.
     *
     * @param string $core
     *
     * @return self Provides fluent interface
     */
    public function setCore($core)
    {
        return $this->setOption('core', $core);
    }

    /**
     * Get core option.
     *
     * @return string
     */
    public function getCore()
    {
        return $this->getOption('core');
    }

    /**
     * Set timeout option.
     *
     * @param int $timeout
     *
     * @return self Provides fluent interface
     */
    public function setTimeout($timeout)
    {
        return $this->setOption('timeout', $timeout);
    }

    /**
     * Get timeout option.
     *
     * @return string
     */
    public function getTimeout()
    {
        return $this->getOption('timeout');
    }

    /**
     * Set scheme option.
     *
     * @param string $scheme
     *
     * @return self Provides fluent interface
     */
    public function setScheme($scheme)
    {
        return $this->setOption('scheme', $scheme);
    }

    /**
     * Get scheme option.
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->getOption('scheme');
    }

    /**
     * Get the base url for all requests.
     *
     * Based on host, path, port and core options.
     *
     * @return string
     */
    public function getCoreBaseUri()
    {
        $uri = $this->getServerUri();
        $core = $this->getCore();

        if (!empty($core)) {
            $uri .= $core.'/';
        }

        return $uri;
    }

    /**
     * Get the base url for all requests.
     *
     * Based on host, path, port and core options.
     *
     * @deprecated Please use getCoreBaseUri or getServerUri now, will be removed in Solarium 5
     *
     * @return string
     */
    public function getBaseUri()
    {
        $message = 'Endpoint::getBaseUri is deprecated since Solarium 4.2, will be removed in Solarium 5.'.
            'please use getServerUri or getCoreBaseUri now.';
        @trigger_error($message, E_USER_DEPRECATED);

        return $this->getCoreBaseUri();
    }

    /**
     * Get the server uri, required for non core/collection specific requests.
     *
     * @return string
     */
    public function getServerUri()
    {
        return $this->getScheme().'://'.$this->getHost().':'.$this->getPort().$this->getPath().'/';
    }

    /**
     * Set HTTP basic auth settings.
     *
     * If one or both values are NULL authentication will be disabled
     *
     * @param string $username
     * @param string $password
     *
     * @return self Provides fluent interface
     */
    public function setAuthentication($username, $password)
    {
        $this->setOption('username', $username);
        $this->setOption('password', $password);

        return $this;
    }

    /**
     * Get HTTP basic auth settings.
     *
     * @return array
     */
    public function getAuthentication()
    {
        return [
            'username' => $this->getOption('username'),
            'password' => $this->getOption('password'),
        ];
    }

    /**
     * Initialization hook.
     *
     * In this case the path needs to be cleaned of trailing slashes.
     *
     * @see setPath()
     */
    protected function init()
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'path':
                    $this->setPath($value);
                    break;
            }
        }
    }
}
