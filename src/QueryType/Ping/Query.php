<?php

namespace Solarium\QueryType\Ping;

use Solarium\Core\Client\Client;
use Solarium\Core\Query\AbstractQuery as BaseQuery;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;

/**
 * Ping query.
 *
 * Use a ping query to test Solr communication.
 * A ping query has only two options, the path to use and the resultclass. See
 * {@link Solarium\Query} for the methods to set these options.
 */
class Query extends BaseQuery
{
    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'resultclass' => Result::class,
        'handler' => 'admin/ping',
        'omitheader' => true,
    ];

    /**
     * Get type for this query.
     *
     * @return string
     */
    public function getType(): string
    {
        return Client::QUERY_PING;
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
     * The ping query has no response parser so we return a null value.
     */
    public function getResponseParser(): ?ResponseParserInterface
    {
        return null;
    }
}
