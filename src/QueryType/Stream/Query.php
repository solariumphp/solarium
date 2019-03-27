<?php

namespace Solarium\QueryType\Stream;

use Solarium\Core\Client\Client;
use Solarium\Core\Query\AbstractQuery;

/**
 * Stream query.
 */
class Query extends AbstractQuery
{
    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'handler' => 'stream',
        'resultclass' => 'Solarium\QueryType\Stream\Result',
        'documentclass' => 'Solarium\QueryType\Select\Result\Document',
    ];

    /**
     * Get type for this query.
     *
     * @return string
     */
    public function getType(): string
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
     * Get a response parser for this query.
     *
     * @return ResponseParser
     */
    public function getResponseParser()
    {
        return new ResponseParser();
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

    /**
     * Set a custom document class.
     *
     * This class should implement the document interface
     *
     * @param string $value classname
     *
     * @return self Provides fluent interface
     */
    public function setDocumentClass($value)
    {
        return $this->setOption('documentclass', $value);
    }

    /**
     * Get the current documentclass option.
     *
     * The value is a classname, not an instance
     *
     * @return string
     */
    public function getDocumentClass()
    {
        return $this->getOption('documentclass');
    }
}
