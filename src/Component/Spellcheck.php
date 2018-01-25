<?php

namespace Solarium\Component;

use Solarium\Component\ComponentTraits\SpellcheckTrait;
use Solarium\Component\RequestBuilder\Spellcheck as RequestBuilder;
use Solarium\Component\ResponseParser\Spellcheck as ResponseParser;

/**
 * Spellcheck component.
 *
 * @see http://wiki.apache.org/solr/SpellcheckComponent
 */
class Spellcheck extends AbstractComponent implements SpellcheckInterface
{
    use SpellcheckTrait;

    /**
     * Get component type.
     *
     * @return string
     */
    public function getType()
    {
        return ComponentAwareQueryInterface::COMPONENT_SPELLCHECK;
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
     * Set spellcheck query option.
     *
     * @param string $query
     * @param array  $bind  Bind values for placeholders in the query string
     *
     * @return self Provides fluent interface
     */
    public function setQuery($query, $bind = null)
    {
        if (null !== $bind) {
            $query = $this->getQueryInstance()->getHelper()->assemble($query, $bind);
        }

        return $this->setOption('query', trim($query));
    }
}
