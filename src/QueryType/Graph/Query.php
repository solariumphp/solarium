<?php

namespace Solarium\QueryType\Graph;

use Solarium\Core\Client\Client;
use Solarium\Core\Query\AbstractQuery;
use Solarium\QueryType\Stream\RequestBuilder;

/**
 * Graph query.
 */
class Query extends AbstractQuery
{
    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'handler' => 'graph',
        'resultclass' => 'Solarium\QueryType\Graph\Result',
    ];

    /**
     * Get type for this query.
     *
     * @return string
     */
    public function getType()
    {
        return Client::QUERY_GRAPH;
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
     * No response parser required since we pass through GraphML.
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
