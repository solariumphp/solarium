<?php

namespace Solarium\QueryType\Stream;

use Solarium\Core\Client\Client;
use Solarium\Core\Query\AbstractQuery as BaseQuery;

/**
 * Stream query.
 **/
class Query extends BaseQuery
{
    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'resultclass' => 'Solarium\QueryType\Stream\Result',
        'handler' => 'stream',
    ];

    /**
     * Get type for this query.
     *
     * @return string
     */
    public function getType()
    {
        return Client::QUERY_STREAM;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder()
    {
        return new RequestBuilder();
    }

    /**
     * The stream query has no response parser so we return a null value.
     */
    public function getResponseParser()
    {
    }

    /**
     * Set the expression.
     *
     * @param string $expr
     *
     * @return self Provides fluent interface
     */
    public function setExpression($expr)
    {
        return $this->setOption('expr', $expr);
    }

    /**
     * Get the expression.
     *
     * @return string
     */
    public function getExpression()
    {
        return $this->getOption('expr');
    }

}
