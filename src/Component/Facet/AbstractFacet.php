<?php

namespace Solarium\Component\Facet;

use Solarium\Core\Configurable;

/**
 * Facet base class.
 *
 * @see http://wiki.apache.org/solr/SimpleFacetParameters
 */
abstract class AbstractFacet extends Configurable implements FacetInterface
{
    /**
     * Must be implemented by the facet types and return one of the constants.
     *
     * @abstract
     *
     * @return string
     */
    abstract public function getType(): string;

    /**
     * Get key value.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->getOption('key');
    }

    /**
     * Set key value.
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setKey($value)
    {
        return $this->setOption('key', $value);
    }
}
