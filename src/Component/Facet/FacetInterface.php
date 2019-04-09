<?php

namespace Solarium\Component\Facet;

/**
 * Facet interface.
 *
 * @see http://wiki.apache.org/solr/SimpleFacetParameters
 */
interface FacetInterface
{
    /**
     * Must be implemented by the facet types and return one of the constants.
     *
     * @abstract
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Get key.
     *
     * @return string|null
     */
    public function getKey(): ?string;

    /**
     * Set key.
     *
     * @param string $key
     *
     * @return self
     */
    public function setKey(string $key): self;
}
