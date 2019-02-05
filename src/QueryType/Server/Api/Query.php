<?php

namespace Solarium\QueryType\Server\Api;

use Solarium\Core\Client\Client;
use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\Result\QueryType;

/**
 * V2 API query.
 */
class Query extends AbstractQuery
{
    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'version' => Request::API_V2,
        'method' => Request::METHOD_GET,
        'resultclass' => QueryType::class,
    ];

    /**
     * Get type for this query.
     *
     * @return string
     */
    public function getType(): string
    {
        return Client::QUERY_API;
    }

    /**
     * Set version option.
     *
     * @param string $version
     *
     * @return self Provides fluent interface
     */
    public function setVersion($version): self
    {
        $this->setOption('version', $version);
        return $this;
    }

    /**
     * Get version option.
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->getOption('version');
    }

    /**
     * Set method option.
     *
     * @param string $method
     *
     * @return self Provides fluent interface
     */
    public function setMethod($method): self
    {
        $this->setOption('method', $method);
        return $this;
    }

    /**
     * Get method option.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->getOption('method');
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder(): RequestBuilder
    {
        return new RequestBuilder();
    }

    /**
     * Get a response parser for this query.
     *
     * @return ResponseParser
     */
    public function getResponseParser(): ResponseParser
    {
        return new ResponseParser();
    }
}
