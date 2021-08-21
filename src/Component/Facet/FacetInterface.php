<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Facet;

/**
 * Facet interface.
 *
 * @see https://solr.apache.org/guide/faceting.html
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
