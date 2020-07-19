<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component;

use Solarium\Component\ComponentTraits\TermsTrait;
use Solarium\Component\RequestBuilder\ComponentRequestBuilderInterface;
use Solarium\Component\RequestBuilder\Terms as RequestBuilder;
use Solarium\Component\ResponseParser\ComponentParserInterface;
use Solarium\Component\ResponseParser\Terms as ResponseParser;

/**
 * Terms component.
 *
 * A terms query provides access to the indexed terms in a field and the number of documents that match each term.
 * This can be useful for doing auto-suggest or other things that operate at the term level instead of the search
 * or document level. Retrieving terms in index order is very fast since the implementation directly uses Lucene's
 * TermEnum to iterate over the term dictionary.
 */
class Terms extends AbstractComponent implements TermsInterface
{
    use TermsTrait;

    /**
     * Get component type.
     *
     * @return string
     */
    public function getType(): string
    {
        return ComponentAwareQueryInterface::COMPONENT_TERMS;
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
}
