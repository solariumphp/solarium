<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Facet;

use Solarium\Component\FacetSetInterface;

/**
 * JSON facet aggregation.
 *
 * @see https://solr.apache.org/guide/json-facet-api.html#stat-facet-example
 * @see https://solr.apache.org/guide/json-facet-api.html#stat-facet-functions
 */
class JsonAggregation extends AbstractFacet implements JsonFacetInterface
{
    /**
     * Get the facet type.
     *
     * @return string
     */
    public function getType(): string
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
    public function setFunction(string $function): self
    {
        $this->setOption('function', $function);

        return $this;
    }

    /**
     * Get the function string.
     *
     * @return string|null
     */
    public function getFunction(): ?string
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
    public function setMin(int $min): self
    {
        $this->setOption('min', $min);

        return $this;
    }

    /**
     * Get the min value.
     *
     * This option doesn't exist in Solr originally, but it's useful to filter
     * the aggregations returned by Solr.
     *
     * @return int|null
     */
    public function getMin(): ?int
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
