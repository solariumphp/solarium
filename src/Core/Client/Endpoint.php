<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Client;

use Solarium\Core\Configurable;
use Solarium\Exception\UnexpectedValueException;

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
        'path' => '/',
        'context' => 'solr',
        'collection' => null,
        'core' => null,
        'leader' => false,
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
        $output = __CLASS__.'::__toString'."\n".'host: '.$this->getHost()."\n".'port: '.$this->getPort()."\n".'path: '.$this->getPath()."\n".'context: '.$this->getContext()."\n".'collection: '.$this->getCollection()."\n".'core: '.$this->getCore()."\n".'authentication: '.print_r($this->getAuthentication() + $this->getAuthorizationToken(), true);

        return $output;
    }

    /**
     * Get key value.
     *
     * @return string|null
     */
    public function getKey(): ?string
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
    public function setKey(string $value): self
    {
        $this->setOption('key', $value);

        return $this;
    }

    /**
     * Set host option.
     *
     * @param string $host This can be a hostname or an IP address
     *
     * @return self Provides fluent interface
     */
    public function setHost(string $host): self
    {
        $this->setOption('host', $host);

        return $this;
    }

    /**
     * Get host option.
     *
     * @return string|null
     */
    public function getHost(): ?string
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
    public function setPort(int $port): self
    {
        $this->setOption('port', $port);

        return $this;
    }

    /**
     * Get port option.
     *
     * @return int|null
     */
    public function getPort(): ?int
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
    public function setPath(string $path): self
    {
        $this->setOption('path', rtrim($path, '/'));

        return $this;
    }

    /**
     * Get path option.
     *
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->getOption('path');
    }

    /**
     * Set context option.
     *
     * If the context has a leading or trailing slash it will be removed.
     *
     * @param string $context
     *
     * @return self Provides fluent interface
     */
    public function setContext(string $context): self
    {
        $this->setOption('context', trim($context, '/'));

        return $this;
    }

    /**
     * Get context option.
     *
     * @return string|null
     */
    public function getContext(): ?string
    {
        return $this->getOption('context');
    }

    /**
     * Set collection option.
     *
     * @param string $collection
     *
     * @return self Provides fluent interface
     */
    public function setCollection(string $collection): self
    {
        $this->setOption('collection', $collection);

        return $this;
    }

    /**
     * Get collection option.
     *
     * @return string|null
     */
    public function getCollection(): ?string
    {
        return $this->getOption('collection');
    }

    /**
     * Set core option.
     *
     * @param string $core
     *
     * @return self Provides fluent interface
     */
    public function setCore(string $core): self
    {
        $this->setOption('core', $core);

        return $this;
    }

    /**
     * Get core option.
     *
     * @return string|null
     */
    public function getCore(): ?string
    {
        return $this->getOption('core');
    }

    /**
     * Set scheme option.
     *
     * @param string $scheme
     *
     * @return self Provides fluent interface
     */
    public function setScheme(string $scheme): self
    {
        $this->setOption('scheme', $scheme);

        return $this;
    }

    /**
     * Get scheme option.
     *
     * @return string|null
     */
    public function getScheme(): ?string
    {
        return $this->getOption('scheme');
    }

    /**
     * Get the V1 base url for all SolrCloud requests.
     *
     * Based on host, path, port and collection options.
     *
     * @throws UnexpectedValueException
     *
     * @return string
     */
    public function getCollectionBaseUri(): string
    {
        $uri = $this->getServerUri();
        $context = $this->getContext();
        $collection = $this->getCollection();

        if ($collection) {
            $uri .= $context.'/'.$collection.'/';
        } else {
            throw new UnexpectedValueException('No collection set.');
        }

        return $uri;
    }

    /**
     * Get the V1 base url for all requests.
     *
     * Based on host, path, port and core options.
     *
     * @throws UnexpectedValueException
     *
     * @return string
     */
    public function getCoreBaseUri(): string
    {
        $uri = $this->getServerUri();
        $context = $this->getContext();
        $core = $this->getCore();

        if ($core) {
            // V1 API
            $uri .= $context.'/'.$core.'/';
        } else {
            throw new UnexpectedValueException('No core set.');
        }

        return $uri;
    }

    /**
     * Get the base url for all V1 API requests.
     *
     * @throws UnexpectedValueException
     *
     * @return string
     */
    public function getBaseUri(): string
    {
        try {
            return $this->getCollectionBaseUri();
        } catch (UnexpectedValueException $e) {
            try {
                return $this->getCoreBaseUri();
            } catch (UnexpectedValueException $e) {
                throw new UnexpectedValueException('Neither collection nor core set.');
            }
        }
    }

    /**
     * Get the base url for all V1 API requests.
     *
     * @throws UnexpectedValueException
     *
     * @return string
     */
    public function getV1BaseUri(): string
    {
        return $this->getServerUri().$this->getContext().'/';
    }

    /**
     * Get the base url for all V2 API requests.
     *
     * @throws UnexpectedValueException
     *
     * @return string
     */
    public function getV2BaseUri(): string
    {
        return $this->getServerUri().'api/';
    }

    /**
     * Get the server uri, required for non core/collection specific requests.
     *
     * @return string
     */
    public function getServerUri(): string
    {
        return $this->getScheme().'://'.$this->getHost().':'.$this->getPort().$this->getPath().'/';
    }

    /**
     * Set HTTP basic auth settings.
     *
     * @param string $username
     * @param string $password
     *
     * @return self Provides fluent interface
     */
    public function setAuthentication(string $username, string $password): self
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
    public function getAuthentication(): array
    {
        return [
            'username' => $this->getOption('username'),
            'password' => $this->getOption('password'),
        ];
    }

    /**
     * Set authorization token.
     *
     * Used for JWT or simple token based authorization.
     *
     * @param string $tokenname
     * @param string $token
     *
     * @return self Provides fluent interface
     */
    public function setAuthorizationToken(string $tokenname, string $token): self
    {
        $this->setOption('tokenname', $tokenname);
        $this->setOption('token', $token);

        return $this;
    }

    /**
     * Get authorization token.
     *
     * @return array
     */
    public function getAuthorizationToken(): array
    {
        return [
            'tokenname' => $this->getOption('tokenname'),
            'token' => $this->getOption('token'),
        ];
    }

    /**
     * If the shard is a leader or not. Only in SolrCloud.
     *
     * @param bool $leader
     *
     * @return self Provides fluent interface
     */
    public function setLeader(bool $leader): self
    {
        $this->setOption('leader', $leader);

        return $this;
    }

    /**
     * If the shard is a leader or not. Only in SolrCloud.
     *
     * @return bool|null
     */
    public function isLeader(): ?bool
    {
        return $this->getOption('leader');
    }

    /**
     * Initialization hook.
     *
     * In this case the path needs to be cleaned of trailing slashes.
     * The context needs to be cleaned of leading and trailing slashes.
     *
     * @see setPath()
     * @see setContext()
     */
    protected function init()
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'path':
                    $this->setPath($value);
                    break;
                case 'context':
                    $this->setContext($value);
                    break;
            }
        }
    }
}
