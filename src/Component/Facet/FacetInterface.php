<?php

namespace Solarium\Component\Facet;

/**
 * Facet base class.
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
    public function getType();

    /**
     * Get key value.
     *
     * @return string
     */
    public function getKey();

    /**
     * Set key value.
     *
     * @param string $value
     *
     * @return FacetInterface
     */
    public function setKey($value);
}
