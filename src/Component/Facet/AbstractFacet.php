<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Facet;

use Solarium\Core\Configurable;
use Solarium\Core\Query\LocalParameters\LocalParametersTrait;

/**
 * Facet base class.
 *
 * @see https://solr.apache.org/guide/faceting.html
 */
abstract class AbstractFacet extends Configurable implements FacetInterface
{
    use LocalParametersTrait;

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
        return $this->getLocalParameters()->getKeys()[0] ?? null;
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
        $this->getLocalParameters()->setKey($key);

        return $this;
    }
}
