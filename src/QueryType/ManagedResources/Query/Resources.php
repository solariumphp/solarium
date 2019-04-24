<?php

namespace Solarium\QueryType\ManagedResources\Query;

use Solarium\Core\Client\Client;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\QueryType\ManagedResources\RequestBuilder\Resources as RequestBuilder;
use Solarium\QueryType\ManagedResources\ResponseParser\Resources as ResponseParser;
use Solarium\Core\Query\AbstractQuery;
use Solarium\QueryType\ManagedResources\Result\Resources\ResourceList;

class Resources extends AbstractQuery
{
    /**
     * Fixed name for resources.
     *
     * @var string
     */
    private $name = 'resources';

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'handler' => 'schema/managed',
        'resultclass' => ResourceList::class,
        'omitheader' => true,
    ];

    /**
     * Get the name of resources.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get query type.
     *
     * @return string
     */
    public function getType(): string
    {
        return Client::QUERY_MANAGED_RESOURCES;
    }

    /**
     * Get the request builder class for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder(): RequestBuilderInterface
    {
        return new RequestBuilder();
    }

    /**
     * Get the response parser class for this query.
     *
     * @return ResponseParser
     */
    public function getResponseParser(): ResponseParserInterface
    {
        return new ResponseParser();
    }
}
