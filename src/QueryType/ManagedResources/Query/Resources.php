<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\ManagedResources\Query;

use Solarium\Core\Client\Client;
use Solarium\Core\Query\AbstractQuery as BaseQuery;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\QueryType\ManagedResources\RequestBuilder\Resources as RequestBuilder;
use Solarium\QueryType\ManagedResources\ResponseParser\Resources as ResponseParser;
use Solarium\QueryType\ManagedResources\Result\Resources\ResourceList;

/**
 * Resources.
 */
class Resources extends BaseQuery
{
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
     * Fixed name for resources.
     *
     * @var string
     */
    private $name = 'resources';

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
     * @return \Solarium\QueryType\ManagedResources\RequestBuilder\Resources
     */
    public function getRequestBuilder(): RequestBuilderInterface
    {
        return new RequestBuilder();
    }

    /**
     * Get the response parser class for this query.
     *
     * @return \Solarium\QueryType\ManagedResources\ResponseParser\Resources
     */
    public function getResponseParser(): ResponseParserInterface
    {
        return new ResponseParser();
    }
}
