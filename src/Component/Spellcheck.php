<?php

namespace Solarium\Component;

use Solarium\Component\ComponentTraits\SpellcheckTrait;
use Solarium\Component\RequestBuilder\ComponentRequestBuilderInterface;
use Solarium\Component\RequestBuilder\Spellcheck as RequestBuilder;
use Solarium\Component\ResponseParser\ComponentParserInterface;
use Solarium\Component\ResponseParser\Spellcheck as ResponseParser;

/**
 * Spellcheck component.
 *
 * @see http://wiki.apache.org/solr/SpellcheckComponent
 */
class Spellcheck extends AbstractComponent implements SpellcheckInterface, QueryInterface
{
    use SpellcheckTrait;
    use QueryTrait;

    /**
     * Get component type.
     *
     * @return string
     */
    public function getType(): string
    {
        return ComponentAwareQueryInterface::COMPONENT_SPELLCHECK;
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
