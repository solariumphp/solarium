<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

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
        'omitheader' => false,
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
     * Get a responseparser for this query.
     *
     * @return ResponseParser
     */
    public function getResponseParser(): ?ResponseParserInterface
    {
        return new ResponseParser();
    }
}
