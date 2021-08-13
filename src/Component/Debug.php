<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component;

use Solarium\Component\RequestBuilder\ComponentRequestBuilderInterface;
use Solarium\Component\RequestBuilder\Debug as RequestBuilder;
use Solarium\Component\ResponseParser\ComponentParserInterface;
use Solarium\Component\ResponseParser\Debug as ResponseParser;

/**
 * Debug component.
 *
 * @see https://solr.apache.org/guide/common-query-parameters.html#debug-parameter
 */
class Debug extends AbstractComponent
{
    /**
     * Get component type.
     *
     * @return string
     */
    public function getType(): string
    {
        return ComponentAwareQueryInterface::COMPONENT_DEBUG;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder(): ComponentRequestBuilderInterface
    {
        return new RequestBuilder();
    }

    /**
     * Get a response parser for this query.
     *
     * @return ResponseParser
     */
    public function getResponseParser(): ?ComponentParserInterface
    {
        return new ResponseParser();
    }

    /**
     * Get the explainOther option.
     *
     * @return string|null
     */
    public function getExplainOther(): ?string
    {
        return $this->getOption('explainother');
    }

    /**
     * Set the explainOther query.
     *
     * @see https://solr.apache.org/guide/common-query-parameters.html#explainother-parameter
     *
     * @param string $query
     *
     * @return self Provides fluent interface
     */
    public function setExplainOther(string $query): self
    {
        $this->setOption('explainother', $query);

        return $this;
    }
}
