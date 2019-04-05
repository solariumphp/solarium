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
     * Get key.
     *
     * @return string|null
     */
    public function getKey(): ?string
    {
        return $this->getOption('key');
    }

    /**
     * Set key.
     *
     * @param string $key
     *
     * @return self Provides fluent interface
     */
    public function setKey(string $key): FacetInterface
    {
        $this->setOption('key', $key);
        return $this;
    }
}
