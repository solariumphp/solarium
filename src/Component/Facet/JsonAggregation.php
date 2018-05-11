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
    public function setFunction(string $function)
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
     * Set the min value.
     *
     * This overwrites the current value.
     * This option doesn't exist in Solr originally, but it's useful to filter
     * the aggregations returned by Solr.
     *
     * @param int $min
     *
     * @return self Provides fluent interface
     */
    public function setMin(int $min)
    {
        return $this->setOption('min', $min);
    }

    /**
     * Get the min value.
     *
     * This option doesn't exist in Solr originally, but it's useful to filter
     * the aggregations returned by Solr.
     *
     * @return int
     */
    public function getMin()
    {
        return $this->getOption('min');
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
