<?php

namespace Solarium\Component\Facet;

use Solarium\Component\FacetSetInterface;

/**
 * JSON facet aggregation.
 *
 * @see https://lucene.apache.org/solr/guide/7_3/json-facet-api.html
 */
class JsonAggregation extends AbstractFacet implements JsonFacetInterface
{
    /**
     * Get the facet type.
     *
     * @return string
     */
    public function getType()
    {
        return FacetSetInterface::JSON_FACET_AGGREGATION;
    }

    /**
     * Set the function string.
     *
     * This overwrites the current value
     *
     * @param string $function
     *
     * @return self Provides fluent interface
     */
    public function setFunction($function)
    {
        return $this->setOption('function', $function);
    }

    /**
     * Get the function string.
     *
     * @return string
     */
    public function getFunction()
    {
        return $this->getOption('function');
    }

    /**
     * Serializes nested facets as option "facet" and returns that array structure.
     *
     * @return array|string
     */
    public function serialize()
    {
        return $this->getFunction();
    }

}
