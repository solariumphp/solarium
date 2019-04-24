<?php

namespace Solarium\Component;

use Solarium\Component\ComponentTraits\SuggesterTrait;
use Solarium\Component\RequestBuilder\ComponentRequestBuilderInterface;
use Solarium\Component\RequestBuilder\Suggester as RequestBuilder;
use Solarium\Component\ResponseParser\ComponentParserInterface;
use Solarium\Component\ResponseParser\Suggester as ResponseParser;

/**
 * Spellcheck component.
 *
 * @see http://wiki.apache.org/solr/SpellcheckComponent
 */
class Suggester extends AbstractComponent implements SuggesterInterface, QueryInterface
{
    use SuggesterTrait;
    use QueryTrait;

    /**
     * Get component type.
     *
     * @return string
     */
    public function getType(): string
    {
        return ComponentAwareQueryInterface::COMPONENT_SUGGESTER;
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
