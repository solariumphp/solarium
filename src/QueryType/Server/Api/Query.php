<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Api;

use Solarium\Core\Client\Client;
use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;

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
        'version' => Request::API_V1,
        'method' => Request::METHOD_GET,
        'resultclass' => Result::class,
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
    public function setVersion(string $version): self
    {
        $this->setOption('version', $version);

        return $this;
    }

    /**
     * Get version option.
     *
     * @return string|null
     */
    public function getVersion(): ?string
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
    public function setMethod(string $method): self
    {
        $this->setOption('method', $method);

        return $this;
    }

    /**
     * Get method option.
     *
     * @return string|null
     */
    public function getMethod(): ?string
    {
        return $this->getOption('method');
    }

    /**
     * Set accept option.
     *
     * @param string $accept
     *
     * @return self Provides fluent interface
     */
    public function setAccept(string $accept): self
    {
        $this->setOption('accept', $accept);

        return $this;
    }

    /**
     * Get accept option.
     *
     * @return string|null
     */
    public function getAccept(): ?string
    {
        return $this->getOption('accept');
    }

    /**
     * Set request Content-Type.
     *
     * Use one of the Request::CONTENT_TYPE_* constants as value.
     *
     * Content-Type parameters can be passed in $params or set with {@see setContentTypeParams()}.
     *
     * @param string|null $contentType
     * @param array|null  $params
     *
     * @return self Provides fluent interface
     */
    public function setContentType(?string $contentType, ?array $params = null): self
    {
        $this->setOption('contenttype', $contentType);

        if (null !== $params) {
            $this->setOption('contenttypeparams', $params);
        }

        return $this;
    }

    /**
     * Get request Content-Type.
     *
     * @return string|null
     */
    public function getContentType(): ?string
    {
        return $this->getOption('contenttype');
    }

    /**
     * Set Content-Type parameters.
     *
     * @param array|null $params
     *
     * @return self Provides fluent interface
     */
    public function setContentTypeParams(?array $params): self
    {
        $this->setOption('contenttypeparams', $params);

        return $this;
    }

    /**
     * Get Content-Type parameters.
     *
     * @return array|null
     */
    public function getContentTypeParams(): ?array
    {
        return $this->getOption('contenttypeparams');
    }

    /**
     * Set rawdata option.
     *
     * @param string $rawData
     *
     * @return self Provides fluent interface
     */
    public function setRawData(string $rawData): self
    {
        $this->setOption('rawdata', $rawData);

        return $this;
    }

    /**
     * Get method option.
     *
     * @return string|null
     */
    public function getRawData(): ?string
    {
        return $this->getOption('rawdata');
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder(): RequestBuilderInterface
    {
        return new RequestBuilder();
    }

    /**
     * Get a response parser for this query.
     *
     * @return ResponseParser
     */
    public function getResponseParser(): ResponseParserInterface
    {
        return new ResponseParser();
    }
}
