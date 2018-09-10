<?php

namespace Solarium\QueryType\ManagedResources\Query;

use Solarium\Core\Client\Client;
use Solarium\QueryType\ManagedResources\RequestBuilder\Resources as RequestBuilder;
use Solarium\QueryType\ManagedResources\ResponseParser\Resources as ResponseParser;
use Solarium\Core\Query\AbstractQuery;

class Resources extends AbstractQuery {

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'handler' => 'schema/managed',
        'resultclass' => 'Solarium\QueryType\ManagedResources\Result\Resources\ResourceList',
        'omitheader' => true,
    ];

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
    public function getRequestBuilder(): RequestBuilder
    {
        return new RequestBuilder();
    }

    /**
     * Get the response parser class for this query.
     *
     * @return ResponseParser
     */
    public function getResponseParser(): ResponseParser
    {
        return new ResponseParser();
    }
}